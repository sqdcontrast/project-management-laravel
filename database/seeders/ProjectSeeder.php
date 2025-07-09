<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(2)->create([
            'role' => 'manager',
        ]);

        $users->each(function ($user) {
            $projects = Project::factory(2)->create([
                'created_by' => $user->id
            ]);

            $user->joinedProjects()->attach($projects);
        });
    }
}
