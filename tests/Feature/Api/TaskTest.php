<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_task(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $project = Project::factory()->create();

        Sanctum::actingAs($admin);

        $taskData = [
            'title' => 'test task',
            'description' => 'test description',
            'project_id' => $project->id,
        ];

        $this->postJson(route('projects.tasks.store', $project->id), $taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_admin_can_edit_task(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $task = Task::factory()->create();

        Sanctum::actingAs($admin);

        $taskData = [
            'title' => 'test task',
            'description' => 'test description',
            'status' => 'in_progress',
        ];

        $this->putJson(route('tasks.update', $task), $taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_admin_can_get_tasks(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $project = Project::factory()->create();

        Task::factory(3)->create([
            'project_id' => $project->id,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(route('projects.tasks.index', $project->id));
        $response->assertOk();
    }

    public function test_admin_can_delete_task(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $task = Task::factory()->create();

        Sanctum::actingAs($admin);

        $this->deleteJson(route('tasks.destroy', $task));

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }

    public function test_admin_can_assign_task_to_project_member(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $project = Project::factory()->create();
        $project->members()->attach($user);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'assigned_to' => null,
        ]);

        Sanctum::actingAs($admin);

        $taskData = [
            'title' => 'test task',
            'description' => 'test description',
            'status' => 'in_progress',
            'assigned_to' => $user->id,
        ];

        $this->putJson(route('tasks.update', $task), $taskData);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    public function test_admin_cannot_assign_task_to_non_project_member(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'role' => 'member',
        ]);

        $task = Task::factory()->create([
            'assigned_to' => null,
        ]);

        Sanctum::actingAs($admin);

        $taskData = [
            'title' => 'test task',
            'description' => 'test description',
            'status' => 'in_progress',
            'assigned_to' => $user->id,
        ];

        $response = $this->putJson(route('tasks.update', $task), $taskData);

        $response->assertUnprocessable();

        $this->assertDatabaseMissing('tasks', $taskData);
    }
}
