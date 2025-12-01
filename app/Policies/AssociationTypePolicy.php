<?php

namespace App\Policies;

use App\Models\Setup\AssociationType;
use App\Models\User;
use App\Enums\Permissions;

class AssociationTypePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('access'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssociationType $associationType): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('access'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('create'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssociationType $associationType): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('update'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssociationType $associationType): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('delete'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssociationType $associationType): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('delete'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssociationType $associationType): bool
    {
        return $user->can(Permissions::ASSOCIATION_TYPE->permission('delete'));
    }
}
