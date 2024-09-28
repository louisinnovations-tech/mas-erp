<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use Illuminate\Http\Request;
use App\Models\Utility;
use Paytabscom\Laravel_paytabs\Facades\paypage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Crypt;

class PaytabController extends Controller
{
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled, $invoiceData;


    public function paymentSetting($id)
    {
        $payment_setting = Utility::getCompanyPaymentSetting($id);
        config([
            'paytabs.profile_id' => isset($payment_setting['paytab_profile_id']) ? $payment_setting['paytab_profile_id'] : '',
            'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
            'paytabs.region' => isset($payment_setting['paytab_region']) ? $payment_setting['paytab_region'] : '',
            'paytabs.currency' => 'BHD',
        ]);
    }

    public function invoicePayWithpaytab(Request $request)
    {
        try {
            $invoice_id = $request->invoice_id;
            $invoice = Bill::find($invoice_id);
            $this->paymentSetting($invoice->created_by);

            if (\Auth::check()) {
                $user = Auth::user();
            } else {
                $user = User::where('id', $invoice->created_by)->first();
            }
            if ($user->type != 'owner') {
                $user = User::where('id', $user->created_by)->first();
            }

            $get_amount = $request->amount;

            if ($invoice && $get_amount != 0) {
                if ($get_amount > $invoice->due_amount) {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                } else {
                    $pay = paypage::sendPaymentCode('all')
                        ->sendTransaction('sale')
                        ->sendCart(1, $get_amount, 'invoice payment')
                        ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                        ->sendURLs(
                            route('invoice.paytab.status', ['success' => 1, 'data' => $request->all(), $invoice->id, 'amount' => $get_amount]),
                            route('invoice.paytab.status', ['success' => 0, 'data' => $request->all(), $invoice->id, 'amount' => $get_amount])
                        )
                        ->sendLanguage('en')
                        ->sendFramed($on = false)
                        ->create_pay_page();
                    return $pay;

                }
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function PaytabGetPaymentCallback(Request $request, $invoice_id, $amount)
    {
        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        if ($invoice) {
            try {
                $invoice_payment                 = new BillPayment();
                $invoice_payment->bill_id     = $invoice_id;
                $invoice_payment->amount         = $amount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->method   = 'PayTab';
                $invoice_payment->save();

                $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $amount;
                }
                $invoice->save();


                if (Auth::check()) {
                    return redirect()->route('pay.invoice', Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('ERROR', __('Transaction fail'));
                }
            } catch (\Exception $e) {

                if (Auth::check()) {
                    return redirect()->route('pay.invoice', $invoice_id)->with('error', $e->getMessage());
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', $e->getMessage());
                }
            }
        }
            else {
                if (Auth::check()) {
                    return redirect()->route('pay.invoice', $invoice_id)->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice not found.'));
                }
            }
    }

}
