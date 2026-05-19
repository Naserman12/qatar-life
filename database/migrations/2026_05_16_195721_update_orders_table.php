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
    Schema::table('orders', function (Blueprint $table) {

        // بيانات العميل
        $table->string('customer_name')->nullable();
        $table->string('phone')->nullable();

        // السلة (مهم جدًا)
        $table->json('items')->nullable();

        // المبالغ
        $table->decimal('subtotal', 10, 2)->default(0);
        $table->decimal('total', 10, 2)->default(0);
        $table->decimal('tax', 10, 2)->default(0);

    
        // الدفع (Moyasar)
        $table->string('payment_id')->nullable();   // id من Moyasar
        $table->string('payment_status')->nullable(); // paid / failed
        $table->string('payment_method')->nullable(); // card / apple_pay

        // callback tracking
        $table->text('payment_response')->nullable();

        $table->timestamp('paid_at')->nullable();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
