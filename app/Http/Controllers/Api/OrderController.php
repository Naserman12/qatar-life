<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
     // كل الطلبات (للأدمن فقط)
    public function allOrders()
    {
        $orders = Order::with('product', 'user')->get();
        return response()->json($orders);
    }

     // تأكيد الطلب (يغير الحالة ويحذف من السلة)
    public function confirmOrder($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $order->status = 'confirmed';
        $order->save();

        // حذف الطلب من سلة المستخدم
        $order->delete();

        return response()->json(['message' => '✅ تم تأكيد الطلب وحذفه من السلة']);
    }
    // عرض جميع الطلبات للمستخدم الحالي
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('product')->get();
        return response()->json($orders);
    }

    // إنشاء طلب جديد
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => '✅ تم إنشاء الطلب بنجاح',
            'order' => $order
        ], 201);
    }

    // عرض طلب محدد
    public function show($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->with('product')->first();
        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }
        return response()->json($order);
    }

    // تحديث الطلب (مثلاً لتغيير الكمية)
    public function update(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $order->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => '✅ تم تحديث الطلب',
            'order' => $order
        ]);
    }

    // حذف الطلب
    public function destroy($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        $order->delete();

        return response()->json(['message' => '✅ تم حذف الطلب']);
    }
}
