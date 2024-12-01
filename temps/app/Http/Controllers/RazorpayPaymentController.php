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

class RazorpayPaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;
    public $pay_amount;



    public function invoicePayWithRazorpay(Request $request)
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

            $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : 'USD';
            $this->secret_key = isset($data['razorpay_secret_key']) ? $data['razorpay_secret_key'] : '';
            $this->public_key = isset($data['razorpay_public_key']) ? $data['razorpay_public_key'] : '';
            $this->is_enabled = isset($data['is_razorpay_enabled']) ? $data['is_razorpay_enabled'] : 'off';


            if (!empty($invoice->advocate)) {
                $advocate = Advocate::find($invoice->advocate);
                $email = $advocate->email;

            } else {
                $email = $invoice->custom_email;
            }

            $res_data['email'] = $email;
            $res_data['total_price'] = $request->amount;
            $res_data['currency'] = $this->currancy;
            $res_data['flag'] = 1;
            $res_data['invoice_id'] = $invoice->id;
            $request->session()->put('invoice_data', $res_data);
            $this->pay_amount = $request->amount;
            return $res_data;

        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }

    }

    public function getInvoicePaymentStatus($pay_id, $invoice_id, Request $request)
    {
        try {
            $invoice_id = decrypt($invoice_id);
            $invoice = Bill::find($invoice_id);


                $data = Utility::getCompanyPaymentSetting($invoice->created_by);

                $this->currancy = isset($data['site_currency']) ? $data['site_currency'] : 'USD';
                $this->secret_key = isset($data['razorpay_secret_key']) ? $data['razorpay_secret_key'] : '';
                $this->public_key = isset($data['razorpay_public_key']) ? $data['razorpay_public_key'] : '';
                $this->is_enabled = isset($data['is_razorpay_enabled']) ? $data['is_razorpay_enabled'] : 'off';


            $ch = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_USERPWD, $this->public_key . ':' . $this->secret_key); // Input your Razorpay Key Id and Secret Id here
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch));

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $invoice_data = $request->session()->get('invoice_data');

            if ($response->status == 'authorized') {

                    $payments = new BillPayment();
                    $payments['bill_id'] = $invoice->id;
                    $payments['date'] = date('Y-m-d');
                    $payments['amount'] = isset($invoice_data['total_price']) ? $invoice_data['total_price'] : 0;
                    $payments['method'] = __('RAZORPAY');
                    $payments['order_id'] = $orderID;
                    $payments['currency'] = $data['site_currency'];

                    $payments['note'] = $invoice->description;
                    $payments['txn_id'] = isset($response->id) ? $response->id : '';
                    $payments->save();


                $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - isset($invoice_data['total_price']) ? $invoice_data['total_price'] : 0;
                }

                $invoice->save();

                $request->session()->forget('invoice_data');

                if (Auth::check()) {
                    return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                } else {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }

            }

        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

}
