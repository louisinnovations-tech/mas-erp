<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToyyibpayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData;

    public function __construct()
    {

        $payment_setting = Utility::payment_settings();

        $this->secretKey = isset($payment_setting['toyyibpay_secret_key']) ? $payment_setting['toyyibpay_secret_key'] : '';
        $this->categoryCode = isset($payment_setting['category_code']) ? $payment_setting['category_code'] : '';
        $this->is_enabled = isset($payment_setting['is_toyyibpay_enabled']) ? $payment_setting['is_toyyibpay_enabled'] : 'off';
    }

    public function index()
    {
        return view('payment');
    }


    // invoice

    public function invoicepaywithtoyyibpay(Request $request)
    {
        $user = \Auth::user();

        $invoice_id = $request->input('invoice_id');
        $invoice = Bill::find($invoice_id);
        $this->invoiceData = $invoice;

        $get_amount = $request->amount;
        $user = User::where('id', $invoice->created_by)->first();

        if ($invoice) {

            if ($get_amount > $invoice->due_amount) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $name = isset($user->name) ? $user->name : 'public' . " - " . $invoice->invoice_id;

                $this->callBackUrl = route('invoice.toyyibpay.status', [$invoice->id, $get_amount]);
                $this->returnUrl = route('invoice.toyyibpay.status', [$invoice->id, $get_amount]);
            }

            $Date = date('d-m-Y');
            $ammount = $get_amount;
            $billExpiryDays = 3;
            $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
            $billContentEmail = "Thank you for purchasing our product!";

            $some_data = array(
                'userSecretKey' => $this->secretKey,
                'categoryCode' => $this->categoryCode,
                'billName' => "invoice",
                'billDescription' => "invoice",
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => 100 * $ammount,
                'billReturnUrl' => $this->returnUrl,
                'billCallbackUrl' => $this->callBackUrl,
                'billExternalReferenceNo' => 'AFR341DFI',
                'billTo' => $user->name,
                'billEmail' => $user->email,
                'billPhone' => '0000000000',
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => '0',
                'billContentEmail' => $billContentEmail,
                'billChargeToCustomer' => 1,
                'billExpiryDate' => $billExpiryDate,
                'billExpiryDays' => $billExpiryDays,
            );
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://toyyibpay.com/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $obj = json_decode($result);

            return redirect('https://toyyibpay.com/' . $obj[0]->BillCode);

            return redirect()->route('bills.show', \Crypt::encrypt($invoice_id))->back()->with('error', __('Unknown error occurred'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function invoicetoyyibpaystatus(Request $request, $invoice_id, $amount)
    {

        $invoice = Bill::find($invoice_id);
        $objUser = \Auth::user();
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        if (\Auth::check()) {

            $user = \Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();

        }

        $payment_id = \Session::get('toyyib_id');
        \Session::forget('toyyib_id');

        if (empty($request->PayerID || empty($request->token))) {
            return redirect()->route('bills.show', $invoice_id)->with('error', __('Payment failed'));
        }
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        try {

            if ($request->return_type == 'success') {
                $new = new BillPayment ();
                $new->bill_id = $invoice_id;
                $new->txn_id = '';
                $new->date = Date('Y-m-d');
                $new->amount = $amount;
                $new->note = '';
                $new->method = 'Toyyibpay';
                $new->save();

                $payment = BillPayment::where('bill_id', $invoice_id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $amount;
                }

                $invoice->save();
            } else {
                if (\Auth::check()) {
                    return redirect()->route('bills.show', $invoice_id)->with('error', __('Transaction fail'));
                } else {
                    return redirect()->redirect()->back()->with('error', __('Transaction fail'));
                }
            }

            if (Auth::user()) {
                return redirect()->route('bills.show', $invoice_id)->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            } else {
                $id = \Crypt::encrypt($invoice_id);
                return redirect()->route('pay.invoice', $id)->with('success', __('Invoice paid Successfully!') . ((isset($msg) ? '<br> <span class="text-danger">' . $msg . '</span>' : '')));
            }

        } catch (\Exception $e) {
            if (\Auth::check()) {
                return redirect()->route('pay.invoice', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
            } else {
                return redirect()->back()->with('success', __('Transaction has been complted.'));
            }
        }
    }
}
