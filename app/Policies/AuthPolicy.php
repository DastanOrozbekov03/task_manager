<?php

namespace App\Policies;

use App\Models\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthPolicy
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
    public function view(Auth $auth, Auth $model): bool
    {
        return $auth->isAdmin() || $auth->id === $model->id;
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
    public function update(Auth $auth, Auth $model): bool
    {
        return $auth->isAdmin() || $auth->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Auth $auth, Auth $model): bool
    {
        return $auth->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
//    public function restore(Auth $auth, Auth $auth): bool
//    {
//        //
//    }
//
//    /**
//     * Determine whether the user can permanently delete the model.
//     */
//    public function forceDelete(Auth $auth, Auth $auth): bool
//    {
//        //
//    }
}
