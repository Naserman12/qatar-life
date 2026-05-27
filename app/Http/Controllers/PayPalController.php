<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalController extends Controller
{
    private function getAccessToken()
    {
        $client = config("services.paypal.client_id");
        $secret = config("services.paypal.secret");
        $response = Http::asForm()->withBasicAuth($client, $secret)
            ->post("https://api-m.sandbox.paypal.com/v1/oauth2/token", [
                "grant_type" => "client_credentials"
            ]);
        return $response->json()["access_token"];
    }

    public function createOrder(Request $request)
    {
            // 1) إنشاء الطلب في قاعدة البيانات
    $order = Order::create([
        'customer_name' => auth()->user()->name ?? 'Guest',
        'phone' => auth()->user()->phone ?? null,
        'items' => json_encode($request->cart_items),
        'subtotal' => $request->amount,
        'total' => $request->amount,
        'tax' => 0,
        'payment_status' => 'pending',
        'payment_method' => 'paypal',
    ]);
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->post(
            "https://api-m.sandbox.paypal.com/v2/checkout/orders",
            [
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $request->amount
                        ]
                    ]
                ]
            ]
        );
        // 3) حفظ رقم العملية من PayPal
    
    $paypalOrderId = $response->json()["id"];
    $order->update([
        'payment_id' => $paypalOrderId
    ]);

     return response()->json([
        "orderID" => $paypalOrderId
    ]);
    }

    public function captureOrder(Request $request)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                "Content-Type" => "application/json"
            ])
            ->post("https://api-m.sandbox.paypal.com/v2/checkout/orders/{$request->orderID}/capture");

        $data = $response->json();

        // 1) إيجاد الطلب في قاعدة البيانات
        $order = Order::where('payment_id', $request->orderID)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'payment_response' => json_encode($data),
                'paid_at' => now(),
            ]);
        }

        return $data;
    }

}
