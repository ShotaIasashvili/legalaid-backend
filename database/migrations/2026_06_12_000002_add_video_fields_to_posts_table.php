<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('source_url');
            $table->string('video_provider')->nullable()->after('video_url');
            $table->text('video_embed_url')->nullable()->after('video_provider');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'video_url',
                'video_provider',
                'video_embed_url',
            ]);
        });
    }
};
