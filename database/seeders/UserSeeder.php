<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'student'],
            [
                'password' => Hash::make('password'),
                'role' => UserRole::Student,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        );

        User::query()->updateOrCreate(
            ['username' => 'student_allweeks'],
            [
                'password' => Hash::make('password'),
                'role' => UserRole::Student,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now(),
            ],
        );

        User::query()->updateOrCreate(
            ['username' => 'student_jan1'],
            [
                'password' => Hash::make('password'),
                'role' => UserRole::Student,
                'created_at' => Carbon::parse('2026-01-01')->startOfDay(),
                'updated_at' => Carbon::now(),
            ],
        );

        User::query()->updateOrCreate(
            ['username' => 'teacher'],
            [
                'password' => Hash::make('password'),
                'role' => UserRole::Teacher,
            ],
        );

        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ],
        );
    }
}
