<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('pack'); // عدد العبوات في الكرتون
        $table->integer('size');  // حجم العبوة (مثلا 330ml, 500ml)
        $table->decimal('price', 10, 2);
        $table->boolean('available')->default(true);
        $table->string('image')->nullable();        // رابط الصورة أو اسم الملف
        $table->string('category')->nullable();     // تصنيف المنتج
        $table->text('description')->nullable();
         $table->boolean('tax_included')->default(true); // هل السعر شامل الضريبة
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
