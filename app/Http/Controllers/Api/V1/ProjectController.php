<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\UserProjectRequest;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResource
    {
        $this->authorize('viewAny', Project::class);

        $user = auth()->user();

        $projects = Project::query()
            ->visibleTo($user)
            ->paginate(10);

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResource
    {
        $this->authorize('create', Project::class);

        $projectData = $request->validated();

        $user = auth()->user();

        $project = $user->projects()->create($projectData);

        $user->joinedProjects()->attach($project);

        return ProjectResource::make($project);
    }

    public function show(Project $project): JsonResource
    {
        $this->authorize('view', $project);

        return ProjectResource::make($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResource
    {
        $this->authorize('update', $project);

        $projectData = $request->validated();

        $project->update($projectData);

        return ProjectResource::make($project);
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
