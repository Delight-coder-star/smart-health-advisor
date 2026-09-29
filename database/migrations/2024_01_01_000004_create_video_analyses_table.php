<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('video_path');
            $table->integer('duration_seconds')->default(10);
            $table->decimal('pupil_dilation_score', 5, 4)->default(0);
            $table->decimal('eye_movement_score', 5, 4)->default(0);
            $table->decimal('blink_rate', 8, 4)->default(0);
            $table->json('analysis_results')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['assessment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_analyses');
    }
};