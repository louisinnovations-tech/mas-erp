<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\UserCoupon;
use App\Models\Utility;
use CoinGate\CoinGate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoingatePaymentController extends Controller
{
    public $mode;
    public $coingate_auth_token;
    public $is_enabled;
    public $currancy;

    public function invoicePayWithCoingate(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'required',
                'invoice_id' => 'required',

            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $invoiceID = decrypt($request->invoice_id);
        $invoice = Bill::find($invoiceID);
        $data =Utility::getCompanyPaymentSetting($invoice->created_by);

        $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : 'USD';
        $this->coingate_auth_token = isset($data['coingate_auth_token']) ? $data['coingate_auth_token'] : '';
        $this->mode = isset($data['coingate_mode']) ? $data['coingate_mode'] : 'off';
        $this->is_enabled = isset($data['is_coingate_enabled']) ? $data['is_coingate_enabled'] : 'off';

        CoinGate::config(
            array(
                'environment' => $this->mode,
                'auth_token' => $this->coingate_auth_token,
                'curlopt_ssl_verifypeer' => false,
            )
        );

        $post_params = array(
            'order_id' => time(),
            'price_amount' => $request->amount,
            'price_currency' => $this->currancy,
            'receive_currency' => $this->currancy,
            'callback_url' => route('invoice.coingate', [encrypt($invoice->id)]),
            'cancel_url' => route('invoice.coingate', [encrypt($invoice->id)]),
            'success_url' => route(
                'invoice.coingate',
                [
                    encrypt($invoice->id),
                    'success=true',
                ]
            ),
            'title' => 'Plan #' . time(),
        );
        $order = \CoinGate\Merchant\Order::create($post_params);
        if ($order) {
            $request->session()->put('invoice_data', $post_params);
            return redirect($order->payment_url);
        } else {
            return redirect()->back()->with('error', __('Opps something wren wrong.'));
        }
    }

    public function getInvoicePaymentStatus($invoice_id, Request $request)
    {
        if (!empty($invoice_id)) {
            $invoice_id = decrypt($invoice_id);
            $invoice = Bill::find($invoice_id);

            $data = Utility::getCompanyPaymentSetting($invoice->created_by);

            $invoice_data = $request->session()->get('invoice_data');
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($invoice && !empty($invoice_data)) {
                try {
                    if ($request->has('success') && $request->success == 'true') {



                            $payments = new BillPayment();
                            $payments['bill_id'] = $invoice->id;
                            $payments['date'] = date('Y-m-d');
                            $payments['amount'] = isset($invoice_data['price_amount']) ? $invoice_data['price_amount'] : 0;
                            $payments['method'] = __('COINGATE');
                            $payments['order_id'] = $orderID;
                            $payments['currency'] = $data['site_currency'];
                            $payments['note'] = $invoice->description;
                            $payments['txn_id'] = isset($request->transaction_id) ? $request->transaction_id : '';
                            $payments->save();


                        $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                        if ($payment >= $invoice->total_amount) {
                            $invoice->status = 'PAID';
                            $invoice->due_amount = 0.00;
                        } else {
                            $invoice->status = 'Partialy Paid';
                            $invoice->due_amount = $invoice->due_amount - isset($invoice_data['price_amount']) ? $invoice_data['price_amount'] : 0;
                        }

                        $invoice->save();

                        $request->session()->forget('invoice_data');
                        if (Auth::check()) {
                            return redirect()->route('invoices.show', $invoice_id)->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                        }
                    } else {
                        return redirect()->back()->with('error', __('Transaction fail.'));
                    }
                } catch (Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
            } else {
                return redirect()->back()->with('error', __('Transaction fail.'));

            }
        } else {
            return redirect()->back()->with('error', __('Transaction fail.'));
        }
    }

}
