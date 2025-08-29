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
    Schema::table('products', function (Blueprint $table) {
        $table->string('unit')->default('kg'); // kg, piece, etc.
        $table->integer('min_order_qty')->default(1); // Minimum order quantity
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('unit');
        $table->dropColumn('min_order_qty');
    });
}

};
