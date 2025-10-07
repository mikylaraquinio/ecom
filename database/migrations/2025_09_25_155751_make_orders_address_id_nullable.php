<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing FK first (uses the default name "orders_address_id_foreign")
            $table->dropForeign(['address_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Make the column nullable
            $table->unsignedBigInteger('address_id')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            // Re-add FK and allow setting null on delete of the address
            $table->foreign('address_id')
                ->references('id')->on('addresses')
                ->nullOnDelete(); // or ->onDelete('set null')
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Revert to NOT NULL
            $table->unsignedBigInteger('address_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            // Re-add FK (choose your preferred behavior)
            $table->foreign('address_id')
                ->references('id')->on('addresses')
                ->cascadeOnDelete();
        });
    }
};
