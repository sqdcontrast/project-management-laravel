<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::query()->paginate(10);

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $taskData = $request->validated();

        $assignedId = $request->input('assigned_to');

        $canAssign = $assignedId
            ? $project->members()->where('user_id', $assignedId)->exists()
            : true;

        if (!$canAssign) {
            return response()->json([
                'error' => 'Assigned user is not member of this project.'
            ], 422);
        }

        $task = $project->tasks()->create($taskData);

        return response()->json($task);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $taskData = $request->validated();

        $assignedId = $request->input('assigned_to');

        $canAssign = $assignedId
            ? $task->project->members()->where('user_id', $assignedId)->exists()
            : true;

        if (!$canAssign) {
            return response()->json([
                'error' => 'Assigned user is not member of this project.'
            ], 422);
        }

        $task->update($taskData);

        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task deleted.'
        ]);
    }
    
}
