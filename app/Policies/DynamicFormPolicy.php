<?php

namespace App\Policies;

use App\Services\RoleService;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Enums\Permissions;

class DynamicFormPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        // No debug output
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): Response
    {
        return $user->can(Permissions::FORM_BUILDER->permission('access'))
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user): Response
    {
        return $user->can(Permissions::FORM_BUILDER->permission('access'))
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

        return $user->can(Permissions::FORM_BUILDER->permission('create'))
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

        return $user->can(Permissions::FORM_BUILDER->permission('update'))
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

        return $user->can(Permissions::FORM_BUILDER->permission('delete'))
            ? Response::allow()
            : Response::deny('You are not allowed to perform this action.');
    }
}
