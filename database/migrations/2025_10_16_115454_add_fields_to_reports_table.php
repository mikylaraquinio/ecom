<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('target_id')->nullable()->after('user_id');
            $table->string('target_type')->nullable()->after('target_id');
            $table->string('severity')->nullable()->after('category');
            $table->string('attachment')->nullable()->after('description');
            $table->string('contact_email')->nullable()->after('attachment');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['target_id', 'target_type', 'severity', 'attachment', 'contact_email']);
        });
    }
};
