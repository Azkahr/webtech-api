<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => \Hash::make('password'),
            'dob' => date('Y-m-d'),
            'phone_number' => '08123333',
            'profile_picture' => '-',
            'role' => 'admin'
        ]);
    }
}
