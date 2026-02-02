<?php

namespace App\Enums;

use App\Enums\Attributes\Abilities;
use App\Enums\Attributes\Description;
use App\Enums\Attributes\Group;
use App\Enums\Attributes\Module;
use App\Traits\Enum\Arrayable;
use App\Traits\Enum\PermissionAttributes;

/**
 * All the permissions for the application.
 *
 * Permission will be set as "caseValue_ability".
 */
enum Permissions: string
{
    use Arrayable, PermissionAttributes;

    /*
    |--------------------------------------------------------------------------
    | FORMS MODULE
    |--------------------------------------------------------------------------
    */
    #[Module('Form Builder')]
    #[Group('Forms')]
    #[Description('Permissions for managing dynamic forms.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case FORM_BUILDER = 'form_builder';

    /*
    |--------------------------------------------------------------------------
    | USERS & ROLES
    |--------------------------------------------------------------------------
    */
    #[Module('Users')]
    #[Group('User Management')]
    #[Description('Permissions for managing users.')]
    #[Abilities(['access', 'create', 'update', 'password', 'delete'])]
    case USER = 'user';

    #[Module('Roles')]
    #[Group('User Management')]
    #[Description('Permissions for managing user roles.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case ROLE = 'role';

    /**
     * Build a full permission string ("caseValue_ability").
     * Example: Permissions::FORM_BUILDER->permission('access') => "form_builder_access".
     */
    public function permission(string $ability): string
    {
        return $this->value.'_'.$ability;
    }
}
