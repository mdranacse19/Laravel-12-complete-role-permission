<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Jobs\SendNewUserPassword;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $role = $request->input('role');
        $search = $request->input('search');
        $status = $request->input('status');
        $orderBy = $request->input('order_by') ?? 'id';
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = $request->integer('per_page', 15);

        $users = User::with(['roles:id,name'])
            ->where('id', '!=', auth()->id())
            ->when(!$user->isSuperAdmin(), fn($q) => $q->withoutRole(RoleService::SUPER_ADMIN))
            ->when($role, fn($q) => $q->whereHas(fn($q) => $q->where('id', $role)))
            ->when($search, fn($q) => $q->where(function ($s) use ($search) {
                $s->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('bn_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('roles', fn($r) => $r->where('name', 'LIKE', "%{$search}%"));
            }))
            ->orderBy($orderBy, $direction)
            ->paginate($perPage);

        $roles = $this->getRoles();

        return Inertia::render('dashboard/users/Index',
            compact('users', 'roles')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, User $user): RedirectResponse
    {
        $plainPassword = Str::random(12);
        $data = $request->getFormData();
        $data['password'] = Hash::make($plainPassword);

        $user->fill($data)->save();
        $user->assignRole($request->input('role'));


        SendNewUserPassword::dispatch($user->fresh(), $plainPassword);

        return to_route('users.index')->with('success', 'User account has been created successfully. The user will receive a welcome email with necessary information including the auto generated password for the account.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->getFormData());
        $user->syncRoles($request->get('role'));

        return to_route('users.index')->with('success', 'User account has been updated successfully.');
    }

    /**
     * Changes the specified users password.
     */
    public function password(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ]);
        $user->update(['password' => Hash::make($request->input('password'))]);

        return redirect()->back()->with('success', 'Password has been changed successfully! The user will receive an email containing the new password.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->syncRoles([]);
        $user->delete();

        return redirect()->back()->with('success', 'User account has been deleted successfully.');
    }

    private function getRoles(): Collection
    {
        return Role::query()
            ->select('id', 'name')
            ->where('name', '!=', RoleService::SUPER_ADMIN)
            ->when(request()->user()->isAdmin(), fn($query) => $query->where('name', '!=', RoleService::ADMIN))
            ->whereNot('hideable', true)
            ->where('id', '>', request()->user()->roles->map(fn($i) => $i->id)->first())
            ->orderBy('name', 'ASC')
            ->get();
    }
}
