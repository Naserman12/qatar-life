<?php
namespace App\Http\Controllers;
use App\Services\PayPalService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{
    public function createOrder(Request $request, PayPalService $paypal)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'cart' => 'required|array'
        ]);
        

        $order = Order::create([
            'user_id' => Auth::id(),
            'customer_name' => auth()->user()->name,
            'items' => json_encode($request->cart),
            'subtotal' => $request->amount,
            'total' => $request->amount,
            'payment_status' => 'pending',
            'payment_method' => 'paypal',
        ]);

        $paypalOrder = $paypal->createOrder($request->amount);

        $order->update([
            'payment_id' => $paypalOrder['id']
        ]);

        return response()->json([
            'orderID' => $paypalOrder['id']
        ]);
    }

public function captureOrder(
    Request $request,
    PayPalService $paypal
) {
    try {

        logger()->info('CAPTURE REQUEST', [
            'orderID' => $request->orderID
        ]);

        $data = $paypal->captureOrder($request->orderID);

        logger()->info('PAYPAL CAPTURE RESPONSE', $data);

        // 1️⃣ إيجاد الطلب
        $order = Order::where('payment_id', $request->orderID)->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        // 2️⃣ التحقق من حالة الدفع
        if (($data['status'] ?? null) === 'COMPLETED') {

            $order->update([
                'payment_status' => 'paid',
                'payment_response' => json_encode($data),
                'paid_at' => now(),
            ]);

        } else {

            $order->update([
                'payment_status' => 'failed',
                'payment_response' => json_encode($data),
            ]);
        }

        // 3️⃣ رد واضح للفرونت
        return response()->json([
            'success' => true,
            'status' => $data['status'],
            'order' => $order
        ]);

    } catch (\Throwable $e) {

        logger()->error('CAPTURE ERROR', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function handle(Request $request)
    {
        $event = $request->all();

        if ($event['event_type'] === 'PAYMENT.CAPTURE.COMPLETED') {

            $orderId = $event['resource']['supplementary_data']['related_ids']['order_id'] ?? null;

            $order = Order::where('payment_id', $orderId)->first();

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_response' => json_encode($event),
                    'paid_at' => now(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

}