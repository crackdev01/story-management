<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => 'password',
            'role'     => 'Admin',
        ]);

        User::create([
            'name'     => 'User',
            'email'    => 'user@example.com',
            'password' => 'password',
            'role'     => 'User',
        ]);
    }
}
