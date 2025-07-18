<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;

class TaskPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $user->isAdmin() || $project->members()->where('user_id', $user->id)->exists();
    }

    public function view(User $user, Task $task): bool
    {
        return $this->canManage($user, $task);
    }

    public function create(User $user, Project $project): bool
    {
        return $user->isAdmin() || $this->isProjectOwner($user, $project);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->canManage($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin() || $this->isProjectOwner($user, $task->project);
    }

    protected function canManage(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isProjectOwner($user, $task->project)) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }

    protected function isProjectOwner(User $user, Project $project): bool
    {
        return $user->isManager() && $project->created_by === $user->id;
    }
}
