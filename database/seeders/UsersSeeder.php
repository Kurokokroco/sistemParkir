<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'administrator',
                'email'    => 'admin@parkir.test',
                'password' => 'admin123',
                'role'     => 'admin',
            ],
            [
                'name'     => 'petugas',
                'email'    => 'petugas@parkir.test',
                'password' => 'petugas123',
                'role'     => 'petugas',
            ],
            [
                'name'     => 'owner',
                'email'    => 'owner@parkir.test',
                'password' => 'owner123',
                'role'     => 'owner',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], 
                [
                    'name'     => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role'     => $user['role'],
                ]
            );
        }
    }
}