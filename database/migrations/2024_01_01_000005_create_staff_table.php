<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('department')->nullable();
            $table->string('type')->default('staff'); // director, former_director, honorary, council, staff
            $table->text('bio')->nullable();
            $table->longText('full_bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('photo_thumbnail')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('achievements')->nullable();
            $table->json('education')->nullable();
            $table->json('career')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
