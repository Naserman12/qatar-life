<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        if (!Auth::check()) {
        return response()->json([
            'message' => 'يجب تسجيل الدخول أولاً'
        ], 401);
        }
        $request->validate([
            'amount' => 'required|numeric',
            'cart' => 'required|array',
            'description' => 'nullable|string',
        ]);

        $response = Http::withBasicAuth(
            config('services.moyasar.secret'),
            ''
        )->post('https://api.moyasar.com/v1/payments', [
            'amount' => $request->amount * 100, // ريال → هللة
            'currency' => 'SAR',
            'description' => $request->description ?? 'Order Payment',
            'callback_url' => 'http://localhost:5173/payment-success',
            'error_url' => 'http://localhost:5173/payment-failed',

            'source' => [
                        'type' => 'creditcard',
                        'name' => 'Test User',
                        'number' => '4111111111111111',
                        'cvc' => '123',
                        'month' => '12',
                        'year' => '2028',
                    ],

        ]);

        return response()->json($response->json());
    }

    public function callback(Request $request)
    {
        $orderId = $request->query('order_id');

        $order = Order::find($orderId);

        if ($order) {
            $order->status = 'paid';
            $order->save();
        }

        return redirect('http://localhost:5173/payment-success');
    }
}