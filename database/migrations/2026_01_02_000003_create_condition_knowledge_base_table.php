<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condition_knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('condition_name');
            $table->string('category', 60);                  // pregnancy, cancer, bp, diabetes...
            $table->text('summary');
            $table->json('common_symptoms')->nullable();
            $table->json('risk_factors')->nullable();
            $table->json('prevention')->nullable();
            $table->json('when_to_seek_help')->nullable();
            $table->json('keywords')->nullable();            // search keywords
            $table->string('severity', 20)->default('moderate');
            $table->boolean('is_chronic')->default(false);
            $table->boolean('requires_medication')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condition_knowledge_base');
    }
};