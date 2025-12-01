<?php

namespace App\Actions\Role;

use App\Enums\Permissions as PermissionsEnum;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class StoreRoleAction
{
    /**
     * Execute the action to store a new role.
     */
    public function execute(array $data, array $permissionNames): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'for_partner' => $data['for_partner'] ?? false,
        ]);

        $permissions = $this->filterValidPermissions($permissionNames);

        $role->givePermissionTo($permissions);

        return $role;
    }

    /**
     * Filter out invalid permissions (group slugs and enum values).
     */
    protected function filterValidPermissions(array $permissionNames): array
    {
        return collect($permissionNames)
            ->filter(function (string $key) {
                return ! in_array($key, PermissionsEnum::values())
                    && ! in_array($key, PermissionsEnum::groupSlugs());
            })
            ->toArray();
    }
}
