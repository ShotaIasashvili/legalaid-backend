<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Flexible key-value page content blocks
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page');       // about, history, structure, contact, etc.
            $table->string('section')->nullable();  // hero, mission, stats, etc.
            $table->string('key');
            $table->longText('value')->nullable();
            $table->string('type')->default('text'); // text, html, json, image, boolean
            $table->string('label')->nullable(); // human-readable label for admin
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page', 'section', 'key']);
            $table->index(['page', 'section']);
        });

        // Global site settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('text'); // text, html, json, image, boolean
            $table->string('group')->default('general'); // general, contact, social, seo
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('page_contents');
    }
};
