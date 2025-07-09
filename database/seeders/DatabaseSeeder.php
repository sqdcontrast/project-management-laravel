<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(2)->create();

        $users->each(function ($user) {
            $projects = Project::factory(2)->create([
                'created_by' => $user->id,
            ]);

            $user->joinedProjects()->attach($projects);

            $projects->each(function ($project) {

                $tasks = Task::factory(3)->create([
                    'project_id' => $project->id,
                ]);

                $tasks->each(function ($task) use ($project) {
                    $project->members()->attach($task->assignedUser);
                });
            });
        });
    }
}
