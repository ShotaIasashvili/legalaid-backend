<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('mobile')->nullable()->after('phone');
            $table->string('head')->nullable()->after('email');
            $table->boolean('is_specialized')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'head', 'is_specialized']);
        });
    }
};
