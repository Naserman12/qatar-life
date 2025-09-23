<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // عرض كل المنتجات
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
      // إضافة منتج جديد
    public function store(Request $request)
    {
        // التحقق من المدخلات
        $request->validate([
                'name' => 'required|string|max:255',
                'pack' => 'required|integer|min:1',
                'size' => 'required|string|max:50',
                'price' => 'required|numeric|min:0',
                'available' => 'boolean',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'category' => 'nullable|string',
                'tax_included' => 'boolean',
        ]);

        // إنشاء المنتج
        $product = Product::create($request->all());

        return response()->json([
            'message' => '✅ تم إضافة المنتج بنجاح',
            'product' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
   // عرض منتج واحد
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'المنتج غير موجود'], 404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id){
    // إيجاد المنتج حسب الـ ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => '❌ المنتج غير موجود'], 404);
        }

        // ✅ التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pack' => 'required|integer|min:1',
            'size' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'available' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'category' => 'nullable|string',
            'tax_included' => 'boolean',
        ]);

        // ✅ تحديث المنتج
        $product->update($validated);

        // ✅ الرد بالنجاح والمنتج المحدث
        return response()->json([
            'message' => '✅ تم تحديث المنتج بنجاح',
            'product' => $product
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    // حذف منتج
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'المنتج غير موجود'], 404);
        }

        $product->delete();

        return response()->json(['message' => '✅ تم حذف المنتج']);
    }
}
