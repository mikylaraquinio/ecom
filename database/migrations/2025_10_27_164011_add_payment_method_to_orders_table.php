<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Ensure that the 'payment_method' column allows only 'cod', 'cop', or 'online'
        $table->enum('payment_method', ['online', 'cod', 'cop'])->default('cod')->change();
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        // Roll back to the previous state (before 'cop' was added)
        $table->enum('payment_method', ['online', 'cod'])->default('cod')->change();
    });
}

};
