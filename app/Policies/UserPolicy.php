<?php

namespace App\Policies;

use App\Models\User;
use App\Services\RoleService;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class UserPolicy
{
    /**
     * Users with certain roles are restricted from accessing certain users with certain roles.
     */
    private array $restrictedRoles = [];

    /**
     * Class constructor.
     */
    public function __construct(RoleService $rolesService)
    {
        $this->restrictedRoles = $rolesService->getRestrictedRoles();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): Response
    {
        return $user->can('user_access')
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, User $model): Response
    {
        $isPartnerUser = boolval($user->roles->first()->for_partner);

        if ($model->hasAnyRole($this->restrictedRoles)) {
            return Response::denyAsNotFound();
        }

        return $user->can('user_access')
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): Response
    {
        return $user->can('user_create')
            ? Response::allow()
            : Response::deny(YOU_ARE_NOT_ALLOWED);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, User $model): Response
    {
        if ($model->hasAnyRole($this->restrictedRoles)) {
            return Response::deny(YOU_ARE_NOT_ALLOWED);
        }

        return $user->can('user_update')
            ? Response::allow()
            : Response::deny(YOU_ARE_NOT_ALLOWED);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, User $model): Response
    {
        if ($model->hasAnyRole($this->restrictedRoles)) {
            return Response::deny('You are not allowed to perform this action.');
        }

        return $user->can('user_delete')
            ? Response::allow()
            : Response::deny(YOU_ARE_NOT_ALLOWED);
    }

    /**
     * Determine whether the user can change the model users password.
     */
    public function password(Authenticatable $user, User $model): Response
    {
        if ($model->hasAnyRole($this->restrictedRoles)) {
            return Response::deny(YOU_ARE_NOT_ALLOWED);
        }

        return $user->can('user_password')
            ? Response::allow()
            : Response::deny(YOU_ARE_NOT_ALLOWED);
    }
}
