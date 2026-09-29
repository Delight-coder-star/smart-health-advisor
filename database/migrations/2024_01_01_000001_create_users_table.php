<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('student_id')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->enum('role', ['student', 'advisor', 'admin'])->default('student');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('risk_score')->default(0);
            $table->enum('risk_level', ['low', 'moderate', 'high', 'critical'])->default('low');
            $table->timestamp('last_assessment_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['role', 'is_active']);
            $table->index('risk_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};