<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('seller_bank_name')->nullable()->after('price');
            $table->string('seller_account_number')->nullable()->after('seller_bank_name');
            $table->string('seller_account_name')->nullable()->after('seller_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['seller_bank_name', 'seller_account_number', 'seller_account_name']);
        });
    }
};
