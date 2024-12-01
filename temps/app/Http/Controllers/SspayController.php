<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class SspayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData, $currency;

    public function __construct()
    {

        $payment_setting = Utility::payment_settings();

        $this->secretKey = isset($payment_setting['sspay_secret_key']) ? $payment_setting['sspay_secret_key'] : '';
        $this->categoryCode                = isset($payment_setting['sspay_category_code']) ? $payment_setting['sspay_category_code'] : '';
        $this->is_enabled          = isset($payment_setting['is_sspay_enabled']) ? $payment_setting['is_sspay_enabled'] : 'off';
        return $this;
    }



    public function invoicepaywithsspaypay(Request $request)
    {
        $invoice_id = $request->input('invoice_id');
        $invoice = Bill::find($invoice_id);
        $this->invoiceData = $invoice;

        $get_amount = $request->amount;

        $user = User::where('id', $invoice->created_by)->first();
        $payment_setting = Utility::getCompanyPaymentSetting($user->id);


        if ($invoice) {

            if ($get_amount > $invoice->due_amount) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }else{
                $this->callBackUrl = route('customer.sspay', [$invoice->id, $get_amount]);
                $this->returnUrl = route('customer.sspay', [$invoice->id, $get_amount]);
            }

            $Date = date('d-m-Y');
            $description = !empty($invoice->description) ?  $invoice->description : $invoice->title;
            $billName = $invoice->title;


            $billExpiryDays = 3;
            $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
            $billContentEmail = "Thank you for purchasing our product!";

            $some_data = array(
                'userSecretKey' => $payment_setting['sspay_secret_key'],
                'categoryCode' => $payment_setting['sspay_category_code'],
                'billName' => $billName,
                'billDescription' => $description,
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => 100 * $get_amount,
                'billReturnUrl' => $this->returnUrl,
                'billCallbackUrl' => $this->callBackUrl,
                'billExternalReferenceNo' => 'AFR341DFI',
                'billTo' => !empty($user->name) ? $user->name : '',
                'billEmail' => !empty($user->email) ? $user->email : '',
                'billPhone' => !empty($user->phone_no) ? $user->phone_no : '0000000000',
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => '0',
                'billContentEmail' => $billContentEmail,
                'billChargeToCustomer' => 1,
                'billExpiryDate' => $billExpiryDate,
                'billExpiryDays' => $billExpiryDays
            );

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://sspay.my/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $obj = json_decode($result);

            return redirect('https://sspay.my/' . $obj[0]->BillCode);
            return redirect()
                ->route('invoice.show', \Crypt::encrypt($invoice->id))
                ->with('error', 'Something went wrong.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount)
    {
        $invoice = Bill::find($invoice_id);
        $this->invoiceData = $invoice;

        try {

            if ($request->status_id == 3) {
                return redirect()->route('pay.invoice', Crypt::encrypt($invoice->id))->with('error', __('Your Transaction is fail please try again'));
            } else if ($request->status_id == 2) {
                return redirect()->route('pay.invoice', Crypt::encrypt($invoice->id))->with('error', __('Your Transaction on pending'));
            } else if ($request->status_id == 1) {

                if ($invoice->dueAmount() == 0) {
                    $invoice->status = 'Paid';
                } else {
                    $invoice->status = 'Partialy Paid';
                }
                $invoice->save();

                $invoice_payment                 = new BillPayment();
                $invoice_payment->bill_id     = $invoice_id;
                $invoice_payment->amount         = $amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->method   = 'Sspay';
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
                if (Auth::check()) {
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                } else {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }
            }
        } catch (\Exception $e) {
            if (Auth::check()) {
                return redirect()->route('invoices.show', $invoice_id)->with('error', $e->getMessage());
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', $e->getMessage());
            }
        }
    }
}
