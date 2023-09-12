<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\canceledOrderDataTable;
use App\DataTables\deliveredOrderDataTable;
use App\DataTables\droppedOffOrderDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\outForDeliveryOrderDataTable;
use App\DataTables\PendingOrderDataTable;
use App\DataTables\processedOrderDataTable;
use App\DataTables\shippedOrderDataTable;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(OrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.index');
    }

    public function pendingOrders(PendingOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.pending-order');
    }

    public function processedOrders(processedOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.pending-order');
    }

    public function droppedOfOrders(droppedOffOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.dropped-off-order');
    }

    public function shippedOrders(shippedOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.shipped-order');
    }

    public function outForDeliveryOrders(outForDeliveryOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.out-for-delivery-order');
    }

    public function deliveredOrders(deliveredOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.delivered-order');
    }

    public function canceledOrders(canceledOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.order.canceled-order');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::findOrFail($id);
        return view('admin.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        // delete order products
        $order->orderProducts()->delete();
        // delete transaction
        $order->transaction()->delete();

        $order->delete();

        return response(['status' => 'success', 'message' => 'Deleted successfully!']);
    }

    public function changeOrderStatus(Request $request)
    {
        $order = Order::findOrFail($request->id);
        if ($request->status == 'canceled') {
            if ($order->transaction) {
                $order->transaction->delete();
            }
        }

        $order->order_status = $request->status;
        $order->save();

        return response(['status' => 'success', 'message' => 'Updated Order Status']);
    }

    public function changePaymentStatus(Request $request)
    {
        $paymentStatus = Order::findOrFail($request->id);
        if ($request->status == 1) {
            $transaction = new Transaction();
            $transaction->order_id = $paymentStatus->id;
            $transaction->transaction_id = \Str::random(10);
            $transaction->payment_method = $paymentStatus->payment_method;
            $transaction->amount = $paymentStatus->amount;
            $transaction->amount_real_currency = round($paymentStatus->amount, 2);
            $transaction->amount_real_currency_name = $paymentStatus->currency_name;
            $transaction->save();
            }
            
        $paymentStatus->payment_status = $request->status;
        $paymentStatus->save();

        return response(['status' => 'success', 'message' => 'Updated Payment Status Successfully']);
    }
}


