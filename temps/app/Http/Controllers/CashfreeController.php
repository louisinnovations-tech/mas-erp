<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;

use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\Shipping;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class CashfreeController extends Controller
{
    public function paymentConfig()
    {
        if (\Auth::check()) {
            $payment_setting = Utility::payment_settings();

            config(
                [
                    'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
                ]
            );
        }
    }
    public function paymentSetting($id)
    {
        $payment_setting = Utility::getCompanyPaymentSetting($id);
        config(
            [
                'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
            ]
        );
    }
  

    public function invoicePayWithcashfree(Request $request)
    {

        $invoice_id = $request->invoice_id;
        $invoice = Bill::find($invoice_id);
        $url = config('services.cashfree.url');
        $this->paymentSetting($invoice->created_by);
        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {

            if ($get_amount > $invoice->due_amount) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }

            $headers = array(
                "Content-Type: application/json",
                "x-api-version: 2022-01-01",
                "x-client-id: " . config('services.cashfree.key'),
                "x-client-secret: " . config('services.cashfree.secret')
            );
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $data = json_encode([
                'order_id' => $orderID,
                'order_amount' => $get_amount,
                "order_currency" => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                "customer_details" => [
                    "customer_id" => 'customer_' . $user->id,
                    "customer_name" => $user->name,
                    "customer_email" => $user->email,
                    "customer_phone" => '1234567890',
                ],
                "order_meta" => [
                    "return_url" => route('invoice.cashfree.status') . '?order_id={order_id}&invoice_id=' . $invoice_id . '&amount=' . $get_amount
                ]
            ]);
            try {

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $resp = curl_exec($curl);
                curl_close($curl);
                return redirect()->to(json_decode($resp)->payment_link);
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
            }
        }
    }

    public function getInvociePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice    = Bill::find($request->invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));

        $this->paymentSetting($invoice->created_by);
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
            'headers' => [
                'accept' => 'application/json',
                'x-api-version' => '2022-09-01',
                "x-client-id" => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret')
            ],
        ]);
        $respons = json_decode($response->getBody());
        if ($respons->order_id && $respons->cf_payment_id != NULL) {

            $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    'x-client-id' => config('services.cashfree.key'),
                    'x-client-secret' => config('services.cashfree.secret'),
                ],
            ]);
            $info = json_decode($response->getBody());
            try {

                $invoice_payment                 = new BillPayment();
                $invoice_payment->bill_id     = $invoice_id;
                $invoice_payment->amount         = $request->amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->method   = 'Cashfree';
                $invoice_payment->save();

                $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $request->amount;
                }
                $invoice->save();

                if (Auth::check()) {
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('ERROR', __('Transaction fail'));
                }
            } catch (\Exception $e) {

                if (Auth::check()) {
                    return redirect()->route('pay.invoice', $invoice_id)->with('error', $e->getMessage());
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', $e->getMessage());
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('pay.invoice', $invoice_id)->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice not found.'));
            }
        }
    }
}
