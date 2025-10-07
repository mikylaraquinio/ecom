<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // add the new column
            //$table->enum('fulfillment_method', ['delivery','pickup'])->default('delivery')->after('payment_method');

            // if you allow pickup with no address:
            // NOTE: requires doctrine/dbal to change an existing column
            // composer require doctrine/dbal
            $table->unsignedBigInteger('address_id')->nullable()->change();

            // if you also write shipping_fee in controller but column is missing, add it:
            // $table->decimal('shipping_fee', 10, 2)->default(0)->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('fulfillment_method');
            // If you changed address_id to nullable, you can’t easily revert without knowing original spec.
            // $table->unsignedBigInteger('address_id')->nullable(false)->change();
            // $table->dropColumn('shipping_fee');
        });
    }
};