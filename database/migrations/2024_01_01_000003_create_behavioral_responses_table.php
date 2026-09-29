<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('behavioral_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('question_key');
            $table->text('question_text');
            $table->text('response_text');
            $table->integer('response_score')->default(0);
            $table->decimal('sentiment_score', 5, 4)->default(0);
            $table->json('keywords_detected')->nullable();
            $table->timestamps();

            $table->index(['assessment_id', 'question_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('behavioral_responses');
    }
};