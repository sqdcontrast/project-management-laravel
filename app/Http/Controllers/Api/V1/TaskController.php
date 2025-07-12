<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::query()->paginate(10);

        return response()->json($tasks);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $taskData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'exists:users,id']
        ]);

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

    public function update(Request $request, Task $task): JsonResponse
    {
        $taskData = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:to_do,in_progress,done'],
            'assigned_to' => ['nullable', 'exists:users,id']
        ]);

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
