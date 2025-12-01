<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // دروستکردنی بەکارهێنەرێکی تاقیکردنەوە
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );

        // دروستکردنی چەند بەکارهێنەری تر بۆ تاقیکردنەوە
        User::factory()->count(5)->create();

        $this->command->info('Test users created successfully!');
    }
}
