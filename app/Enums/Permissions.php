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
    | PROFILES
    |--------------------------------------------------------------------------
    */
    #[Module('Admin')]
    #[Group('Profiles')]
    #[Description('Permissions for managing admins profile.')]
    #[Abilities(['access', 'create', 'update', 'delete', 'bulk_action', 'export'])]
    case ADMIN = 'admin';

    #[Module('Association')]
    #[Group('Profiles')]
    #[Description('Permissions for managing associations profile.')]
    #[Abilities(['access', 'create', 'update', 'delete', 'bulk_action', 'export'])]
    case PARTNER = 'partner';

    #[Module('Stakeholder')]
    #[Group('Profiles')]
    #[Description('Permissions for managing stakeholders profile.')]
    #[Abilities(['access', 'create', 'update', 'delete', 'bulk_action', 'export'])]
    case STAKEHOLDER = 'stakeholder';

    #[Module('RMG')]
    #[Group('Profiles')]
    #[Description('Permissions for managing stakeholders profile.')]
    #[Abilities(['access', 'create', 'update', 'delete', 'bulk_action', 'export', 'import'])]
    case RMG = 'rmg';

    /*
    |--------------------------------------------------------------------------
    | PROGRAMS
    |--------------------------------------------------------------------------
    */

    #[Module('Training / Course')]
    #[Group('Programs')]
    #[Description('Permissions for managing trainings / courses.')]
    #[Abilities(['access', 'create', 'update', 'delete', 'attendance_sheet', 'attendance_update'])]
    case PROGRAMTRAINING = 'program_training';

    #[Module('Logbook')]
    #[Group('Programs')]
    #[Description('Permissions for managing learners logbook on training.')]
    #[Abilities(['access', 'create'])]
    case LOGBOOK = 'logbook';

    /*
    |--------------------------------------------------------------------------
    | MONITORING
    |--------------------------------------------------------------------------
    */
    #[Module('Monitoring')]
    #[Group('Monitoring & Action')]
    #[Description('Permissions for monitoring activities.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case MONITORING = 'monitoring';

    /*
    |--------------------------------------------------------------------------
    | FEEDBACK MODULE
    |--------------------------------------------------------------------------
    */
    #[Module('Feedback')]
    #[Description('Permissions for feedback management.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case FEEDBACK = 'feedback';

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE SETUP
    |--------------------------------------------------------------------------
    */
    #[Module('Association Type')]
    #[Group('Attribute Setup')]
    #[Description('Permissions for managing modalities.')]
    #[Abilities(['access', 'create', 'update', 'delete', 'bulk_action'])]
    case ASSOCIATION_TYPE = 'association_type';

    #[Module('Form Builder')]
    #[Group('Attribute Setup')]
    #[Description('Permissions for managing reasons for form builder.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case FORM_BUILDER = 'form_builder';

    /*
    |--------------------------------------------------------------------------
    | LOCATIONS
    |--------------------------------------------------------------------------
    */
    #[Module('Division')]
    #[Group('Locations')]
    #[Description('Permissions for managing divisions.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case DIVISION = 'division';

    #[Module('District')]
    #[Group('Locations')]
    #[Description('Permissions for managing districts.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case DISTRICT = 'district';

    /*
    |--------------------------------------------------------------------------
    | USERS & ROLES
    |--------------------------------------------------------------------------
    */
    #[Module('Users')]
    #[Description('Permissions for managing users.')]
    #[Abilities(['access', 'create', 'update', 'password', 'delete'])]
    case USER = 'user';

    #[Module('Roles')]
    #[Description('Permissions for managing user roles.')]
    #[Abilities(['access', 'create', 'update', 'delete'])]
    case ROLE = 'role';

    /*
    |--------------------------------------------------------------------------
    | REPORT
    |--------------------------------------------------------------------------
    */
    #[Module('Reports')]
    #[Description('Permissions for the reports.')]
    #[Abilities(['access', 'export'])]
    case REPORTS = 'report';

    /*
    |--------------------------------------------------------------------------
    | SETTINGS & CMS
    |--------------------------------------------------------------------------
    */
    #[Module('CMS Settings')]
    #[Group('Settings')]
    #[Description('Permissions for managing CMS.')]
    #[Abilities(['access', 'update', 'delete'])]
    case CMS = 'cms';

    #[Module('Application Settings')]
    #[Group('Settings')]
    #[Description('Permissions for the application settings.')]
    #[Abilities(['application', 'contact', 'media', 'mobile', 'social'])]
    case SETTING = 'setting';

    /*
    |--------------------------------------------------------------------------
    | SUPPORT MODULE
    |--------------------------------------------------------------------------
    */
    #[Module('Support')]
    #[Description('Permissions for support management.')]
    #[Abilities(['access', 'create', 'view', 'update', 'delete'])]
    case SUPPORT = 'support';

    /*
    |--------------------------------------------------------------------------
    | AUDIT MODULE
    |--------------------------------------------------------------------------
    */
    #[Module('Audit')]
    #[Description('Permissions for audit management.')]
    #[Abilities(['access', 'view'])]
    case AUDIT = 'audit';

    /**
     * Build a full permission string ("caseValue_ability").
     * Example: Permissions::ASSOCIATION_TYPE->permission('access') => "association_type_access".
     */
    public function permission(string $ability): string
    {
        return $this->value.'_'.$ability;
    }
}
