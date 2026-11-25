<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomLessonQuizAttempt extends Model
{
    protected $fillable = [
        'classroom_lesson_id',
        'user_id',
        'score_percentage',
        'correct_answers',
        'total_questions',
        'passed',
    ];

    protected function casts(): array
    {
        return [
            'score_percentage' => 'integer',
            'correct_answers' => 'integer',
            'total_questions' => 'integer',
            'passed' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(ClassroomLesson::class, 'classroom_lesson_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
