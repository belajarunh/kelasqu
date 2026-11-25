<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->unsignedSmallInteger('position');
            $table->longText('content')->nullable();
            $table->string('video_url', 2048)->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->longText('assignment')->nullable();
            $table->unsignedTinyInteger('quiz_pass_percentage')->default(75);
            $table->timestamps();

            $table->index(['classroom_id', 'position']);
        });

        Schema::create('classroom_lesson_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_lesson_id')->constrained('classroom_lessons')->cascadeOnDelete();
            $table->text('prompt');
            $table->json('options');
            $table->unsignedTinyInteger('correct_option');
            $table->unsignedSmallInteger('position');
            $table->timestamps();

            $table->index(['classroom_lesson_id', 'position']);
        });

        Schema::create('classroom_lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_lesson_id')->constrained('classroom_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->unique(['classroom_lesson_id', 'user_id']);
        });

        Schema::create('classroom_lesson_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_lesson_id')->constrained('classroom_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score_percentage');
            $table->unsignedSmallInteger('correct_answers');
            $table->unsignedSmallInteger('total_questions');
            $table->boolean('passed');
            $table->timestamps();

            $table->index(
                ['classroom_lesson_id', 'user_id', 'score_percentage'],
                'lesson_quiz_attempt_score_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_lesson_quiz_attempts');
        Schema::dropIfExists('classroom_lesson_completions');
        Schema::dropIfExists('classroom_lesson_questions');
        Schema::dropIfExists('classroom_lessons');
    }
};
