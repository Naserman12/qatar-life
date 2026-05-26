<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
     use HasFactory, 
        SoftDeletes;

    protected $fillable = [
           'name',
            'pack',
            'size',
            'price',
            'available',
            'description',
            'image',
            'category',
            'tax_included',

               // ⭐ مهم
    'type',
    'is_quran',

    // Quran
    'publisher',
    'pages',
    'language',
    'cover_type',
    'edition',
    ];
}
