<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Catch_;

class IyziPayController extends Controller
{
    public $currancy,$invoiceData,$callBackUrl,$returnUrl;


    public function invoicepaywithiyzipay(Request $request){
        $invoice_id = $request->input('invoice_id');
        $invoice = Bill::find($invoice_id);
        $this->invoiceData  = $invoice;

        $get_amount = $request->amount;

        $user = User::where('id', $invoice->created_by)->first();


        if ($invoice) {

            if ($get_amount > $invoice->due_amount) {
                return redirect()
                    ->route('bills.show', \Crypt::encrypt($invoice->id))
                    ->with('error', 'Invalid amount.');
            }

            $user      = User::find($invoice->created_by);
            $PaymentSettings = Utility::getCompanyPaymentSetting($invoice->created_by);

            $iyzipay_key = $PaymentSettings['iyzipay_key'];
            $iyzipay_secret = $PaymentSettings['iyzipay_secret'];
            $iyzipay_mode = $PaymentSettings['iyzipay_mode'];

            $currency = $PaymentSettings['site_currency'];

            $res_data['total_price'] = $get_amount;
            // set your Iyzico API credentials

                // set your Iyzico API credentials
            try {
                $setBaseUrl = ($iyzipay_mode == 'local') ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com';
                $options = new \Iyzipay\Options();
                $options->setApiKey($iyzipay_key);
                $options->setSecretKey($iyzipay_secret);
                $options->setBaseUrl($setBaseUrl); // or "https://api.iyzipay.com" for production
                $ipAddress = Http::get('https://ipinfo.io/?callback=')->json();
                $address = ($user->address) ? $user->address : 'Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1';
                // create a new payment request
                $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
                $request->setLocale('en');
                $request->setPrice($res_data['total_price']);
                $request->setPaidPrice($res_data['total_price']);
                $request->setCurrency($currency);
                $request->setCallbackUrl(route('invoice.iyzipay.status',[$invoice->id,$get_amount]));
                $request->setEnabledInstallments(array(1));
                $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
                $buyer = new \Iyzipay\Model\Buyer();
                $buyer->setId($user->id);
                $buyer->setName(explode(' ', $user->name)[0]);
                $buyer->setSurname(explode(' ', $user->name)[0]);
                $buyer->setGsmNumber("+" . $user->dial_code . $user->phone);
                $buyer->setEmail($user->email);
                $buyer->setIdentityNumber(rand(0, 999999));
                $buyer->setLastLoginDate("2023-03-05 12:43:35");
                $buyer->setRegistrationDate("2023-04-21 15:12:09");
                $buyer->setRegistrationAddress($address);
                $buyer->setIp($ipAddress['ip']);
                $buyer->setCity($ipAddress['city']);
                $buyer->setCountry($ipAddress['country']);
                $buyer->setZipCode($ipAddress['postal']);
                $request->setBuyer($buyer);
                $shippingAddress = new \Iyzipay\Model\Address();
                $shippingAddress->setContactName($user->name);
                $shippingAddress->setCity($ipAddress['city']);
                $shippingAddress->setCountry($ipAddress['country']);
                $shippingAddress->setAddress($address);
                $shippingAddress->setZipCode($ipAddress['postal']);
                $request->setShippingAddress($shippingAddress);
                $billingAddress = new \Iyzipay\Model\Address();
                $billingAddress->setContactName($user->name);
                $billingAddress->setCity($ipAddress['city']);
                $billingAddress->setCountry($ipAddress['country']);
                $billingAddress->setAddress($address);
                $billingAddress->setZipCode($ipAddress['postal']);
                $request->setBillingAddress($billingAddress);
                $basketItems = array();
                $firstBasketItem = new \Iyzipay\Model\BasketItem();
                $firstBasketItem->setId("BI101");
                $firstBasketItem->setName("Binocular");
                $firstBasketItem->setCategory1("Collectibles");
                $firstBasketItem->setCategory2("Accessories");
                $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                $firstBasketItem->setPrice($res_data['total_price']);
                $basketItems[0] = $firstBasketItem;
                $request->setBasketItems($basketItems);

                $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);

                return redirect()->to($checkoutFormInitialize->getpaymentPageUrl());
            } catch (\Exception $e) {

                return redirect()->route('bills.show')->with('errors', $e->getMessage());
            }

        }
        else{
            return redirect()
            ->route('bills.show', \Crypt::encrypt($invoice->id))
            ->with('error', $response['message'] ?? 'Something went wrong.');
        }

        return redirect()->back()->with('error', __('Unknown error occurred'));
    }
    public function invoiceiyzipaystatus($invoice_id, $amount)  {

        $invoice = Bill::find($invoice_id);

        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        if ($invoice) {
            try {
                $invoice_payment                 = new BillPayment();
                $invoice_payment->bill_id     = $invoice_id;
                $invoice_payment->amount         = $amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->method   = 'IyziPay';
                $invoice_payment->save();

                $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $amount;
                }
                $invoice->save();
                if (Auth::user()) {
                    return redirect()->route('bills.show', $invoice_id)->with('success', __('Invoice paid Successfully!!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                } else {
                    $id = Crypt::encrypt($invoice_id);

                    return redirect()->route('pay.invoice', $id)->with('success', __('Invoice paid Successfully!!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
                }

                if (Auth::check()) {
                    return redirect()->route('invoices.show', $invoice_id['invoice_id'])->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id['invoice_id']))->with('ERROR', __('Transaction fail'));
                }
            } catch (\Exception $e) {

                if (Auth::check()) {
                    return redirect()->route('bills.show', $invoice_id['invoice_id'])->with('error', $e->getMessage());
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', $e->getMessage());
                }
            }
        }
            else {
                if (Auth::check()) {
                    return redirect()->route('invoices.show', $invoice_id['invoice_id'])->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id['invoice_id']))->with('success', __('Invoice not found.'));
                }
            }
    }

}


