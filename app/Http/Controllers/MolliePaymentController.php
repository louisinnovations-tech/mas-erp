<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mollie\Api\MollieApiClient;

class MolliePaymentController extends Controller
{
    public $api_key;
    public $profile_id;
    public $partner_id;
    public $is_enabled;
    public $currancy;


    public function invoicePayWithMollie(Request $request)
    {
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
        try {
            $invoiceID = decrypt($request->invoice_id);
            $invoice = Bill::find($invoiceID);

            $data = Utility::getCompanyPaymentSetting($invoice->created_by);

            $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : '';
            $this->api_key = isset($data['mollie_api_key']) ? $data['mollie_api_key'] : '';
            $this->profile_id = isset($data['mollie_profile_id']) ? $data['mollie_profile_id'] : '';
            $this->partner_id = isset($data['mollie_partner_id']) ? $data['mollie_partner_id'] : '';
            $this->is_enabled = isset($data['is_mollie_enabled']) ? $data['is_mollie_enabled'] : 'off';

            $mollie = new MollieApiClient();
            $mollie->setApiKey($this->api_key);
            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => $this->currancy,
                        "value" => number_format($request->amount, 2),
                    ],
                    "description" => "payment for invoice",
                    "redirectUrl" => route('invoice.mollie', encrypt($invoice->id)),
                ]
            );

            session()->put('mollie_payment_id', $payment->id);
            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        } catch (Exception $e) {

            return redirect()->back()->with('error',$e->getMessage());
        }

    }

    public function getInvoicePaymentStatus($invoice_id, Request $request)
    {
        try {

            $invoiceID = decrypt($invoice_id);
            $invoice = Bill::find($invoiceID);

            $data = Utility::getCompanyPaymentSetting($invoice->created_by);
            $manual_payments = BillPayment::where('bill_id', $invoice->id)->first();
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : '';
            $this->api_key = isset($data['mollie_api_key']) ? $data['mollie_api_key'] : '';
            $this->profile_id = isset($data['mollie_profile_id']) ? $data['mollie_profile_id'] : '';
            $this->partner_id = isset($data['mollie_partner_id']) ? $data['mollie_partner_id'] : '';
            $this->is_enabled = isset($data['is_mollie_enabled']) ? $data['is_mollie_enabled'] : 'off';

            $mollie = new \Mollie\Api\MollieApiClient ();
            $mollie->setApiKey($this->api_key);

            if ($invoice && session()->has('mollie_payment_id')) {
                $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                if ($payment->isPaid()) {

                        $payments = new BillPayment();
                        $payments['bill_id'] = $invoice->id;
                        $payments['date'] = date('Y-m-d');
                        $payments['amount'] = isset($payment->amount->value) ? $payment->amount->value : 0;
                        $payments['method'] = __('MOLLIE');
                        $payments['order_id'] = $orderID;
                        $payments['currency'] = $data['site_currency'];
                        $payments['note'] = $invoice->description;
                        $payments['txn_id'] = isset($payment->id) ? $payment->id : '';
                        $payments->save();


                    $payment = BillPayment::where('bill_id', $invoiceID)->sum('amount');

                    if ($payment >= $invoice->total_amount) {
                        $invoice->status = 'PAID';
                        $invoice->due_amount = 0.00;
                    } else {
                        $invoice->status = 'Partialy Paid';
                        $invoice->due_amount = $invoice->due_amount - isset($payment->amount->value) ? $payment->amount->value : 0;
                    }

                    $invoice->save();

                    if (Auth::check()) {
                        return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                    } else {
                        return redirect()->back()->with('success', __('Invoice paid Successfully!'));
                    }

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
