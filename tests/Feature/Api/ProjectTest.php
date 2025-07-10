<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_project(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($admin);

        $projectData = [
            'name' => 'test name',
            'description' => 'test description',
        ];

        $this->postJson(route('projects.store'), $projectData);

        $this->assertDatabaseHas('projects', $projectData);
    }

    public function test_admin_can_edit_project(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $project = Project::factory()->create();

        Sanctum::actingAs($admin);

        $projectData = [
            'name' => 'test name',
            'description' => 'test description',
            'status' => 'completed',
        ];

        $this->putJson(route('projects.update', $project), $projectData);

        $this->assertDatabaseHas('projects', $projectData);
    }

    public function test_admin_can_get_projects(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Project::factory(3)->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson(route('projects.index'));
        $response->assertOk();
    }

    public function test_admin_can_delete_project(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $project = Project::factory()->create();

        Sanctum::actingAs($admin);

        $this->deleteJson(route('projects.destroy', $project));

        $this->assertDatabaseMissing('projects', $project->toArray());
    }
}
