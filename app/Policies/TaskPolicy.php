<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Task $task): bool
    {
        return $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Task $task): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->role === 'admin';
    }
}
