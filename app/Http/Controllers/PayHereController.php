<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lahirulhr\PayHere\PayHere;

class PayHereController extends Controller
{
    public $paymentSetting;
    public function __construct()
    {
        $paymentSetting = Utility::payment_settings();
        $config = [
            'payhere.api_endpoint' => $paymentSetting['payhere_mode'] === 'local'
                ? 'https://sandbox.payhere.lk/'
                : 'https://www.payhere.lk/',
        ];

        $config['payhere.merchant_id'] = $paymentSetting['merchant_id'] ?? '';
        $config['payhere.merchant_secret'] = $paymentSetting['merchant_secret'] ?? '';
        $config['payhere.app_secret'] = $paymentSetting['payhere_app_secret'] ?? '';
        $config['payhere.app_id'] = $paymentSetting['payhere_app_id'] ?? '';

        config($config);

        $this->paymentSetting = $paymentSetting;
    }


    public function invoicePayWithPayHere(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $get_amount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $authuser = Auth::user();

        try {
            if ($invoice) {
                $payment_setting = Utility::getCompanyPaymentSetting($user->id);
                $config = [
                    'payhere.api_endpoint' => $payment_setting['payhere_mode'] === 'local'
                        ? 'https://sandbox.payhere.lk/'
                        : 'https://www.payhere.lk/',
                ];

                $config['payhere.merchant_id'] = $payment_setting['merchant_id'] ?? '';
                $config['payhere.merchant_secret'] = $payment_setting['merchant_secret'] ?? '';
                $config['payhere.app_secret'] = $payment_setting['payhere_app_secret'] ?? '';
                $config['payhere.app_id'] = $payment_setting['payhere_app_id'] ?? '';

                config($config);

                $hash = strtoupper(
                    md5(
                        config('payhere.merchant_id') .
                            $orderID .
                            number_format($get_amount, 2, '.', '') .
                            'LKR' .
                            strtoupper(md5(config('payhere.merchant_secret')))
                    )
                );

                $data = [
                    'first_name' => $authuser->name ?? '',
                    'last_name' => '',
                    'email' => $authuser->email ?? "",
                    'address' => '',
                    'city' => '',
                    'country' => '',
                    'order_id' => $orderID,
                    'items' => 'Invoice',
                    'currency' => 'LKR',
                    'amount' => $get_amount,
                    'hash' => $hash,
                ];

                return PayHere::checkOut()
                    ->data($data)
                    ->successUrl(route('invoice.payhere.status', ['success' => 1, 'data' => $request->all(),'amount'=>$get_amount]))
                    ->failUrl(route('invoice.payhere.status', ['success' => 0, 'data' => $request->all()]))
                    ->renderView();

            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function invoiceGetPayHereStatus(Request $request){

        $invoice_id = $request->data['invoice_id'];
        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();

        if ($request->success == 1) {
            $info = PayHere::retrieve()
                ->orderId($request->order_id)
                ->submit();
                if ($info['data'][0]['order_id'] == $request->order_id) {
                    if ($info['data'][0]['status'] == "RECEIVED") {
                        $invoice_payment                 = new BillPayment();
                        $invoice_payment->bill_id     = $invoice->id;
                        $invoice_payment->txn_id = app('App\Http\Controllers\BillController')->transactionNumber($user->id);
                        $invoice_payment->amount         = $request->amount;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->method   = 'Xendit';
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
                            return redirect()->route('pay.invoice', $invoice->id)->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
                        }
                    }
                }
        }else{
            if (Auth::check()) {
                return redirect()->route('pay.invoice', $invoice->id)->with('success', __('Oops! Something went wrong!'));
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Oops! Something went wrong!'));
            }

        }



    }
}
