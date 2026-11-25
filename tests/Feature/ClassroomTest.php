<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\ClassroomLesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_class(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('kelas.store'), [
            'name' => 'Pemrograman Web Dasar',
            'category' => 'Teknologi',
            'description' => 'Belajar dasar HTML, CSS, dan JavaScript.',
        ]);

        $response->assertRedirect(route('kelas.index'));
        $this->assertDatabaseHas('classrooms', [
            'user_id' => $admin->id,
            'name' => 'Pemrograman Web Dasar',
            'category' => 'Teknologi',
        ]);
    }

    public function test_learner_can_enroll_and_view_class(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'user']);
        $classroom = Classroom::factory()->create([
            'user_id' => $admin->id,
            'name' => 'Kelas belajar baru',
            'category' => 'Teknologi',
            'description' => 'Deskripsi kelas',
        ]);

        $this->actingAs($learner)
            ->post(route('kelas.enroll', $classroom))
            ->assertRedirect(route('kelas.catalog'));

        $this->assertDatabaseHas('classroom_user', [
            'user_id' => $learner->id,
            'classroom_id' => $classroom->id,
        ]);

        $this->actingAs($learner)
            ->get(route('kelas.show', $classroom))
            ->assertOk()
            ->assertSee('Kelas belajar baru');
    }

    public function test_admin_can_create_lesson_content_and_enrolled_learners_can_access_it(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'user']);
        $classroom = Classroom::factory()->create(['user_id' => $admin->id]);

        $this->actingAs($admin)
            ->post(route('kelas.pertemuan.store', $classroom), [
                'title' => 'Pengenalan HTML',
                'position' => 1,
                'content' => 'HTML menyusun struktur halaman web.',
                'video_url' => 'https://youtu.be/abc123',
                'attachment' => UploadedFile::fake()->create('materi.pdf', 100, 'application/pdf'),
                'assignment' => 'Buat halaman profil sederhana.',
                'quiz_pass_percentage' => 75,
            ])
            ->assertRedirect(route('kelas.show', $classroom));

        $lesson = ClassroomLesson::query()->where('classroom_id', $classroom->id)->firstOrFail();
        Storage::disk('local')->assertExists($lesson->attachment_path);

        $this->actingAs($admin)
            ->post(route('kelas.pertemuan.questions.store', [$classroom, $lesson]), [
                'prompt' => 'Apa fungsi HTML?',
                'options' => ['Database', 'Struktur halaman', 'Server', 'Browser'],
                'correct_option' => 1,
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->get(route('kelas.pertemuan.edit', [$classroom, $lesson]))
            ->assertOk()
            ->assertSee('Pengenalan HTML')
            ->assertSee('Apa fungsi HTML?');

        $learner->enrolledClassrooms()->attach($classroom);

        $this->actingAs($learner)
            ->get(route('kelas.show', $classroom))
            ->assertOk()
            ->assertSee('Pengenalan HTML')
            ->assertSee('HTML menyusun struktur halaman web.')
            ->assertSee('Buat halaman profil sederhana.')
            ->assertSee('Apa fungsi HTML?')
            ->assertSee('youtube-nocookie.com/embed/abc123');

        $this->get(route('kelas.pertemuan.attachment', [$classroom, $lesson]))->assertOk();

        $stranger = User::factory()->create(['role' => 'user']);
        $this->actingAs($stranger)
            ->get(route('kelas.pertemuan.attachment', [$classroom, $lesson]))
            ->assertForbidden();
    }

    public function test_learner_can_retry_quiz_and_track_best_score_and_lesson_progress(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'user']);
        $classroom = Classroom::factory()->create(['user_id' => $admin->id]);
        $learner->enrolledClassrooms()->attach($classroom);
        $lesson = $classroom->lessons()->create([
            'title' => 'Dasar HTML',
            'position' => 1,
            'quiz_pass_percentage' => 75,
        ]);
        $firstQuestion = $lesson->questions()->create([
            'prompt' => 'Tag untuk judul utama?',
            'options' => ['<h1>', '<p>', '<div>', '<span>'],
            'correct_option' => 0,
            'position' => 1,
        ]);
        $secondQuestion = $lesson->questions()->create([
            'prompt' => 'Tag untuk paragraf?',
            'options' => ['<h1>', '<p>', '<div>', '<span>'],
            'correct_option' => 1,
            'position' => 2,
        ]);

        $this->actingAs($learner)
            ->post(route('kelas.pertemuan.quiz', [$classroom, $lesson]), [
                'answers' => [
                    $firstQuestion->id => 0,
                    $secondQuestion->id => 0,
                ],
            ])
            ->assertSessionHas('quiz_result', fn (array $result): bool => $result['score'] === 50 && ! $result['passed']);

        $this->post(route('kelas.pertemuan.quiz', [$classroom, $lesson]), [
            'answers' => [
                $firstQuestion->id => 0,
                $secondQuestion->id => 1,
            ],
        ])->assertSessionHas('quiz_result', fn (array $result): bool => $result['score'] === 100 && $result['passed']);

        $this->assertDatabaseCount('classroom_lesson_quiz_attempts', 2);

        $this->post(route('kelas.pertemuan.complete', [$classroom, $lesson]))
            ->assertRedirect(route('kelas.show', $classroom));
        $this->assertDatabaseHas('classroom_lesson_completions', [
            'classroom_lesson_id' => $lesson->id,
            'user_id' => $learner->id,
        ]);

        $this->get(route('kelas.show', $classroom))
            ->assertOk()
            ->assertSee('Nilai terbaik:')
            ->assertSee('100%')
            ->assertSee('Selesai');
    }

    public function test_admin_can_manage_learner_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.learners.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.learners.store'), [
                'name' => 'Learner Baru',
                'email' => 'learnerbaru@kelasqu.id',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
            ])
            ->assertRedirect(route('admin.learners.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'learnerbaru@kelasqu.id',
            'role' => 'user',
        ]);
    }

    public function test_admin_dashboard_shows_user_and_activity_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pengguna terbaru')
            ->assertSee('Aktivitas terbaru');
    }
}
