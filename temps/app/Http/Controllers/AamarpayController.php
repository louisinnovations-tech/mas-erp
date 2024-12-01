<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillPayment;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AamarpayController extends Controller
{



    function redirect_to_merchant($url)
    {

        $token = csrf_token();
?>
        <html xmlns="http://www.w3.org/1999/xhtml">

        <head>
            <script type="text/javascript">
                function closethisasap() {
                    document.forms["redirectpost"].submit();
                }
            </script>
        </head>

        <body onLoad="closethisasap();">

            <form name="redirectpost" method="post" action="<?php echo 'https://sandbox.aamarpay.com/' . $url; ?>">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </body>

        </html>
<?php
        exit;
    }

 

    public function invoicePayWithaamarpay(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice = Bill::find($invoice_id);

        $user = User::where('id', $invoice->created_by)->first();
        $url = 'https://sandbox.aamarpay.com/request.php';

        $payment_setting = Utility::getCompanyPaymentSetting($invoice->created_by);

        $aamarpay_store_id = $payment_setting['aamarpay_store_id'];
        $aamarpay_signature_key = $payment_setting['aamarpay_signature_key'];
        $aamarpay_description = $payment_setting['aamarpay_description'];
        $currency = !empty($payment_setting['site_currency']) ? $payment_setting['site_currency'] : 'BDT';

        $invoice_id = $request->invoice_id;
        $invoice = Bill::find($invoice_id);

        if (\Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->created_by)->first();
        }
        $get_amount = $request->amount;

        if ($invoice && $get_amount != 0) {

            if ($get_amount > $invoice->due_amount) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            try {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $fields = array(
                    'store_id' => $aamarpay_store_id,
                    //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
                    'amount' => $get_amount,
                    //transaction amount
                    'payment_type' => '',
                    //no need to change
                    'currency' => $currency,
                    //currenct will be USD/BDT
                    'tran_id' => $orderID,
                    //transaction id must be unique from your end
                    'cus_name' => $user->name,
                    //customer name
                    'cus_email' => $user->email,
                    //customer email address
                    'cus_add1' => '',
                    //customer address
                    'cus_add2' => '',
                    //customer address
                    'cus_city' => '',
                    //customer city
                    'cus_state' => '',
                    //state
                    'cus_postcode' => '',
                    //postcode or zipcode
                    'cus_country' => '',
                    //country
                    'cus_phone' => '1234567890',
                    //customer phone number
                    'success_url' => route('invoice.aamarpay.status', Crypt::encrypt(['response' => 'success', 'invoice_id' => $invoice->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your success route
                    'fail_url' => route('invoice.aamarpay.status', Crypt::encrypt(['response' => 'failure', 'invoice_id' => $invoice->id, 'price' => $get_amount, 'order_id' => $orderID])),
                    //your fail route
                    'cancel_url' => route('invoice.aamarpay.status', Crypt::encrypt(['response' => 'cancel'])),
                    //your cancel url
                    'signature_key' => $aamarpay_signature_key,
                    'desc' => $aamarpay_description,
                ); //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key

                $fields_string = http_build_query($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
                curl_close($ch);
                $this->redirect_to_merchant($url_forward);
            } catch (\Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        }
    }

    public function getInvociePaymentStatus(Request $request, $data)
    {
        $data = Crypt::decrypt($data);

        $getAmount = $data['price'];
        $invoice_id = $data['invoice_id'];
        $invoice = Bill::find($invoice_id);
        $user = User::where('id', $invoice->created_by)->first();
        $objUser = $user;

        if ($invoice) {
            try {
                $invoice_payment                 = new BillPayment();
                $invoice_payment->bill_id     = $invoice_id;
                $invoice_payment->amount         = $getAmount;
                $invoice_payment->date           = date('Y-m-d');
                $invoice_payment->method   = 'Aamarpay';
                $invoice_payment->save();

                $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $getAmount;
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
        } else {
            if (Auth::check()) {
                return redirect()->route('pay.invoice', $invoice_id)->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice not found.'));
            }
        }
    }
}
