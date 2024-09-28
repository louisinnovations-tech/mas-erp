<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use YooKassa\Client;

class YooKassaController extends Controller
{

    public function invoicePayWithYookassa(Request $request)
    {
        $invoice_id = $request->invoice_id;

        $invoice = Bill::find($invoice_id);
        $getAmount = $request->amount;

        $user = User::where('id', $invoice->created_by)->first();

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);

        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];
        $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'RUB';
        $get_amount = $request->amount;

        try {
            if ($invoice) {


                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $currency,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route('invoice.yookassa.status', [
                                    'invoice_id'=>$invoice->id,
                                    'amount'=>$get_amount
                                ]),
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );

                    Session::put('invoice_payment_id', $payment['id']);


                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }


                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->back()->with('error', __($e));
        }
    }
    public function getInvociePaymentStatus(Request $request)
    {
        $get_amount = $request->amount;

        $invoice = Bill::find($request->invoice_id);
        $user = User::where('id', $invoice->created_by)->first();

        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $yookassa_shop_id = $payment_setting['yookassa_shop_id'];
        $yookassa_secret_key = $payment_setting['yookassa_secret'];

        if ($invoice) {
            try {
                if (is_int((int)$yookassa_shop_id)) {
                    $client = new Client();
                    $client->setAuth((int)$yookassa_shop_id, $yookassa_secret_key);
                    $paymentId = Session::get('invoice_payment_id');

                    if ($paymentId == null) {
                        return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                    }
                    $payment = $client->getPaymentInfo($paymentId);

                    Session::forget('invoice_payment_id');

                    if (isset($payment) && $payment->status == "succeeded") {

                        $user = auth()->user();
                        try {
                            $invoice_payment                 = new BillPayment();
                            $invoice_payment->bill_id     = $request->invoice_id;
                            $invoice_payment->txn_id = app('App\Http\Controllers\BillController')->transactionNumber($user->id);
                            $invoice_payment->amount         = $get_amount;
                            $invoice_payment->date           = date('Y-m-d');
                            $invoice_payment->method   = 'Yookassa';
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
                    } else {
                        return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                    }
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
