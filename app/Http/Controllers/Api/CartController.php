<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // عرض عناصر السلة للمستخدم الحالي
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
                         ->with('product')
                         ->get();

        return response()->json($cartItems);
    }

    // إضافة منتج للسلة
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        return response()->json([
            'message' => '✅ تم إضافة المنتج إلى السلة',
            'cart' => $cart
        ], 201);
    }

    // تحديث الكمية
    public function update(Request $request, $id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$cart) {
            return response()->json(['message' => 'عنصر السلة غير موجود'], 404);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => '✅ تم تحديث الكمية',
            'cart' => $cart
        ]);
    }

    // حذف عنصر من السلة
    public function destroy($id)
    {
        $cart = Cart::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$cart) {
            return response()->json(['message' => 'عنصر السلة غير موجود'], 404);
        }

        $cart->delete();

        return response()->json(['message' => '✅ تم حذف المنتج من السلة']);
    }
}
