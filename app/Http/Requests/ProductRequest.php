<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 🧴 المنتج الأساسي
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'available' => 'boolean',

            // 🧾 النوع
            'type' => 'required|in:product,quran',

            // 🧴 منتجات عادية
            'size' => 'nullable|string|max:50',
            'pack' => 'nullable|integer|min:1',

            // 📖 المصحف
            'publisher' => 'nullable|string|max:255',
            'pages' => 'nullable|integer|min:1|max:2000',
            'language' => 'nullable|string|max:100',
            'cover_type' => 'nullable|string|max:50',
            'edition' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المنتج مطلوب',
            'price.required' => 'السعر مطلوب',
            'price.numeric' => 'السعر يجب أن يكون رقم',
            'type.required' => 'نوع المنتج مطلوب',
            'type.in' => 'نوع المنتج غير صحيح',
            'pages.integer' => 'عدد الصفحات يجب أن يكون رقم',
        ];
    }
}