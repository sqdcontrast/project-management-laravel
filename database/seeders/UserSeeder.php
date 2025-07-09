<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'member@mail.com',
            'role' => 'member',
        ]);

        User::factory()->create([
            'email' => 'manager@mail.com',
            'role' => 'manager',
        ]);

        User::factory()->create([
            'email' => 'admin@mail.com',
            'role' => 'admin',
        ]);
    }
}
