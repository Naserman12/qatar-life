<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    private function baseUrl()
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function getAccessToken()
    {
        $response = Http::asForm()
            ->withBasicAuth(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
            ->post($this->baseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ]);

        if (!$response->successful()) {

            logger()->error('PayPal Auth Error', [
                'response' => $response->body()
            ]);

            throw new \Exception(
                "PayPal Auth Failed"
            );
        }

        return $response->json()['access_token'];
    }

    public function createOrder($amount)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->baseUrl() . '/v2/checkout/orders', [

                "intent" => "CAPTURE",

                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => number_format($amount, 2, '.', '')
                        ]
                    ]
                ],

                "application_context" => [
                    "shipping_preference" => "NO_SHIPPING",
                    "user_action" => "PAY_NOW"
                ]
            ]);

        if (!$response->successful()) {

            logger()->error('PayPal Create Order Error', [
                'response' => $response->body()
            ]);

            throw new \Exception(
                "PayPal Create Order Failed"
            );
        }

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post(
                $this->baseUrl() .
                "/v2/checkout/orders/{$orderId}/capture"
            );

        if (!$response->successful()) {

            logger()->error('PayPal Capture Error', [
                'response' => $response->body()
            ]);

        return [
        'paypal_status' => $response->status(),
        'paypal_body' => $response->json(),
            ];

        }

        return $response->json();
    }
}