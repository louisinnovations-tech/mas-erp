<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Bill;
use App\Models\BillPayment;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BankTransferController extends Controller
{
    public $currancy,$invoiceData;

    public function destroy(order $order)
    {

        if ($order) {
            $order->delete();
            return redirect()->back()->with('success', __('Order Successfully Deleted.'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

    public function show(Order $order, $id)
    {
        $order = Order::find($id);
        $admin_payment_setting = Utility::payment_settings();

        return view('order.show', compact('order', 'admin_payment_setting'));
    }
 
    public function orderreject($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->payment_status = 'Rejected';
            $order->save();
            return redirect()->back()->with('success', __('Order Successfully Rejected'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }
    public function invoicePayWithbank(Request $request)
    {

        $invoice = Bill::find($request->invoice_id);
        $this->invoiceData = $invoice;

        $get_amount = $request->amount;
        $request->validate(['amount' => 'required|numeric|min:0']);
        if ($request->payment_receipt) {

            $validation = [
                'max:' . '20480',
            ];
            $image_size = $request->file('payment_receipt')->getSize();



                $dir = 'uploads/receipt/';
                $filenameWithExt = $request->file('payment_receipt')->getClientOriginalName();
                $path = Utility::upload_file($request, 'payment_receipt', $filenameWithExt, $dir, $validation);
                if ($path['flag'] == 1) {
                    $payment_receipt = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }

        }
        if ($invoice) {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $BankTransfer = BankTransfer::create(
                [
                    'invoice_id' => $invoice->id,
                    'order_id' => $orderID,
                    'amount' => $get_amount,
                    'status' => 'Pending',
                    'receipt' => !empty($payment_receipt) ? $payment_receipt : 0,
                    'date' => date('Y-m-d H:i:s'),
                    'created_by' => $invoice->created_by,
                    'type' => __('Invoice'),
                ]
            );

            return redirect()->back()->with('success', __('Payment Successfully Done'));
        }
    }

    public function invoicebankPaymentDestroy($id)
    {
        $payment_show = BankTransfer::find($id);

        $payment_show->delete();
        return redirect()->back()->with('success', __('Payment Successfully Deleted'));
    }

    public function bankpaymentshow(BankTransfer $banktransfer, $invoicepayment_id)
    {
        $banktransfer = BankTransfer::find($invoicepayment_id);

        $payment_setting = Utility::payment_settings();
        return view('bills.view', compact('banktransfer', 'payment_setting'));
    }

    public function invoicebankstatus(Request $request, $banktransfer_id)
    {

        $banktransfer = Banktransfer::find($banktransfer_id);
        if ($banktransfer) {
            $banktransfer->status = $request->status;

            $banktransfer->update();
            if ($request->status == 'Approval') {
                $banktransfer->status = 'Approved';
                $invoice_payment = new BillPayment();
                $invoice_payment->txn_id = $banktransfer->order_id;
                $invoice_payment->bill_id = $banktransfer->invoice_id;
                $invoice_payment->amount = $banktransfer->amount;
                $invoice_payment->date = date('Y-m-d');
                $invoice_payment->method = 'Bank transfer';
                $invoice_payment->note = '';
                $invoice_payment->reciept = ($banktransfer->receipt == '0') ? '-' : $banktransfer->receipt;
                $invoice_payment->save();

                $invoice = Bill::find($banktransfer->invoice_id);
                $payment = BillPayment::where('bill_id', $invoice->id)->sum('amount');

                if ($payment >= $invoice->total_amount) {
                    $invoice->status = 'PAID';
                    $invoice->due_amount = 0.00;
                } else {
                    $invoice->status = 'Partialy Paid';
                    $invoice->due_amount = $invoice->due_amount - $banktransfer->amount;
                }

                $invoice->save();
            }
            $banktransfer->delete();

            return redirect()->back()->with('success', __('Invoice payment successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}
