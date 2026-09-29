<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('symptom_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('question');
            $table->string('category', 60)->nullable();      // pregnancy, cancer, bp, general
            $table->json('symptoms_mentioned')->nullable();
            $table->json('keywords_detected')->nullable();
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('status', ['pending', 'answered', 'escalated'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('symptom_queries');
    }
};