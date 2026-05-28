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
            ->post($this->baseUrl() . "/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);
            dd([
            config('services.paypal.client_id'),
            config('services.paypal.secret'),
            $response->status(),
            $response->body()
            ]);

        if (!$response->successful()) {
            throw new \Exception("PayPal Auth Failed: " . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function createOrder($amount)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl() . "/v2/checkout/orders", [
                "intent" => "CAPTURE",
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => number_format($amount, 2, '.', '')
                        ]
                    ]
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return $response->json();
    }

    public function captureOrder($orderId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl() . "/v2/checkout/orders/{$orderId}/capture");

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        return $response->json();
    }
}