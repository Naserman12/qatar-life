<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // عرض كل المنتجات
    public function index()
    {
        $products = Product::all();
        return response()->json(['data' => $products]);
    }
      /**
     * 🧴 عرض منتجات عادية فقط
     */
    public function products()
    {
        $products = Product::where('type', 'product')->get();

        return response()->json($products);
    }
    /**
     * 📖 عرض كتب القرآن فقط
     */
    public function quran()
    {
        $quran = Product::where('type', 'quran')->get();

        return response()->json($quran);
    }
    /**
     * Store a newly created resource in storage.
     */
      // إضافة منتج جديد
public function store( ProductRequest  $request)
{
    $product = Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'image' => $request->image,
        'description' => $request->description,
        'available' => filter_var($request->available, FILTER_VALIDATE_BOOLEAN),

        'type' => $request->type,

        // 📖 Quran flag
        'is_quran' => $request->type === 'quran' ? 1 : 0,

        // 📖 Quran fields
        'publisher' => $request->publisher,
        'pages' => $request->pages,
        'language' => $request->language,
        'cover_type' => $request->cover_type,
        'edition' => $request->edition,

        // 🧴 product fields
        'size' => $request->size,
        'pack' => $request->pack,
    ]);

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
   public function update(ProductRequest $request, $id)
{
    $product = Product::findOrFail($id);

    $product->update([
        'name' => $request->name,
        'price' => $request->price,
        'image' => $request->image,
        'description' => $request->description,
        'available' => filter_var($request->available, FILTER_VALIDATE_BOOLEAN),

        'type' => $request->type,

        'is_quran' => $request->type === 'quran' ? 1 : 0,


        'publisher' => $request->publisher,
        'pages' => $request->pages,
        'language' => $request->language,
        'cover_type' => $request->cover_type,
        'edition' => $request->edition,

        'size' => $request->size,
        'pack' => $request->pack,
    ]);

    return response()->json([
        'message' => '✅ تم تحديث المنتج بنجاح',
        'product' => $product
    ]);
} 
// إظهار واخفاء المنتجات
    public function toggaleProduct(Req){
        return
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
