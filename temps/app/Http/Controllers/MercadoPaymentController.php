<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MercadoPaymentController extends Controller
{
    public $token;
    public $is_enabled;
    public $currancy;
    public $mode;


    public function invoicePayWithMercado(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(), [
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

            $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

            $this->token = isset($payment_setting['mercado_access_token']) ? $payment_setting['mercado_access_token'] : '';
            $this->mode = isset($payment_setting['mercado_mode']) ? $payment_setting['mercado_mode'] : '';
            $this->is_enabled = isset($payment_setting['is_mercado_enabled']) ? $payment_setting['is_mercado_enabled'] : 'off';
            $this->currancy = $payment_setting['site_currency'];

            $preference_data = array(
                "items" => array(
                    array(
                        "title" => "Invoice : " . $request->invoice_id,
                        "quantity" => 1,
                        "currency_id" => $this->currancy,
                        "unit_price" => (float) $request->amount,
                    ),
                ),
            );

            \MercadoPago\SDK::setAccessToken($this->token);

            $preference = new \MercadoPago\Preference ();

            $item = new \MercadoPago\Item ();
            $item->title = "Invoice : " . $request->invoice_id;
            $item->quantity = 1;
            $item->unit_price = (float) $request->amount;
            $preference->items = array($item);

            $success_url = route('invoice.mercado', [encrypt($invoice->id), 'amount' => (float) $request->amount, 'flag' => 'success']);
            $failure_url = route('invoice.mercado', [encrypt($invoice->id), 'flag' => 'failure']);
            $pending_url = route('invoice.mercado', [encrypt($invoice->id), 'flag' => 'pending']);
            $preference->back_urls = array(
                "success" => $success_url,
                "failure" => $failure_url,
                "pending" => $pending_url,
            );
            $preference->auto_return = "approved";
            $preference->save();

            $payer = new \MercadoPago\Payer ();

            if (!empty($invoice->advocate)) {
                $advocate = Advocate::find($invoice->advocate);
                $email = $advocate->email;
                $name = $advocate->name;

            } else {
                $email = $invoice->custom_email;
                $name = $invoice->custom_advocate;
            }

            $payer->name = $name;
            $payer->email = $email;
            $payer->address = array(
                "street_name" => '',
            );

            if ($this->mode == 'live') {
                $redirectUrl = $preference->init_point;
            } else {
                $redirectUrl = $preference->sandbox_init_point;
            }
            return redirect($redirectUrl);

        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function getInvoicePaymentStatus($invoice_id, Request $request)
    {
        try {
            $invoice_id = decrypt($invoice_id);
            $invoice = Bill::find($invoice_id);


            if ($request->status == 'approved' && $request->flag == 'success') {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

                    $payments = new BillPayment();
                    $payments['bill_id'] = $invoice->id;
                    $payments['date'] = date('Y-m-d');
                    $payments['amount'] = $request->has('amount') ? $request->amount : 0;
                    $payments['method'] = __('Mercado Pago');
                    $payments['order_id'] = $orderID;
                    $payments['currency'] = $payment_setting['site_currency'];
                    $payments['note'] = $invoice->description;
                    $payments['txn_id'] = $request->has('preference_id') ? $request->preference_id : '';
                    $payments->save();


                $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $request->has('amount') ? $request->amount : 0;
                }


                $invoice->save();


                if (Auth::check()) {
                    return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                } else {
                    return redirect()->back()->with('success', __('Invoice paid Successfully!'));
                }
            } else {
                if (Auth::check()) {
                    return redirect()->route('bills.show', $invoice_id)->with('error', __('Transaction fail'));
                } else {
                    return redirect()->back()->with('error', __('Transaction fail'));
                }
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

}
