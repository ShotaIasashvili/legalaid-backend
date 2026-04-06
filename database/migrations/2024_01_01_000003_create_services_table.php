<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('full_content')->nullable();
            $table->string('icon')->nullable();
            $table->string('category');
            $table->string('color')->nullable();
            $table->json('requirements')->nullable();
            $table->json('how_to_apply')->nullable();
            $table->json('related_services')->nullable();
            $table->json('special_eligibility_categories')->nullable();
            $table->json('download_links')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('featured_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
