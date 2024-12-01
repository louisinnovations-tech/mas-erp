<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\UserCoupon;
use GuzzleHttp\Client;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class BenefitPaymentController extends Controller
{
    public $currancy;
 
    public function invoicePayWithbenefit(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Bill::find($invoice_id);
        $admin_payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

        $secret_key = $admin_payment_setting['benefit_secret_key'];

        $setting = \App\Models\Utility::settings();



        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        try {
            $get_amount = $request->amount;
            if ($invoice && $get_amount != 0) {

                if ($get_amount > $invoice->due_amount) {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                }

                $userData =
                    [
                        "amount" => $get_amount,
                        "currency" => !empty($admin_payment_setting['site_currency']) ? $admin_payment_setting['site_currency'] : 'USD',
                        "customer_initiated" => true,
                        "threeDSecure" => true,
                        "save_card" => false,
                        "description" => $invoice['invoice_id'],
                        "metadata" => ["udf1" => "Metadata 1"],
                        "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                        "receipt" => ["email" => true, "sms" => true],
                        "customer" => ["first_name" => $user['name'], "middle_name" => "", "last_name" => "", "email" => $user['email'], "phone" => ["country_code" => 965, "number" => 51234567]],
                        "source" => ["id" => "src_bh.benefit"],
                        "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                        "redirect" => ["url" => route('invoice.benefit.status', ['invoice_id' => $invoice_id, 'amount' => $get_amount])],

                    ];
                $responseData = json_encode($userData);
                $client = new Client();

                try {
                    $response = $client->request('POST', 'https://api.tap.company/v2/charges', [
                        'body' => $responseData,
                        'headers' => [
                            'Authorization' => 'Bearer ' . $secret_key,
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ],
                    ]);
                } catch (\Throwable $th) {
                    dd($th);
                    return redirect()->back()->with('error', $th);
                }

                $data = $response->getBody();
                $res = json_decode($data);
                return redirect($res->transaction->url);
            }
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->back()->with('error', __($e->getMessage()));
        }

    }
    public function getInvociePaymentStatus(Request $request, $invoice_id, $amount)
    {
        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;
        $admin_payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

        $secret_key = $admin_payment_setting['benefit_secret_key'];
        if ($invoice) {
            try {
                $post = $request->all();
                $client = new Client();
                $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $secret_key,
                        'accept' => 'application/json',
                    ],
                ]);

                $json = $response->getBody();
                $data = json_decode($json);
                $status_code = $data->gateway->response->code;
                if ($status_code == '00'){
                $invoice_payment                 = new BillPayment();
                $invoice_payment->bill_id     = $invoice_id;
                $invoice_payment->amount         = $amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->method   = 'Benefit';
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
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('ERROR', __('Transaction fail'));
                }
            }
            else {
                return redirect()->route('pay.invoice')->with('error', __('Your Transaction is fail please try again'));
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
