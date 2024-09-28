<?php

namespace App\Http\Controllers;

use App\Models\Advocate;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaystackPaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;
    public $currancy;

    public function paymentConfig()
    {

        $payment_setting = DB::table('payment_settings')->get()->pluck('value', 'name')->toArray();

        $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
        $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';

        return $this;
    }


    public function invoicePayWithPaystack(Request $request)
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

        }catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount, $pay_id)
    {
        try {
            $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
            $invoice = Bill::find($invoiceID);

            $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

            $this->secret_key = isset($payment_setting['paystack_secret_key']) ? $payment_setting['paystack_secret_key'] : '';
            $this->public_key = isset($payment_setting['paystack_public_key']) ? $payment_setting['paystack_public_key'] : '';
            $this->is_enabled = isset($payment_setting['is_paystack_enabled']) ? $payment_setting['is_paystack_enabled'] : 'off';



            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $result = [];
            if ($invoice) {
                $url = "https://api.paystack.co/transaction/verify/$pay_id";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    [
                        'Authorization: Bearer ' . $this->secret_key,
                    ]
                );
                $responce = curl_exec($ch);
                curl_close($ch);
                if ($responce) {
                    $result = json_decode($responce, true);
                }


                $payments = new BillPayment();
                $payments['bill_id'] = $invoiceID;
                $payments['date'] = date('Y-m-d');
                $payments['amount'] = $amount;
                $payments['method'] = __('PAYSTACK');
                $payments['order_id'] = $orderID;
                $payments['currency'] = $payment_setting['site_currency'];

                $payments['note'] = $invoice->description;
                $payments->save();


                $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $amount;
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
