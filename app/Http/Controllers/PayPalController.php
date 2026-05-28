<?php
namespace App\Http\Controllers;
use App\Services\PayPalService;
use App\Models\Order;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function createOrder(Request $request, PayPalService $paypal)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'cart' => 'required|array'
        ]);

        $order = Order::create([
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

    public function captureOrder(Request $request, PayPalService $paypal)
    {
        $data = $paypal->captureOrder($request->orderID);

        $order = Order::where('payment_id', $request->orderID)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'payment_response' => json_encode($data),
                'paid_at' => now(),
            ]);
        }

        return response()->json($data);
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