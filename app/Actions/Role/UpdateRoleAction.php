<?php

namespace App\Actions\Role;

use App\Enums\Permissions as PermissionsEnum;
use App\Services\RoleService;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class UpdateRoleAction
{
    public function __construct(protected RoleService $roleService)
    {
    }

    /**
     * Execute the action to update an existing role.
     */
    public function execute(Role $role, array $data, array $permissionNames): Role
    {
        // Only update name and for_partner if role is not locked
        if (! in_array($role->name, $this->roleService->getLockedRoles())) {
            $role->update([
                'name' => $data['name'],
                'for_partner' => $data['for_partner'] ?? false,
            ]);
        }

        $permissions = $this->filterValidPermissions($permissionNames);
        $queryPermissions = Permission::whereIn('name', $permissions)->get();

        $role->syncPermissions($queryPermissions);

        return $role->fresh();
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
