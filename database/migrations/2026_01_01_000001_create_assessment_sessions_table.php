<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('session_token')->unique();
            $table->enum('status', ['active', 'completed', 'abandoned', 'crisis_terminated'])->default('active');
            $table->enum('mode', ['adaptive', 'comprehensive', 'quick', 'targeted'])->default('adaptive');
            $table->integer('questions_asked')->default(0);
            $table->integer('questions_answered')->default(0);
            $table->integer('max_questions')->default(20);
            $table->decimal('running_sentiment', 5, 4)->default(0);
            $table->integer('running_risk_score')->default(0);
            $table->enum('current_risk_level', ['low', 'moderate', 'high', 'critical'])->default('low');
            $table->json('domains_covered')->nullable();
            $table->json('domains_pending')->nullable();
            $table->json('ai_metadata')->nullable();
            $table->boolean('crisis_flag_raised')->default(false);
            $table->timestamp('crisis_flagged_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('session_token');
        });

        Schema::create('session_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('assessment_sessions')->onDelete('cascade');
            $table->integer('sequence');
            $table->string('domain', 60);
            $table->string('subdomain', 80)->nullable();
            $table->text('question_text');
            $table->enum('question_type', ['scale', 'boolean', 'text', 'multi_choice', 'frequency'])->default('scale');
            $table->json('options')->nullable();
            $table->integer('weight')->default(1);
            $table->boolean('is_crisis_sensitive')->default(false);
            $table->string('source', 40)->default('template');
            $table->json('generation_metadata')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'sequence']);
        });

        Schema::create('session_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('assessment_sessions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('session_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('raw_answer')->nullable();
            $table->integer('numeric_score')->nullable();
            $table->decimal('sentiment_score', 5, 4)->default(0);
            $table->string('sentiment_label', 30)->nullable();
            $table->decimal('confidence', 4, 3)->default(0);
            $table->json('keywords_detected')->nullable();
            $table->json('analysis_snapshot')->nullable();
            $table->boolean('crisis_detected')->default(false);
            $table->boolean('substance_flagged')->default(false);
            $table->boolean('self_harm_flagged')->default(false);
            $table->integer('response_latency_ms')->nullable();
            $table->timestamp('answered_at');
            $table->timestamps();

            $table->index(['session_id', 'answered_at']);
            $table->index('crisis_detected');
        });

        Schema::create('assessment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('assessment_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('final_risk_score')->default(0);
            $table->enum('final_risk_level', ['low', 'moderate', 'high', 'critical'])->default('low');
            $table->decimal('final_sentiment', 5, 4)->default(0);
            $table->json('domain_scores')->nullable();
            $table->json('top_concerns')->nullable();
            $table->json('protective_factors')->nullable();
            $table->json('crisis_indicators')->nullable();
            $table->json('diagnostic_summary')->nullable();
            $table->json('recommendations')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['user_id', 'generated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_reports');
        Schema::dropIfExists('session_answers');
        Schema::dropIfExists('session_questions');
        Schema::dropIfExists('assessment_sessions');
    }
};