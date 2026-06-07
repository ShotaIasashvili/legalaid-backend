<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'deleted_at'], 'posts_status_deleted_at_index');
            $table->index(['deleted_at', 'created_at'], 'posts_deleted_at_created_at_index');
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->index(['status', 'deleted_at', 'deadline'], 'vacancies_status_deleted_at_deadline_index');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->index(['is_active', 'deleted_at'], 'staff_is_active_deleted_at_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('deleted_at', 'documents_deleted_at_index');
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->index('deleted_at', 'offices_deleted_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropIndex('offices_deleted_at_index');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_deleted_at_index');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropIndex('staff_is_active_deleted_at_index');
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropIndex('vacancies_status_deleted_at_deadline_index');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_deleted_at_created_at_index');
            $table->dropIndex('posts_status_deleted_at_index');
        });
    }
};