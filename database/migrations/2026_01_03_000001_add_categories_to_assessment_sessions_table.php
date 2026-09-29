<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_sessions', 'selected_categories')) {
                $table->json('selected_categories')->nullable()->after('mode');
            }
            if (!Schema::hasColumn('assessment_sessions', 'merged_advice')) {
                $table->json('merged_advice')->nullable()->after('ai_metadata');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessment_sessions', function (Blueprint $table) {
            $table->dropColumn(['selected_categories', 'merged_advice']);
        });
    }
};