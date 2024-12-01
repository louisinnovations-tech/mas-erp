<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PaytmWallet;

class PaytmPaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;


    public function invoicePayWithPaytm(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
                'amount' => 'required',
                'invoice_id' => 'required',
                'mobile' => 'required',
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

            config(
                [
                    'services.paytm-wallet.env' => isset($data['paytm_mode']) ? $data['paytm_mode'] : '',
                    'services.paytm-wallet.merchant_id' => isset($data['paytm_merchant_id']) ? $data['paytm_merchant_id'] : '',
                    'services.paytm-wallet.merchant_key' => isset($data['paytm_merchant_key']) ? $data['paytm_merchant_key'] : '',
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => isset($data['paytm_industry_type']) ? $data['paytm_industry_type'] : '',
                ]
            );
            if (!empty($invoice->advocate)) {
                $advocate = Advocate::find($invoice->advocate);
                $email = $advocate->email;
                $name = $advocate->name;

            } else {
                $email = $invoice->custom_email;
                $name = $invoice->custom_advocate;
            }

            $call_back = route('invoice.paytm', [encrypt($invoice->id)]);
            $payment = PaytmWallet::with('receive');
            $payment->prepare(
                [
                    'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                    'user' => $invoice->created_by,
                    'mobile_number' => $request->mobile,
                    'email' => $email,
                    'amount' => $request->amount,
                    'invoice_id' => $invoice->id,
                    'callback_url' => $call_back,
                ]
            );

            return $payment->receive();

        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }

    }

    public function getInvoicePaymentStatus($invoice_id, Request $request)
    {
        try {
            $invoice_id = decrypt($invoice_id);
            $invoice = Bill::find($invoice_id);

            if ($invoice) {
                $transaction = PaytmWallet::with('receive');
                $response = $transaction->response();

                if ($transaction->isSuccessful()) {

                    $manual_payments = BillPayment::where('bill_id', $invoice->id)->first();
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

                        $payments = new BillPayment();
                        $payments['bill_id'] = $invoice->id;
                        $payments['date'] = date('Y-m-d');
                        $payments['amount'] = $request->has('amount') ? $request->amount : 0;
                        $payments['method'] = __('PAYTM');
                        $payments['order_id'] = $orderID;
                        $payments['currency'] = $payment_setting['site_currency'];

                        $payments['note'] = $invoice->description;
                        $payments['txn_id'] = isset($request->TXNID) ? $request->TXNID : '';
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

            }
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }


}
