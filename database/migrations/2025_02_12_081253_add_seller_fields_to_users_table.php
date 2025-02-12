<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('role')->default('buyer'); // Default role is buyer
            $table->string('farm_name')->nullable();
            $table->string('farm_address')->nullable();
            $table->string('gov_id')->nullable();
            $table->string('farm_certificate')->nullable();
            $table->string('mobile_money')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'farm_name', 'farm_address', 'gov_id', 'farm_certificate', 'mobile_money']);
        });
    }

};
