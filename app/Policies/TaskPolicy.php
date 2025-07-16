<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $this->canManage($user, $task);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    public function update(User $user, Task $task): bool
    {
        return $this->canManage($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isManager() && $task->project->created_by === $user->id) {
            return true;
        }

        return false;
    }

    protected function canManage(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isManager() && $task->project->created_by === $user->id) {
            return true;
        }

        return $user->id === $task->assigned_to;
    }
}
