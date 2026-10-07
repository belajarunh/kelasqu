<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@kelasqu.id')],
            [
                'name' => 'Admin KelasQu',
                'role' => 'admin',
                'password' => env('ADMIN_PASSWORD', 'KelasQuAdmin123!'),
            ],
        );

        User::firstOrCreate(
            ['email' => 'siswa@kelasqu.id'],
            [
                'name' => 'Nadia Putri',
                'role' => 'user',
                'password' => 'KelasQuUser123!',
            ],
        );
    }
}
