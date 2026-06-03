<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default users
        User::create([
            'name' => 'Admin Laboratorium',
            'email' => 'admin@labtrack.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Baskara Mahasiswa',
            'email' => 'student@labtrack.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
        ]);
    }
}
