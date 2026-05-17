<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $response = Http::withBasicAuth(
            config('services.moyasar.secret'),
            ''
        )->post('https://api.moyasar.com/v1/payments', [
            'amount' => $request->amount * 100, // ريال → هللة
            'currency' => 'SAR',
            'description' => $request->description ?? 'Order Payment',
            'callback_url' => url('http://localhost:5173/payment-success'),
            'source' => [
                        'type' => 'creditcard',
                        'name' => 'Test User',
                        'number' => '4111111111111111',
                        'cvc' => '123',
                        'month' => '12',
                        'year' => '2025',
                    ],

        ]);

        return response()->json($response->json());
    }

    public function callback(Request $request)
    {
        return view('payment.success');
    }
}