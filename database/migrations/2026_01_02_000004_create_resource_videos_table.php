<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('youtube_id', 20)->nullable();
            $table->string('url');
            $table->string('category', 60);
            $table->json('keywords')->nullable();
            $table->text('description')->nullable();
            $table->string('language', 10)->default('en');
            $table->integer('duration_seconds')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_videos');
    }
};