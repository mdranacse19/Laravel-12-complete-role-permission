<?php

namespace App\Policies;

use App\Models\Admin;
use App\Enums\Permissions;
use App\Services\RoleService;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): Response
    {
        return $user->can(Permissions::ADMIN->permission('access'))
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user): Response
    {
        return $user->can(Permissions::ADMIN->permission('access'))
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): Response
    {
        if ($user->hasAnyRole([RoleService::RMG, RoleService::MANAGER, RoleService::GUEST ])) {
            return Response::deny('You are not allowed to perform this action.');
        }

        return $user->can(Permissions::ADMIN->permission('create'))
            ? Response::allow()
            : Response::deny('You are not allowed to perform this action.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user): Response
    {
        if ($user->hasAnyRole([RoleService::RMG, RoleService::MANAGER, RoleService::GUEST ])) {
            return Response::deny('You are not allowed to perform this action.');
        }

        return $user->can(Permissions::ADMIN->permission('update'))
            ? Response::allow()
            : Response::deny('You are not allowed to perform this action.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user): Response
    {
        if ($user->hasAnyRole([RoleService::ASSOCIATION, RoleService::RMG, RoleService::MANAGER, RoleService::GUEST ])) {
            return Response::deny('You are not allowed to perform this action.');
        }

        return $user->can(Permissions::ADMIN->permission('delete'))
            ? Response::allow()
            : Response::deny('You are not allowed to perform this action.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authenticatable $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authenticatable $user, Admin $admin): bool
    {
        return false;
    }
}
