<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('popup_path')->nullable();
            $table->string('webp_path')->nullable();
            $table->string('thumbnail_webp_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->string('folder')->nullable(); // news, staff, services, etc.
            $table->timestamps();
        });

        // Stats for the stats section of the homepage
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value');
            $table->string('suffix')->nullable(); // +, %, etc.
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('group')->default('homepage'); // homepage, about, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stats');
        Schema::dropIfExists('media_library');
    }
};
