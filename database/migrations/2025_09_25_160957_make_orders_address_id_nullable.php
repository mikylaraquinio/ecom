<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop existing FK
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']); // usually orders_address_id_foreign
        });

        // Make column nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->nullable()->change();
        });

        // Re-add FK with SET NULL on delete
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('address_id')
                  ->references('id')->on('addresses')
                  ->nullOnDelete(); // same as ON DELETE SET NULL
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('address_id')
                  ->references('id')->on('addresses')
                  ->cascadeOnDelete();
        });
    }
};
