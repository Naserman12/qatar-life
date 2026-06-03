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
            'payment_id' => null,
            'user_id' => Auth::id(),
            'customer_name' => auth()->user()->name,
            'items' => json_encode($request->cart),
            'subtotal' => $request->amount,
            'total' => $request->amount,
            'payment_status' => 'pending',
            'payment_method' => 'paypal',
            'is_gift' => $request->gift ? true : false,

            'gift_name' => $request->gift['name'] ?? null,
            'gift_phone' => $request->gift['phone'] ?? null,
            'gift_from' => $request->gift['from'] ?? null,
            'gift_message' => $request->gift['message'] ?? null,
            'gift_contact_method' => $request->gift['gift_contact_method'] ?? null,
            ]);
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'message' => 'Already paid'
                ]);
            } 
        $paypalOrder = $paypal->createOrder($request->amount);

        $order->update([
            'payment_id' => $paypalOrder['id']
        ]);

        return response()->json([
            'orderID' => $paypalOrder['id']
        ]);
    }
//     public function captureOrder(Request $request, PayPalService $paypal)
// {
//     try {

//         error_log('CAPTURE REQUEST: ' . json_encode($request->all()));

//         $data = $paypal->captureOrder($request->orderID);

//         error_log('PAYPAL RESPONSE: ' . json_encode($data));

//         $order = Order::where('payment_id', $request->orderID)->first();

//         if (!$order) {
//             return response()->json([
//                 'error' => 'Order not found'
//             ], 404);
//         }

//          $status = $data['status'] ?? null;

//         if (!in_array($status, ['COMPLETED', 'APPROVED'])) {
//             logger()->error('Invalid PayPal Status', $data);

//             return response()->json([
//                 'error' => 'Payment not completed',
//                 'status' => $status,
//                 'data' => $data
//             ], 400);
//         } else {

//             $order->update([
//                 'payment_status' => 'failed',
//                 'payment_response' => json_encode($data),
//             ]);
//         }

//         return response()->json([
//             'success' => true,
//             'status' => $status,
//             'order' => $order
//         ]);

//     } catch (\Throwable $e) {

//         error_log('CAPTURE ERROR: ' . $e->getMessage());

//         return response()->json([
//             'error' => $e->getMessage(),
//             'line' => $e->getLine()
//         ], 500);
//     }
// }
public function captureOrder(Request $request, PayPalService $paypal)
{
    try {

        $data = $paypal->captureOrder($request->orderID);

        $order = Order::where('payment_id', $request->orderID)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // $status = $data['status'] ?? null;
        $status = $data['status']
        ?? $data['purchase_units'][0]['payments']['captures'][0]['status']
        ?? null;

        if (in_array($status, ['COMPLETED', 'APPROVED'])) {

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

        return response()->json([
            'success' => true,
            'status' => $status,
        ]);

    } catch (\Throwable $e) {

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