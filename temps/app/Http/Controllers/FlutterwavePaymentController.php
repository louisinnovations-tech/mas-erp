<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\UserCoupon;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlutterwavePaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;

    public function paymentConfig()
    {

        $payment_setting = DB::table('payment_settings')->get()->pluck('value', 'name')->toArray();

        $this->secret_key = isset($payment_setting['flutterwave_secret_key']) ? $payment_setting['flutterwave_secret_key'] : '';
        $this->public_key = isset($payment_setting['flutterwave_public_key']) ? $payment_setting['flutterwave_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_flutterwave_enabled']) ? $payment_setting['is_flutterwave_enabled'] : 'off';

        return $this;
    }



    public function invoicePayWithFlutterwave(Request $request)
    {
        try {
            $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
            $invoice = Bill::find($invoiceID);

            if ($invoice) {
                $price = $request->amount;

                if ($price > 0) {

                    if (!empty($invoice->advocate)) {
                        $advocate = Advocate::find($invoice->advocate);
                        $email = $advocate->email;

                    } else {
                        $email = $invoice->custom_email;
                    }
                    $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

                    $res_data['email'] = $email;
                    $res_data['total_price'] = (int) $price;
                    $res_data['currency'] = !empty($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'USD';
                    $res_data['flag'] = 1;

                    return $res_data;
                } else {
                    $res['msg'] = __("Enter valid amount.");
                    $res['flag'] = 2;

                    return $res;
                }
            } else {
                return redirect()->route('bills.index')->with('error', __('Invoice is deleted.'));
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $pay_id)
    {
        try {
            $invoiceID = decrypt($invoice_id);

            $invoice = Bill::find($invoiceID);


            $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

            $this->secret_key = isset($payment_setting['flutterwave_secret_key']) ? $payment_setting['flutterwave_secret_key'] : '';
            $this->public_key = isset($payment_setting['flutterwave_public_key']) ? $payment_setting['flutterwave_public_key'] : '';
            $this->is_enabled = isset($payment_setting['is_flutterwave_enabled']) ? $payment_setting['is_flutterwave_enabled'] : 'off';


            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $result = [];
            if ($invoice) {
                $data = array(
                    'txref' => $pay_id,
                    'SECKEY' => $this->secret_key,
                );

                // make request to endpoint using unirest.
                $headers = array('Content-Type' => 'application/json');
                $body = \Unirest\Request\Body::json($data);
                $url = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

                // Make `POST` request and handle response with unirest
                $response = \Unirest\Request::post($url, $headers, $body);

                if (!empty($response)) {
                    $response = json_decode($response->raw_body, true);

                }

                $payments = new BillPayment();
                $payments['bill_id'] = $invoiceID;
                $payments['date'] = date('Y-m-d');
                $payments['amount'] = $request->amount;
                $payments['method'] = __('FLUTTERWAVE');
                $payments['order_id'] = $orderID;
                $payments['currency'] = $payment_setting['site_currency'];
                $payments['note'] = $invoice->description;
                $payments->save();

                $payment = BillPayment::where('bill_id', $invoiceID)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $request->amount;
                }


                $invoice->save();

                if (Auth::check()) {
                    return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                } else {
                    return redirect()->back()->with('success', __(' Payment successfully added.'));
                }

            } else {
                return redirect()->back()->with('error', __('Invoice is deleted.'));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }

    }

}
