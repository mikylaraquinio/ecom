<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Step 1: shop + pickup
            $table->string('shop_name', 60);
            $table->string('pickup_address')->nullable();   // preview/combined string

            $table->string('pickup_full_name')->nullable();
            $table->string('pickup_phone', 30)->nullable();
            $table->string('pickup_region_group', 60)->nullable();
            $table->string('pickup_province', 80)->nullable();
            $table->string('pickup_city', 80)->nullable();
            $table->string('pickup_barangay', 120)->nullable();
            $table->string('pickup_postal', 12)->nullable();
            $table->text('pickup_detail')->nullable();

            // Step 2: business + docs
            $table->enum('business_type', ['individual','sole','corporation','cooperative'])->default('individual');
            $table->string('tax_id', 50)->nullable();

            $table->string('gov_id_path')->nullable();
            $table->string('rsbsa_path')->nullable();
            $table->string('mayors_permit_path')->nullable();

            // status of review/approval
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
