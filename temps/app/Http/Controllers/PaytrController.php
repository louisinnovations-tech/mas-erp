<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PaytrController extends Controller
{

    public function invoicePayWithpaytr(Request $request)
    {
        $invoice_id = $request->invoice_id;

        $invoice = Bill::find($invoice_id);
        $getAmount = $request->amount;


        $user = User::where('id', $invoice->created_by)->first();

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $paytr_merchant_id = $payment_setting['paytr_merchant_id'];
        $paytr_merchant_key = $payment_setting['paytr_merchant_key'];
        $paytr_merchant_salt = $payment_setting['paytr_merchant_salt'];
        $currency =isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'USD';

        try {

            $merchant_id    = $paytr_merchant_id;
            $merchant_key   = $paytr_merchant_key;
            $merchant_salt  = $paytr_merchant_salt;
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));


            $email = $user->email;
            $payment_amount = $getAmount;
            $merchant_oid = $orderID;
            $user_name = $user->name;
            $user_address =  'no address';
            $user_phone = '0000000000';

            $user_basket = base64_encode(json_encode(array(
                array("Plan", $payment_amount, 1),
            )));

            if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }

            $user_ip = $ip;
            $timeout_limit = "30";
            $debug_on = 1;
            $test_mode = 0;
            $no_installment = 0;
            $max_installment = 0;
            $currency = $currency;
            $payment_amount = $payment_amount * 100;
            $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
            $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

            $request['orderID'] = $orderID;
            $request['invoice_id'] = $invoice_id;
            $request['amount'] = $getAmount;
            $request['payment_status'] = 'failed';
            $payment_failed = $request->all();
            $request['payment_status'] = 'success';
            $payment_success = $request->all();

            $post_vals = array(
                'merchant_id' => $merchant_id,
                'user_ip' => $user_ip,
                'merchant_oid' => $merchant_oid,
                'email' => $email,
                'payment_amount' => $payment_amount,
                'paytr_token' => $paytr_token,
                'user_basket' => $user_basket,
                'debug_on' => $debug_on,
                'no_installment' => $no_installment,
                'max_installment' => $max_installment,
                'user_name' => $user_name,
                'user_address' => $user_address,
                'user_phone' => $user_phone,
                'merchant_ok_url' => route('invoice.paytr.status', $payment_success),
                'merchant_fail_url' => route('invoice.paytr.status', $payment_failed),
                'timeout_limit' => $timeout_limit,
                'currency' => $currency,
                'test_mode' => $test_mode
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);


            $result = @curl_exec($ch);

            if (curl_errno($ch)) {
                die("PAYTR IFRAME connection error. err:" . curl_error($ch));
            }

            curl_close($ch);

            $result = json_decode($result, 1);

            if ($result['status'] == 'success') {
                $token = $result['token'];
            } else {
                return redirect()->back()->with('error', $result['reason']);
            }
            return view('paytr_payment.index', compact('token'));
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e));
        }
    }
    public function getInvociePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $get_amount = $request->amount;

        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();

        if ($invoice) {
            try {
                if ($request->payment_status == "success") {
                    $invoice_payment                 = new BillPayment();
                    $invoice_payment->bill_id     = $invoice_id;
                    $invoice_payment->txn_id = app('App\Http\Controllers\BillController')->transactionNumber($user->id);
                    $invoice_payment->amount         = $get_amount;
                    $invoice_payment->date           = date('Y-m-d');
                    $invoice_payment->method   = 'PayTR';
                    $invoice_payment->save();

                    $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

                    if ($payment >= $invoice->total_amount) {
                        $invoice->status = 'PAID';
                        $invoice->due_amount = 0.00;
                    } else {
                        $invoice->status = 'Partialy Paid';
                        $invoice->due_amount = $invoice->due_amount - $get_amount;
                    }
                    $invoice->save();

                    if (Auth::check()) {
                        return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                    } else {
                        return redirect()->route('pay.invoice', encrypt($invoice_id))->with('ERROR', __('Transaction fail'));
                    }
                } else {

                    if (Auth::check()) {
                        return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('error', __('Transaction fail!'));
                    } else {
                        return redirect()->route('pay.invoice', encrypt($invoice_id))->with('error', __('Transaction fail'));
                    }
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
