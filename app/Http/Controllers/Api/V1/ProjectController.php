<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\UserProjectRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::query()->paginate(10);

        return response()->json($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $projectData = $request->validated();

        $user = auth()->user();

        $project = $user->projects()->create($projectData);

        $user->joinedProjects()->attach($project);

        return response()->json($project);
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $projectData = $request->validated();

        $project->update($projectData);

        return response()->json($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted.'
        ]);
    }

    public function addUser(UserProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('addUser', $project);

        $userId = $request->validated('user_id');

        $project->members()->syncWithoutDetaching([$userId]);

        return response()->json([
            'message' => "User added to project."
        ]);
    }

    public function removeUser(Project $project, User $user): JsonResponse
    {
        $this->authorize('addUser', $project);

        $project->members()->detach($user->id);

        return response()->json([
            'message' => "User removed from project."
        ]);
    }
}
