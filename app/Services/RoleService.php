<?php

namespace App\Services;

use App\Enums\Permissions as PermissionsEnum;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

/**
 * Deals with user roles and permissions related actions.
 */
class RoleService
{
    /**
     * Role name super admin for system user.
     */
    const SUPER_ADMIN = 'Super Admin';

    /**
     * Role name admin for for application stakeholder.
     */
    const ADMIN = 'Admin';

    /**
     * Role name Association for BGMEA, BKMEA and others.
     */
    const ASSOCIATION = 'Association';

    /**
     * Role name RMG for garments sector admin.
     */
    const RMG = 'RMG';

    /**
     * Role name manager for garments users.
     */
    const MANAGER = 'Manager';

    /**
     * Role name Guest for government and other NGO users.
     */
    const GUEST = 'Guest';

    /**
     * Returns admin role names as array.
     */
    public function getAdminRoles(): array
    {
        return [self::SUPER_ADMIN, self::ADMIN];
    }

    /**
     * Returns restricted role names for authenticated user.
     */
    public function getRestrictedRoles(): array
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return match (true) {
            $user->hasRole(self::SUPER_ADMIN) => [self::SUPER_ADMIN],
            $user->hasRole(self::RMG) => [self::SUPER_ADMIN, self::ADMIN, self::ASSOCIATION, self::GUEST],
            default => [self::SUPER_ADMIN, self::ADMIN],
        };
    }

    /**
     * Returns locked role names that are not allowed to be deleted.
     */
    public function getLockedRoles(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::ASSOCIATION,
            self::RMG,
            self::MANAGER,
            self::GUEST,
        ];
    }

    /**
     * Returns a collection of all roles.
     */
    public function getAll(): Collection
    {
        return Role::whereNotIn('name', $this->getRestrictedRoles())
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * Returns a collection of all roles.
     */
    public function paginate(int $per_page = 15): LengthAwarePaginator
    {
        return Role::whereNotIn('name', $this->getRestrictedRoles())
            ->orderBy('name', 'ASC')
            ->paginate($per_page);
    }

    /**
     * Searches and sorts roles based on query.
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $query = Role::whereNotIn('name', $this->getRestrictedRoles());

        if ($request->has('search') && $request->get('search')) {
            $query->where('name', 'LIKE', '%'.$request->get('search').'%');
        }

        if ($request->has('order_by') && $request->get('order_by')) {
            $direction = in_array($request->get('direction'), ['asc', 'desc']) ? Str::upper($request->get('direction')) : 'ASC';
            $query->orderBy($request->get('order_by'), $direction);
        } else {
            $query->orderBy('id', 'DESC');
        }

        $roles = $query->paginate($request->get('per_page'));

        $roles->getCollection()->transform(function ($role) {
            $role->deletable = in_array($role->name, $this->getLockedRoles()) ? false : true;

            return $role;
        });

        return $roles;
    }

    /**
     * Searches and sorts roles based on query.
     */
    public function filteredByLoggedInRole()
    {
        $user = auth()->user();

        $roles = Role::when($user->hasRole([self::RMG, self::MANAGER]), function ($q) {
                $q->whereIn('name', [self::RMG, self::MANAGER]);
            })
            ->when($user->hasRole([self::ADMIN, self::SUPER_ADMIN]), function ($q) {
                $q->whereIn('name', [self::MANAGER,self::RMG, self::ADMIN]);
            })
            ->select('id', 'name')
            ->orderBy('id', 'desc')->get();

        return $roles;
    }

    /**
     * Structures a roles permissions for PrimeVue tree view selection.
     */
    public function getRolesPermissions(Role $role): array
    {
        $role->load(['permissions' => fn ($q) => $q->select('name')]);
        $permissions = PermissionsEnum::withDetails();
        $rolePermissions = [];

        foreach ($role->permissions as $permission) {
            if (! array_key_exists($permission->name, $permissions)) {
                continue;
            }

            $module = $permissions[$permission->name]['value'];
            $group = $permissions[$permission->name]['group_slug'];

            if (! array_key_exists($module, $rolePermissions)) {
                $rolePermissions[$module] = [
                    'checked' => true,
                    'partialChecked' => false,
                ];
            }

            if ($group && ! array_key_exists($group, $rolePermissions)) {
                $rolePermissions[$group] = [
                    'checked' => true,
                    'partialChecked' => false,
                ];
            }

            $rolePermissions[$permission->name] = [
                'checked' => true,
                'partialChecked' => false,
            ];
        }

        return $rolePermissions;
    }
}
