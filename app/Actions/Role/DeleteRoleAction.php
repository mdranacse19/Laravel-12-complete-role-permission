<?php

namespace App\Actions\Role;

use App\Services\RoleService;
use App\Models\Role;

class DeleteRoleAction
{
    public function __construct(protected RoleService $roleService)
    {
    }

    /**
     * Execute the action to delete a role.
     */
    public function execute(Role $role): bool
    {
        // Check if role is locked
        if (in_array($role->name, $this->roleService->getLockedRoles())) {
            throw new \Exception('You are not allowed to delete this role.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            throw new \Exception('Please delete all the users of this role first and then try again.');
        }

        // Sync permissions to empty array before deletion
        $role->syncPermissions([]);

        return $role->delete();
    }
}
