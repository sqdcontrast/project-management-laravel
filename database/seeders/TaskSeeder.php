<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        $projects->each(function ($project) {

            $tasks = Task::factory(2)->create([
                'project_id' => $project->id,
            ]);

            $tasks->each(function ($task) use ($project) {
                $project->members()->attach($task->assignedUser);
            });
        });
    }
}
