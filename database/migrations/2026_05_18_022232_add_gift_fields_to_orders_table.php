<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // هل الطلب إهداء؟
            $table->boolean('is_gift')
                  ->default(false);

            // اسم المُهدى إليه
            $table->string('gift_name')
                  ->nullable();

            // رقم المُهدى إليه
            $table->string('gift_phone')
                  ->nullable();

            // اسم المُهدي
            $table->string('gift_from')
                  ->nullable();

            // رسالة الإهداء
            $table->text('gift_message')
                  ->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'is_gift',
                'gift_name',
                'gift_phone',
                'gift_from',
                'gift_message'
            ]);

        });
    }
};