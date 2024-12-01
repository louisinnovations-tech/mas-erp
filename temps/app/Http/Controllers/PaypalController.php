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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    protected $invoiceData;
    private $_api_context;
    public $currancy;

    public function paymentConfig($userId)
    {

        $payment_setting = Utility::getCompanyPaymentSetting($userId);

        if ($payment_setting['paypal_mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }

    }

    public function PayWithPaypal(Request $request, $invoice_id)
    {
        try {
            $id = decrypt($invoice_id);
            $invoice = Bill::find($id);

            $this->invoiceData = $invoice;
            $this->paymentConfig($invoice->created_by);

            $settings = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');

            $payment_setting = DB::table('payment_settings')->get()->pluck('value', 'name')->toArray();

            $request->validate(['amount' => 'required|numeric|min:0']);

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));

            if ($invoice) {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $name = Utility::invoiceNumberFormat($settings, $invoice->id);

                $paypalToken = $provider->getAccessToken();

                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    "application_context" => [
                        "return_url" => route('get.payment.status', [$invoice->id, $request->amount]),
                        "cancel_url" => route('get.payment.status', [$invoice->id, $request->amount]),
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => $payment_setting['site_currency'],
                                "value" => $request->amount,
                            ],
                        ],
                    ],
                ]);

                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    foreach ($response['links'] as $links) {
                        if ($links['rel'] == 'approve') {
                            return redirect()->away($links['href']);
                        }
                    }
                    return redirect()
                        ->route('bills.show', encrypt($invoice->id))
                        ->with('error', 'Something went wrong.');
                } else {
                    return redirect()
                        ->route('bills.show', encrypt($invoice->id))
                        ->with('error', $response['message'] ?? 'Something went wrong.');
                }

            } else {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }

        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function GetPaymentStatus(Request $request, $invoice_id, $amount)
    {
        try {
            $invoice = Bill::find($invoice_id);

            $this->paymentConfig($invoice->created_by);

            $provider = new PayPalClient;

            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $payment_id = Session::get('paypal_payment_id');

            if (empty($request->PayerID || empty($request->token))) {
                return redirect()->back()->with('error', __('Payment failed'));
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $payment_setting = DB::table('payment_settings')->get()->pluck('value', 'name')->toArray();

            $payments = new BillPayment();
            $payments['bill_id'] = $invoice_id;
            $payments['date'] = date('Y-m-d');
            $payments['amount'] = $amount;
            $payments['method'] = __('PAYPAL');
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

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


}
