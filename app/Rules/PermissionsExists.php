<?php

namespace App\Rules;

use App\Enums\Permissions as PermissionsEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionsExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $selectedPermissions = array_keys($value);
        $permissions = Permission::get()->pluck('name')->toArray();

        if (! is_array($value) || count($value) === 0) {
            $fail('Please select some permissions.');
        }

        foreach ($selectedPermissions as $permission) {
            // do nothing if the permission is a group name
            if (in_array($permission, PermissionsEnum::groupSlugs())) {
                continue;
            }

            // do nothing if the permission is a module name
            if (in_array($permission, PermissionsEnum::values())) {
                continue;
            }

            // validate if the provided permission is either a module name or a valid permission.
            if (! in_array($permission, $permissions)) {
                $fail('Invalid permission "'.Str::headline($permission).'" selected.');
            }
        }
    }
}
