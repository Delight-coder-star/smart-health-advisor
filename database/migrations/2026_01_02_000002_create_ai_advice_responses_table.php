<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_advice_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('query_id')->constrained('symptom_queries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('response_text');
            $table->json('possible_conditions')->nullable();
            $table->json('recommended_actions')->nullable();
            $table->json('when_to_see_doctor')->nullable();
            $table->json('prevention_tips')->nullable();
            $table->json('red_flags')->nullable();          // warning signs
            $table->decimal('confidence', 4, 3)->default(0);
            $table->string('source', 40)->default('local');  // local | openai | kb
            $table->json('related_videos')->nullable();
            $table->json('knowledge_refs')->nullable();
            $table->boolean('disclaimer_shown')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_advice_responses');
    }
};