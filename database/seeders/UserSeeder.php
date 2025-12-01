<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RoleService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->createSuperAdmin();

        $this->createAdmins();
    }

    /**
     * Creates "Super Admin" user and assigns the role.
     */
    private function createSuperAdmin(): void
    {
        $role = Role::query()->where('name', RoleService::SUPER_ADMIN)->first();

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@alprtm.com',
        ]);

        $user->assignRole($role);
    }

    /**
     * Creates "Admin" users and assigns the role.
     */
    private function createAdmins(): void
    {
        $role = Role::query()->where('name', RoleService::ADMIN)->first();

        $admins = [
            [
                'name' => 'Admin',
                'email' => 'admin@alprtm.com',
            ],
        ];

        foreach ($admins as $admin) {
            $user = User::factory()->create($admin);

            $user->assignRole($role);
        }
    }
}
