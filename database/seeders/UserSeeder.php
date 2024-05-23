<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'username' => 'user',
            'password' => Hash::make('ministerio'),
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed para el usuario admin
        DB::table('users')->insert([
            'username' => 'admin',
            'password' => Hash::make('novellpty09.'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
