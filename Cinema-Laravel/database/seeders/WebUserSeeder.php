<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class WebUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default web user for booking system
        User::updateOrCreate(
            ['email' => 'web@cinema.com'],
            [
                'name' => 'Web Booking User',
                'email' => 'web@cinema.com',
                'password' => Hash::make('web123456'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Web booking user created successfully!');
    }
}
