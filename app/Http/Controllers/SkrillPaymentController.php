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
use Obydul\LaraSkrill\SkrillRequest;
use Obydul\LaraSkrill\SkrillClient;

class SkrillPaymentController extends Controller
{
    public $email;
    public $is_enabled;
    public $currancy;


    public function invoicePayWithSkrill(Request $request)
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

        $invoiceID = decrypt($request->invoice_id);
        $invoice = Bill::find($invoiceID);

        $data = Utility::getCompanyPaymentSetting($invoice->created_by);

        $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : '';
        $this->currancy = isset($data['currency']) ? $data['currency'] : 'USD';
        $this->email = isset($data['skrill_email']) ? $data['skrill_email'] : '';
        $this->is_enabled = isset($data['is_skrill_enabled']) ? $data['is_skrill_enabled'] : 'off';

        $tran_id = md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id');
        $skill = new SkrillRequest();
        $skill->pay_to_email = $this->email;
        $skill->return_url = route('invoice.skrill', [encrypt($invoice->id), 'tansaction_id=' . MD5($tran_id)]);
        $skill->cancel_url = route('invoice.skrill', encrypt($invoice->id));

        // create object instance of SkrillRequest
        if (!empty($invoice->advocate)) {
            $advocate = Advocate::find($invoice->advocate);
            $email = $advocate->email;

        } else {
            $email = $invoice->custom_email;
        }

        $skill->transaction_id = MD5($tran_id); // generate transaction id
        $skill->amount = $request->amount;
        $skill->currency = $this->currancy;
        $skill->language = 'EN';
        $skill->prepare_only = '1';
        $skill->merchant_fields = 'site_name, customer_email';
        $skill->site_name = env('APP_NAME');
        $skill->customer_email = $email;

        // create object instance of SkrillClient
        $client = new SkrillClient($skill);
        $sid = $client->generateSID(); //return SESSION ID

        // handle error
        $jsonSID = json_decode($sid);
        if ($jsonSID != null && $jsonSID->code == "BAD_REQUEST") {
            return redirect()->back()->with('error', $jsonSID->message);
        }

        // do the payment
        $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
        if ($tran_id) {
            $data = [
                'amount' => $request->amount,
                'trans_id' => MD5($request['transaction_id']),
                'currency' => $this->currancy,
            ];
            session()->put('skrill_data', $data);
        }

        return redirect($redirectUrl);
    }
    public function getInvoicePaymentStatus($invoice_id, Request $request)
    {

        if (!empty($invoice_id)) {
            $invoice_id = decrypt($invoice_id);
            $invoice = Bill::find($invoice_id);

            $data = Utility::getCompanyPaymentSetting($invoice->created_by);

            $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : '';
            $this->currancy = isset($data['currency']) ? $data['currency'] : 'USD';
            $this->email = isset($data['skrill_email']) ? $data['skrill_email'] : '';
            $this->is_enabled = isset($data['is_skrill_enabled']) ? $data['is_skrill_enabled'] : 'off';
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if ($invoice) {
                try {
                    if (session()->has('skrill_data') && $request->has('tansaction_id')) {
                        $get_data = session()->get('skrill_data');
                        $manual_payments = BillPayment::where('bill_id', $invoice->id)->first();


                            $payments = new BillPayment();
                            $payments['bill_id'] = $invoice->id;
                            $payments['date'] = date('Y-m-d');
                            $payments['amount'] = isset($get_data['amount']) ? $get_data['amount'] : 0;
                            $payments['method'] = __('SKRILL');
                            $payments['order_id'] = $orderID;
                            $payments['currency'] = $data['site_currency'];
                            $payments['status'] = __('PAID');
                            $payments['note'] = $invoice->description;
                            $payments['txn_id'] = $request->input('tansaction_id');
                            $payments->save();


                        $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                        if ($payment >= $invoice->total_amount) {
                            $invoice->status = 'PAID';
                            $invoice->due_amount = 0.00;
                        } else {
                            $invoice->status = 'Partialy Paid';
                            $invoice->due_amount = $invoice->due_amount - isset($get_data['amount']) ? $get_data['amount'] : 0;
                        }

                        $invoice->save();

                        session()->forget('skrill_data');

                        if (Auth::check()) {
                            return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                        } else {
                            return redirect()->back()->with('success', __(' Payment successfully added.'));
                        }

                    } else {
                       return redirect()->back()->with('error', __("Payment failed"));


                    }
                } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
            } else {

                    return redirect()->back()->with('success', __(' Bill not found.'));


            }
        } else {
            return redirect()->back()->with('success', __(' Bill not found.'));


        }
    }


}
