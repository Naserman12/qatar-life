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

        'total',

        'items',
        'paid_at',

        'is_gift',
        'gift_name',
        'gift_phone',
        'gift_from',
        'gift_message',
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
