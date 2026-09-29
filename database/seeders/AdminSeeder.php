<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@hararepoly.ac.zw',
            'student_id' => null,
            'phone' => '+263 4 123 4567',
            'department' => 'ICT Services',
            'role' => 'admin',
            'password' => Hash::make('Admin@123'),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dr. Sarah Counselor',
            'email' => 'counselor@hararepoly.ac.zw',
            'student_id' => null,
            'phone' => '+263 4 123 4568',
            'department' => 'Student Affairs',
            'role' => 'advisor',
            'password' => Hash::make('Advisor@123'),
            'is_active' => true,
        ]);

        $this->command->info('Admin and advisor accounts created:');
        $this->command->info('  Admin: admin@hararepoly.ac.zw / Admin@123');
        $this->command->info('  Advisor: counselor@hararepoly.ac.zw / Advisor@123');
    }
}