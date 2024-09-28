<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Xendit\Xendit;

class XenditPaymentController extends Controller
{


    public function invoicePayWithXendit(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $get_amount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {
            if ($invoice) {
                $payment_setting = Utility::getCompanyPaymentSetting($user->id);
                $xendit_token = $payment_setting['xendit_token'];
                $xendit_api = $payment_setting['xendit_api'];
                $currency = isset($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'RUB';
                $response = ['orderId' => $orderID, 'user' => $user, 'get_amount' => $get_amount, 'invoice' => $invoice, 'currency' => $currency];
                Xendit::setApiKey($xendit_api);
                $params = [
                    'external_id' => $orderID,
                    'payer_email' => Auth::user()->email ?? 'Testuser@gmail.com',
                    'description' => 'Payment for order ' . $orderID,
                    'amount' => $get_amount,
                    'callback_url' =>  route('invoice.xendit.status'),
                    'success_redirect_url' => route('invoice.xendit.status', $response),
                ];

                $Xenditinvoice = \Xendit\Invoice::create($params);
                Session::put('invoicepay',$Xenditinvoice);
                return redirect($Xenditinvoice['invoice_url']);

            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }

    public function getInvociePaymentStatus(Request $request){
        $session = Session::get('invoicepay');
        $invoice = Bill::find($request->invoice);
        $user = User::where('id', $invoice->created_by)->first();
        $payment_setting = Utility::getCompanyPaymentSetting($user->id);
        $xendit_api = $payment_setting['xendit_api'];
        Xendit::setApiKey($xendit_api);
        $getInvoice = \Xendit\Invoice::retrieve($session['id']);

        if($getInvoice['status'] == 'PAID'){

            $invoice_payment                 = new BillPayment();
            $invoice_payment->bill_id     = $invoice->id;
            $invoice_payment->txn_id = app('App\Http\Controllers\BillController')->transactionNumber($user->id);
            $invoice_payment->amount         = $request->get_amount;
            $invoice_payment->date           = date('Y-m-d');
            $invoice_payment->method   = 'Xendit';
            $invoice_payment->save();

            $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

            if ($payment >= $invoice->total_amount) {
                $invoice->status = 'PAID';
                $invoice->due_amount = 0.00;
            } else {
                $invoice->status = 'Partialy Paid';
                $invoice->due_amount = $invoice->due_amount - $request->get_amount;
            }
            $invoice->save();
        }
        if (Auth::check()) {
            return redirect()->route('pay.invoice', $invoice->id)->with('success', __('Invoice paid Successfully!'));
        } else {
            return redirect()->route('pay.invoice', encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));
        }
    }
}
