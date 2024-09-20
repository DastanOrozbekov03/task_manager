<?php

namespace App\Policies;

use App\Models\Auth;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    use HandlesAuthorization;
    public function viewAny(Auth $auth): bool
    {
        return $auth->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Auth $auth, Task $task): bool
    {

        return $auth->isAdmin() || $auth->id === $task->auth_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Auth $auth): bool
    {
        return $auth->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Auth $auth, Task $task): bool
    {
        return $auth->id === $task->auth_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Auth $auth, Task $task): bool
    {
        \Log::info('Auth ID: ' . $auth->id . ', Task Auth ID: ' . $task->auth_id);
        return $auth->isAdmin() || $auth->id === $task->auth_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
//    public function restore(Auth $auth, Task $task): bool
//    {
//        //
//    }

    /**
     * Determine whether the user can permanently delete the model.
     */
//    public function forceDelete(Auth $auth, Task $task): bool
//    {
//        //
//    }
}
