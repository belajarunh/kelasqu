<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomLessonQuestion extends Model
{
    protected $fillable = [
        'classroom_lesson_id',
        'prompt',
        'options',
        'correct_option',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_option' => 'integer',
            'position' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(ClassroomLesson::class, 'classroom_lesson_id');
    }
}
