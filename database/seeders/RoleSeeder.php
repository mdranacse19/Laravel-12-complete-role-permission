<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Services\RoleService;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::Insert([
            [
                'name' => RoleService::SUPER_ADMIN,
                'guard_name' => 'web',
                'deleteable' => 0,
                'hideable' => 0,
            ],
            [
                'name' => RoleService::ADMIN,
                'guard_name' => 'web',
                'deleteable' => 0,
                'hideable' => 0,
            ],
            [
                'name' => RoleService::ASSOCIATION,
                'guard_name' => 'web',
                'deleteable' => 0,
                'hideable' => 0,
            ],
            [
                'name' => RoleService::RMG,
                'guard_name' => 'web',
                'deleteable' => 0,
                'hideable' => 0,
            ],
            [
                'name' => RoleService::MANAGER,
                'guard_name' => 'web',
                'deleteable' => 0,
                'hideable' => 0,
            ],
            [
                'name' => RoleService::GUEST,
                'guard_name' => 'web',
                'deleteable' => 0,
                'hideable' => 0,
            ],
        ]);
    }
}
