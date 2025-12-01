<?php

namespace App\Actions\Role;

use App\Models\Role;

class RestoreRoleAction
{
    /**
     * Execute the action to restore a soft-deleted role.
     */
    public function execute(int $roleId): Role
    {
        $role = Role::withTrashed()->findOrFail($roleId);

        if (! $role->trashed()) {
            throw new \Exception('This role is not deleted.');
        }

        $role->restore();

        return $role->fresh();
    }
}
