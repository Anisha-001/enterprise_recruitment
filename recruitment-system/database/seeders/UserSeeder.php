<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@enterprise.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => Hash::make('Password123!'),
                'phone' => '+1-555-0100',
                'is_admin' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // HR Admin
        $hrAdmin = User::firstOrCreate(
            ['email' => 'hr@enterprise.com'],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'password' => Hash::make('Password123!'),
                'phone' => '+1-555-0101',
                'is_admin' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $hrAdmin->assignRole('hr_admin');

        // Recruiter
        $recruiter = User::firstOrCreate(
            ['email' => 'recruiter@enterprise.com'],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'password' => Hash::make('Password123!'),
                'phone' => '+1-555-0102',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $recruiter->assignRole('recruiter');

        // Hiring Manager
        $hiringManager = User::firstOrCreate(
            ['email' => 'manager@enterprise.com'],
            [
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'password' => Hash::make('Password123!'),
                'phone' => '+1-555-0103',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $hiringManager->assignRole('hiring_manager');

        // Interviewer
        $interviewer = User::firstOrCreate(
            ['email' => 'interviewer@enterprise.com'],
            [
                'first_name' => 'David',
                'last_name' => 'Park',
                'password' => Hash::make('Password123!'),
                'phone' => '+1-555-0104',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $interviewer->assignRole('interviewer');

        $this->command->info('Users seeded successfully.');
        $this->command->info('Default passwords: Password123!');
    }
}
