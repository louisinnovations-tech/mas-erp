<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MidtransController extends Controller
{


    public function invoicePayWithMidtrans(Request $request)
    {
        $invoice_id = $request->invoice_id;

        $invoice = Bill::find($invoice_id);

        $getAmount = $request->amount;

        $user = User::where('id', $invoice->created_by)->first();

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);

        $midtrans_secret = $payment_setting['midtrans_secret'];
        $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'RUB';
        $get_amount = round($request->amount);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {
            if ($invoice) {

                 // Set your Merchant Server Key
                \Midtrans\Config::$serverKey = $midtrans_secret;
                // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                \Midtrans\Config::$isProduction = $payment_setting['midtrans_mode'] == 'local' ? false : true;
                // Set sanitization on (default)
                \Midtrans\Config::$isSanitized = true;
                // Set 3DS transaction for credit card to true
                \Midtrans\Config::$is3ds = true;

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $orderID,
                        'gross_amount' => $get_amount,
                    ),
                    'customer_details' => array(
                        'first_name' => Auth::user()->name ?? 'Test',
                        'last_name' => '',
                        'email' => Auth::user()->email ?? 'test@gmail.com',
                        'phone' => '8787878787',
                    ),
                );
                $snapToken = \Midtrans\Snap::getSnapToken($params);


                $data = [
                    'snap_token' => $snapToken,
                    'midtrans_secret' => $midtrans_secret,
                    'invoice_id'=>$invoice->id,
                    'amount'=>$get_amount,
                    'mode' => $payment_setting['midtrans_mode'],
                    'fallback_url' => 'invoice.midtrans.status'
                ];

                return view('midtras.payment', compact('data'));
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {

            return redirect()->back()->with('error', __($e));
        }
    }
    public function getInvociePaymentStatus(Request $request)
    {
        $get_amount = $request->amount;

        $invoice = Bill::find($request->invoice_id);
        $user = User::where('id', $invoice->created_by)->first();

        $response = json_decode($request->json, true);
        if ($invoice) {
            try {
                if (isset($response['status_code']) && $response['status_code'] == 200) {

                    $user = auth()->user();
                    try {
                        $invoice_payment                 = new BillPayment();
                        $invoice_payment->bill_id     = $request->invoice_id;
                        $invoice_payment->txn_id = app('App\Http\Controllers\BillController')->transactionNumber($user->id);
                        $invoice_payment->amount         = $get_amount;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->method   = 'Midtrans';
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
                            return redirect()->route('pay.invoice', Crypt::encrypt($request->invoice_id))->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', encrypt($request->invoice_id))->with('ERROR', __('Transaction fail'));
                        }

                    } catch (\Exception $e) {
                        return redirect()->route('pay.invoice')->with('error', __($e->getMessage()));
                    }

                }else{
                    return redirect()->back()->with('error', $response['status_message']);
                }
            } catch (\Exception $e) {
                if (Auth::check()) {
                    return redirect()->route('pay.invoice', $request->invoice_id)->with('error', $e->getMessage());
                } else {
                    return redirect()->route('pay.invoice', encrypt($request->invoice_id))->with('success', $e->getMessage());
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('pay.invoice', $request->invoice_id)->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', encrypt($request->invoice_id))->with('success', __('Invoice not found.'));
            }
        }
    }
}
