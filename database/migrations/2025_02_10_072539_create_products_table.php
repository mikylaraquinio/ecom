<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 12, 2); // Ensure correct precision
                $table->string('image')->nullable();
                
                // Foreign Keys
                $table->foreignId('category_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ✅ Ensure user_id is set
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
