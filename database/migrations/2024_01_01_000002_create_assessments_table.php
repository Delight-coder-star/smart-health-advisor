<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['initial', 'follow_up', 'emergency'])->default('initial');
            $table->integer('total_score')->default(0);
            $table->decimal('sentiment_score', 5, 4)->default(0);
            $table->string('sentiment_label')->nullable();
            $table->enum('risk_level', ['low', 'moderate', 'high', 'critical'])->default('low');
            $table->json('indicators')->nullable();
            $table->json('ai_analysis')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('requires_attention')->default(false);
            $table->boolean('alert_sent')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
			$table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->index(['user_id', 'created_at']);
            $table->index('risk_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};