<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * 💳 إنشاء عملية دفع في Moyasar
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'cart_items' => 'required|array',
            'gift_data' => 'nullable|array',
        ]);

        // 🧠 إنشاء معرف مؤقت للدفع
        $paymentSessionId = uniqid('payment_');

        // 💾 حفظ بيانات الطلب مؤقتًا
        Cache::put($paymentSessionId, [
            'amount' => $request->amount,
            'cart_items' => $request->cart_items,
            'gift_data' => $request->gift_data,
            'user_id' => $request->user()->id ?? null,
        ], now()->addMinutes(30)); // صلاحية 30 دقيقة
        // 💳 إنشاء عملية دفع في Moyasar
$response = Http::withBasicAuth(
    config('services.moyasar.secret'),
    ''
)->post('https://api.moyasar.com/v1/payments', [
    'amount' => intval($request->amount) * 100,
    'currency' => 'SAR',
    'description' => 'Order Payment - Katra Life',
    'callback_url' => 'https://qatar-life-production.up.railway.app/api/payment/callback?session_id=' . $paymentSessionId,
    'error_url' => 'http://localhost:5173/payment-failed',
  'source' => [
        'type' => 'creditcard',
          'name' => $request->card['name'],
    'number' => $request->card['number'],
    'month' => $request->card['month'],
    'year' => $request->card['year'],
    'cvc' => $request->card['cvc'],
    ]
]);



        return response()->json($response->json());
    }

    /**
     * ✅ بعد نجاح الدفع
     */
    public function callback(Request $request)
    {
        $sessionId = $request->query('session_id');

        // 🔍 استرجاع بيانات الطلب المؤقتة
        $paymentData = Cache::get($sessionId);

        if (!$paymentData) {
            return response()->json([
                'message' => '❌ انتهت صلاحية جلسة الدفع'
            ], 404);
        }

        // 💳 إنشاء الطلب الحقيقي بعد نجاح الدفع
        $order = Order::create([
            'user_id' => $paymentData['user_id'],

            // 💰 المبلغ
            'total' => $paymentData['amount'],

            // 🛒 المنتجات
            'items' => json_encode($paymentData['cart_items']),

            // 📌 حالة الطلب
            'status' => 'paid',

            // 🎁 الإهداء
            'is_gift' => !empty($paymentData['gift_data']),

            'gift_name' => $paymentData['gift_data']['name'] ?? null,
            'gift_phone' => $paymentData['gift_data']['phone'] ?? null,
            'gift_from' => $paymentData['gift_data']['from'] ?? null,
            'gift_message' => $paymentData['gift_data']['message'] ?? null,

            // 📲 طريقة التواصل
            'gift_send_mode' => $paymentData['gift_data']['contact_method'] ?? 'store',
        ]);

        // 🧹 حذف الجلسة المؤقتة
        Cache::forget($sessionId);

        // 🎁 تنفيذ منطق الإهداء
        $this->processGift($order);

        // 🔁 تحويل المستخدم لصفحة النجاح
        return redirect('https://qatra-haya.web.app/payment-success');
    }
    /**
     * 🎁 معالجة الإهداء بعد إنشاء الطلب
     */
    private function processGift(Order $order){
        if (!$order->is_gift) {
            return;
        }
        // 📲 الإهداء عبر المتجر
        if ($order->gift_send_mode === 'store') {

            Log::info('🎁 Gift via STORE', [
                'to' => $order->gift_phone,
                'name' => $order->gift_name,
                'message' => $order->gift_message,
                'from' => $order->gift_from,
            ]);
            // هنا لاحقًا:
            // WhatsApp API / SMS / Notification
        }
        // 👤 الإهداء من المستخدم
        if ($order->gift_send_mode === 'user') {
            Log::info('🎁 Gift via USER (no auto send)', [
                'note' => 'User will contact recipient manually',
            ]);
        }
    }
}