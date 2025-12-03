<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Actions\Role\DeleteRoleAction;
use App\Actions\Role\RestoreRoleAction;
use App\Actions\Role\StoreRoleAction;
use App\Actions\Role\UpdateRoleAction;
use App\Enums\Permissions as PermissionsEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, RoleService $service): Response
    {
        $roles = $service->search($request);

        $roles->getCollection()->transform(function ($role) use ($service) {
            $role->deletable = in_array($role->name, $service->getLockedRoles()) ? false : true;

            return $role;
        });

        return Inertia::render('dashboard/roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RoleService $service): Response
    {
        Gate::authorize('role_create');

        $abilities = userAbilities();
        $isSuperAdmin = auth()->user()->hasRole([RoleService::SUPER_ADMIN]);
        $allPermissions = PermissionsEnum::byPrimeVueGroup($abilities, $isSuperAdmin);


        return Inertia::render('dashboard/roles/Create', compact('allPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request, StoreRoleAction $action): RedirectResponse
    {
        $data = [
            'name' => $request->convertString('roleName'),
            'for_partner' => $request->forPartner ?? false,
        ];

        $permissionNames = array_keys($request->input('permissions', []));

        $role = $action->execute($data, $permissionNames);

        return redirect()
            ->route('roles.index')
            ->with('success', $role->name.' was created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role, RoleService $service): Response
    {
        Gate::authorize('role_update');

        abort_if(in_array($role->name, $service->getRestrictedRoles()), 401, 'You are not allowed to modify this role.');

        $role->name_editable = in_array($role->name, $service->getLockedRoles()) ? false : true;
        $role->forPartner = (bool) $role->for_partner;

        $rolePermissions = $service->getRolesPermissions($role);

        $abilities = userAbilities();
        $isSuperAdmin = auth()->user()->hasRole([RoleService::SUPER_ADMIN]);
        $allPermissions = PermissionsEnum::byPrimeVueGroup($abilities, $isSuperAdmin);

        return Inertia::render('dashboard/roles/Edit', [
            'role' => $role,
            'rolePermissions' => $rolePermissions,
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role, UpdateRoleAction $action, RoleService $service): RedirectResponse
    {
        abort_if(
            in_array($role->name, $service->getRestrictedRoles()),
            401,
            'You are not allowed to perform this action.'
        );

        $data = [
            'name' => $request->convertString('roleName'),
            'for_partner' => $request->forPartner ?? false,
        ];

        $permissionNames = array_keys($request->input('permissions', []));

        $role = $action->execute($role, $data, $permissionNames);

        return redirect()
            ->route('roles.index')
            ->with('success', $role->name.' was updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role, DeleteRoleAction $action, RoleService $service): RedirectResponse
    {
        Gate::authorize('role_delete');

        $roleName = $role->name;

        try {
            $action->execute($role);

            return redirect()
                ->back()
                ->with('success', $roleName.' was deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted role.
     */
    public function restore(int $id, RestoreRoleAction $action): RedirectResponse
    {
        Gate::authorize('role_update');

        try {
            $role = $action->execute($id);

            return redirect()
                ->back()
                ->with('success', $role->name.' was restored successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
