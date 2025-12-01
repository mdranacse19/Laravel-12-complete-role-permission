<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Permissions as PermissionsEnum;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Schema::enableForeignKeyConstraints();

        $array = [];

        foreach (PermissionsEnum::all() as $permission) {
            $array[] = [
                'name' => $permission,
                'guard_name' => 'web',
            ];
        }

        $permission = Permission::insert($array);
    }
}
