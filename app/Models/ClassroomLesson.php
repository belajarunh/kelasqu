<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassroomLesson extends Model
{
    protected $fillable = [
        'classroom_id',
        'title',
        'position',
        'content',
        'video_url',
        'attachment_path',
        'attachment_name',
        'assignment',
        'quiz_pass_percentage',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'quiz_pass_percentage' => 'integer',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ClassroomLessonQuestion::class)->orderBy('position');
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(ClassroomLessonQuizAttempt::class);
    }

    public function getVideoEmbedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $host = strtolower((string) parse_url($this->video_url, PHP_URL_HOST));
        $path = (string) parse_url($this->video_url, PHP_URL_PATH);

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = trim($path, '/');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            parse_str((string) parse_url($this->video_url, PHP_URL_QUERY), $query);
            $videoId = $query['v'] ?? null;

            if (! $videoId && preg_match('~^/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $path, $matches)) {
                $videoId = $matches[1];
            }
        } elseif (in_array($host, ['vimeo.com', 'www.vimeo.com'], true)) {
            $videoId = trim($path, '/');

            return ctype_digit($videoId) ? "https://player.vimeo.com/video/{$videoId}" : null;
        } else {
            return null;
        }

        return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]+$/', $videoId)
            ? "https://www.youtube-nocookie.com/embed/{$videoId}"
            : null;
    }
}
