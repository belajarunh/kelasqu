<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ClassroomLesson;
use App\Models\ClassroomLessonCompletion;
use App\Models\ClassroomLessonQuestion;
use App\Models\ClassroomLessonQuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

class ClassroomLessonController extends Controller
{
    public function create(Classroom $classroom): View
    {
        $this->ensureAdmin();

        return view('classrooms.lessons.form', [
            'classroom' => $classroom,
            'lesson' => null,
            'nextPosition' => (int) $classroom->lessons()->max('position') + 1,
        ]);
    }

    public function store(Request $request, Classroom $classroom): RedirectResponse
    {
        $this->ensureAdmin();
        $validated = $this->validateLesson($request);
        [$attachmentPath, $attachmentName] = $this->resolveAttachment($request);

        $classroom->lessons()->create([
            ...$validated,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        return redirect()->route('kelas.show', $classroom)->with('status', 'Pertemuan berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom, ClassroomLesson $lesson): View
    {
        $this->ensureAdmin();
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $lesson->load('questions');

        return view('classrooms.lessons.form', [
            'classroom' => $classroom,
            'lesson' => $lesson,
            'nextPosition' => $lesson->position,
        ]);
    }

    public function update(Request $request, Classroom $classroom, ClassroomLesson $lesson): RedirectResponse
    {
        $this->ensureAdmin();
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);

        $validated = $this->validateLesson($request);
        $oldAttachmentPath = $lesson->attachment_path;
        [$attachmentPath, $attachmentName] = $this->resolveAttachment($request, $lesson);

        $lesson->update([
            ...$validated,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        if ($oldAttachmentPath && $oldAttachmentPath !== $attachmentPath) {
            Storage::disk('local')->delete($oldAttachmentPath);
        }

        return redirect()->route('kelas.show', $classroom)->with('status', 'Pertemuan berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom, ClassroomLesson $lesson): RedirectResponse
    {
        $this->ensureAdmin();
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $attachmentPath = $lesson->attachment_path;

        $lesson->delete();

        if ($attachmentPath) {
            Storage::disk('local')->delete($attachmentPath);
        }

        return redirect()->route('kelas.show', $classroom)->with('status', 'Pertemuan berhasil dihapus.');
    }

    public function downloadAttachment(Classroom $classroom, ClassroomLesson $lesson)
    {
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $this->ensureClassroomAccess($classroom);
        abort_unless($lesson->attachment_path && Storage::disk('local')->exists($lesson->attachment_path), 404);

        return Storage::disk('local')->download($lesson->attachment_path, $lesson->attachment_name);
    }

    public function complete(Classroom $classroom, ClassroomLesson $lesson): RedirectResponse
    {
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $this->ensureEnrolledLearner($classroom);

        ClassroomLessonCompletion::updateOrCreate(
            [
                'classroom_lesson_id' => $lesson->id,
                'user_id' => auth()->id(),
            ],
            ['completed_at' => now()],
        );

        return redirect()->route('kelas.show', $classroom)->with('status', 'Progres pertemuan berhasil diperbarui.');
    }

    public function uncomplete(Classroom $classroom, ClassroomLesson $lesson): RedirectResponse
    {
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $this->ensureEnrolledLearner($classroom);

        ClassroomLessonCompletion::query()
            ->where('classroom_lesson_id', $lesson->id)
            ->where('user_id', auth()->id())
            ->delete();

        return redirect()->route('kelas.show', $classroom)->with('status', 'Progres pertemuan diperbarui.');
    }

    public function submitQuiz(Request $request, Classroom $classroom, ClassroomLesson $lesson): RedirectResponse
    {
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $this->ensureEnrolledLearner($classroom);

        $questions = $lesson->questions;
        abort_if($questions->isEmpty(), 404);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'between:0,3'],
        ]);

        $answers = $validated['answers'];
        $expectedQuestionIds = $questions->modelKeys();
        $submittedQuestionIds = array_map('intval', array_keys($answers));
        sort($expectedQuestionIds);
        sort($submittedQuestionIds);

        if ($expectedQuestionIds !== $submittedQuestionIds) {
            throw ValidationException::withMessages([
                'answers' => 'Pastikan semua pertanyaan dijawab.',
            ]);
        }

        $correctAnswers = $questions->filter(
            fn (ClassroomLessonQuestion $question): bool => (int) $answers[$question->id] === $question->correct_option,
        )->count();
        $totalQuestions = $questions->count();
        $scorePercentage = (int) floor(($correctAnswers / $totalQuestions) * 100);
        $passed = $scorePercentage >= $lesson->quiz_pass_percentage;

        ClassroomLessonQuizAttempt::create([
            'classroom_lesson_id' => $lesson->id,
            'user_id' => auth()->id(),
            'score_percentage' => $scorePercentage,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'passed' => $passed,
        ]);

        return redirect()->route('kelas.show', $classroom)->with('quiz_result', [
            'lesson_id' => $lesson->id,
            'score' => $scorePercentage,
            'passed' => $passed,
        ]);
    }

    public function storeQuestion(Request $request, Classroom $classroom, ClassroomLesson $lesson): RedirectResponse
    {
        $this->ensureAdmin();
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);

        $lesson->questions()->create([
            ...$this->validateQuestion($request),
            'position' => (int) $lesson->questions()->max('position') + 1,
        ]);

        return back()->with('status', 'Pertanyaan kuis berhasil ditambahkan.');
    }

    public function updateQuestion(
        Request $request,
        Classroom $classroom,
        ClassroomLesson $lesson,
        ClassroomLessonQuestion $question,
    ): RedirectResponse {
        $this->ensureAdmin();
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $this->ensureQuestionBelongsToLesson($lesson, $question);

        $question->update($this->validateQuestion($request));

        return back()->with('status', 'Pertanyaan kuis berhasil diperbarui.');
    }

    public function destroyQuestion(
        Classroom $classroom,
        ClassroomLesson $lesson,
        ClassroomLessonQuestion $question,
    ): RedirectResponse {
        $this->ensureAdmin();
        $this->ensureLessonBelongsToClassroom($classroom, $lesson);
        $this->ensureQuestionBelongsToLesson($lesson, $question);

        $question->delete();

        return back()->with('status', 'Pertanyaan kuis berhasil dihapus.');
    }

    private function validateLesson(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'position' => ['required', 'integer', 'between:1,65535'],
            'content' => ['nullable', 'string', 'max:50000'],
            'video_url' => ['nullable', 'url:http,https', 'max:2048'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,png,jpg,jpeg,webp,txt', 'max:20480'],
            'remove_attachment' => ['sometimes', 'boolean'],
            'assignment' => ['nullable', 'string', 'max:50000'],
            'quiz_pass_percentage' => ['required', 'integer', 'between:1,100'],
        ]);

        unset($validated['attachment'], $validated['remove_attachment']);

        return $validated;
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'prompt' => ['required', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:500'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
        ]);
    }

    private function resolveAttachment(Request $request, ?ClassroomLesson $lesson = null): array
    {
        $attachmentPath = $lesson?->attachment_path;
        $attachmentName = $lesson?->attachment_name;

        if ($request->boolean('remove_attachment')) {
            $attachmentPath = null;
            $attachmentName = null;
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $storedPath = $file->store('classroom-lessons', 'local');

            if (! is_string($storedPath)) {
                Log::error('Failed to store a classroom lesson attachment.', [
                    'classroom_lesson_id' => $lesson?->id,
                ]);
                throw new RuntimeException('Lampiran pertemuan gagal disimpan.');
            }

            $attachmentPath = $storedPath;
            $attachmentName = mb_substr(
                basename(str_replace('\\', '/', $file->getClientOriginalName())),
                0,
                255,
            );
        }

        return [$attachmentPath, $attachmentName];
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }

    private function ensureClassroomAccess(Classroom $classroom): void
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && ! $classroom->isEnrolledFor($user)) {
            abort(403);
        }
    }

    private function ensureEnrolledLearner(Classroom $classroom): void
    {
        abort_unless(auth()->user()->role !== 'admin' && $classroom->isEnrolledFor(auth()->user()), 403);
    }

    private function ensureLessonBelongsToClassroom(Classroom $classroom, ClassroomLesson $lesson): void
    {
        abort_unless($lesson->classroom_id === $classroom->id, 404);
    }

    private function ensureQuestionBelongsToLesson(ClassroomLesson $lesson, ClassroomLessonQuestion $question): void
    {
        abort_unless($question->classroom_lesson_id === $lesson->id, 404);
    }
}
