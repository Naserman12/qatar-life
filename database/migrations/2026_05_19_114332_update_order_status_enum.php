<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
        {
            DB::statement("ALTER TABLE orders MODIFY status ENUM('pending','paid','confirmed','delivered','cancelled') DEFAULT 'pending'");
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
