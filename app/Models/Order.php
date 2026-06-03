<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

 protected $fillable = [
    'user_id',
    'status',

    // المبالغ
    'subtotal',
    'total',
    'tax',

    // بيانات العميل
    'customer_name',
    'phone',

    // السلة
    'items',

    // الدفع
    'payment_id',
    'payment_status',
    'payment_method',
    'payment_response',

    // وقت الدفع
    'paid_at',

    // بيانات الهدية
    'is_gift',
    'gift_contact_method',
    'gift_name',
    'gift_phone',
    'gift_from',
    'gift_message',

    // بيانات المسجد
    'mosque_type',
    'district_id',
    'custom_mosque_name',
];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
    'items' => 'array',
    'paid_at' => 'datetime',
    'is_gift' => 'boolean',
    ];
}
