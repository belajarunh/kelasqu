<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
