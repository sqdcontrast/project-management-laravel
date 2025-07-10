<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::query()->paginate(10);

        return response()->json($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $projectData = $request->validated();

        $user = auth()->user();

        $project = $user->projects()->create($projectData);

        $user->joinedProjects()->attach($project);

        return response()->json($project);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $projectData = $request->validated();

        $project->update($projectData);

        return response()->json($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project deleted.'
        ]);
    }
}
