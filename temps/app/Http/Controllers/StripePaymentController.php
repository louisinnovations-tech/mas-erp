<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Order;
use App\Models\User;
use App\Models\Utility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Charge;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    public $currancy;
    public $currancy_symbol;
    public $stripe_secret;
    public $stripe_key;

    public function planpaymentSetting()
    {
        $admin_payment_setting = Utility::payment_settings();
        $this->currancy_symbol = isset($admin_payment_setting['currency_symbol'])?$admin_payment_setting['currency_symbol']:'';
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        $this->stripe_secret = isset($admin_payment_setting['stripe_secret'])?$admin_payment_setting['stripe_secret']:'';
        $this->stripe_key = isset($admin_payment_setting['stripe_key'])?$admin_payment_setting['stripe_key']:'';
    }

    public function index()
    {
        if (Auth::user()->can('manage order')) {
            $objUser = Auth::user();
            if ($objUser->type == 'company') {
                $orders = Order::select(['orders.*','users.name as user_name',])
                        ->join('users', 'orders.user_id', '=', 'users.id')
                        ->orderBy('orders.created_at', 'DESC')
                        ->get();
            } else {
                $orders = Order::select(
                    [
                        'orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->get();
            }

            return view('order.index', compact('orders'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function addPayment(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $invoice = Bill::find($id);

            if (Auth::check()) {
                $user_id = Auth::user()->creatorId();
                $company_payment_setting = Utility::getCompanyPaymentSetting($user_id);
            } else {

                $user = User::where('id', $invoice->created_by)->first();
                $company_payment_setting = Utility::getCompanyPaymentSetting($user->id);
            }

            if ($invoice) {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $price = $request->amount;
                Stripe::setApiKey($company_payment_setting['stripe_secret']);

                $data = Charge::create(
                    [
                        "amount" => 100 * $price,
                        "currency" => !empty($company_payment_setting['site_currency']) ? $company_payment_setting['site_currency'] : 'INR',
                        "source" => $request->stripeToken,
                        "description" => 'Invoice - ' . isset($invoice->description) ? $invoice->description : '',
                        "metadata" => ["order_id" => $orderID],
                    ]

                );

                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {

                        $payments = new BillPayment();
                        $payments['bill_id'] = $id;
                        $payments['date'] = date('Y-m-d');
                        $payments['amount'] = $price;
                        $payments['method'] = __('STRIPE');
                        $payments['order_id'] = $orderID;
                        $payments['currency'] = $data['currency'];
                        $payments['txn_id'] = $data['balance_transaction'];
                        $payments['note'] = $invoice->description;
                        $payments->save();

                        $payment = BillPayment::where('bill_id',$id)->sum('amount');

                        if ($payment >= $invoice->total_amount) {
                            $invoice->status = 'PAID';
                            $invoice->due_amount = 0.00;
                        } else {
                            $invoice->status = 'Partialy Paid';
                            $invoice->due_amount = $invoice->due_amount - $price;
                        }

                        $invoice->save();

                    if (Auth::check()) {
                        return redirect()->route('bills.show', $invoice->id)->with('success', __('Payment successfully added'));
                    } else {
                        return redirect()->back()->with('success', __(' Payment successfully added.'));
                    }

                } else {
                    if (Auth::check()) {
                        return redirect()->route('bills.show', $invoice->id)->with('error', __('Transaction has been failed.'));
                    } else {
                        return redirect()->back()->with('success', __('Transaction succesfull'));
                    }
                }

            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


}
