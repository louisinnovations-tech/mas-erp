<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;

use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentWallController extends Controller
{
    public $currancy;
    public $secret_key;
    public $public_key;
    public $is_enabled;

    public function invoicePayWithPaymentwall(Request $request)
    {
        $data = $request->all();
        $invoice_id = decrypt($data['invoice_id']);

        $invoice = Bill::find($invoice_id);

        $admin_payment_setting = $this->paymentSetting($invoice->created_by);

        return view('bills.paymentwall', compact('data', 'admin_payment_setting'));
    }

    public function paymentSetting($id)
    {
        $payment_setting = Utility::getCompanyPaymentSetting($id);

        $this->currancy = isset($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
        $this->secret_key = isset($payment_setting['paymentwall_private_key']) ? $payment_setting['paymentwall_private_key'] : '';
        $this->public_key = isset($payment_setting['paymentwall_public_key']) ? $payment_setting['paymentwall_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled']) ? $payment_setting['is_paymentwall_enabled'] : 'off';
        return $this;
    }

    public function getInvoicePaymentStatus($invoice_id, Request $request)
    {
        if (!empty($invoice_id)) {

            $invoice    = Bill::find($invoice_id);
            $data = Utility::getCompanyPaymentSetting($invoice->created_by);

            if ($invoice) {

                \Paymentwall_Config::getInstance()->set(array(
                    'private_key' => $this->secret_key
                ));

                $parameters = $request->all();

                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => isset($request->amount) ? $request->amount : 0,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(), true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() and empty($responseData['secure'])) {

                    if ($charge->isCaptured()) {

                        $new = new BillPayment();
                        $new->bill_id = $invoice_id;
                        $new->txn_id = '';
                        $new->date = Date('Y-m-d');
                        $new->amount = isset($request->amount) ? $request->amount : 0;
                        $new->description = '';
                        $new->payment_method = 'Paymentwall';
                        $new->save();

                        $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                        if ($payment >= $invoice->total_amount) {
                            $invoice->status = 'PAID';
                            $invoice->due_amount = 0.00;
                        } else {
                            $invoice->status = 'Partialy Paid';
                            $invoice->due_amount = $invoice->due_amount-isset($payment->amount->value) ? $payment->amount->value : 0;
                        }

                        $invoice->save();



                        if (Auth::check()) {
                            return redirect()->route('bills.show', $invoice_id)->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                        }
                    } elseif ($charge->isUnderReview()) {
                        $res['invoice'] = $invoice_id;
                        $res['flag'] = 2;
                        return $res;
                    }
                } else {
                    $errors = json_decode($response, true);
                    $res['invoice'] = $invoice_id;
                    $res['flag'] = 2;
                    return $res;
                }
            } else {
                if (Auth::check()) {
                    return redirect()->route('bills.show', $invoice_id)->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice not found.'));
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('bills.index', $invoice_id)->with('error', __('Oops something went wrong.'));
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Oops something went wrong.'));
            }
        }
    }


     public function invoiceerror(Request $request, $flag, $invoice_id)
    {

        if (Auth::check()) {
            if ($flag == 1) {
                return redirect()->route('bills.index')->with('success', __('Payment added Successfully'));
            } else {
                return redirect()->route('bills.index')->with('error', __('Transaction has been failed! '));
            }
        } else {
            if ($flag == 1) {
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Payment added Successfully '));
            } else {
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Transaction has been failed! '));
            }
        }
    }
}
