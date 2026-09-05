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
        User::factory()->admin()->create([
            'name' => 'የስርዓት አስተዳዳሪ',
            'email' => 'admin@maremiya.et',
        ]);

        User::factory()->user()->create([
            'name' => 'መደበኛ ተጠቃሚ',
            'email' => 'user@maremiya.et',
        ]);
    }
}
