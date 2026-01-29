<?php

namespace App\Providers;

use App\Models\Form\DynamicForm;
use App\Models\Profile\Stakeholder;
use App\Models\User;
use App\Policies\StakeholderPolicy;
use App\Models\Setup\AssociationType;
use App\Policies\AssociationTypePolicy;
use App\Policies\DynamicFormPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Services\RoleService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AssociationType::class => AssociationTypePolicy::class,
        DynamicForm::class => DynamicFormPolicy::class,
        Stakeholder::class => StakeholderPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * Implicitly grant "Super Admin" role all permission checks using can()
         *
         * @see https://laravel.com/docs/10.x/authorization#intercepting-gate-checks
         * @see https://spatie.be/index.php/docs/laravel-permission/v6/basic-usage/super-admin
         */
        Gate::before(function ($user, $ability) {
            return $user->hasRole(RoleService::SUPER_ADMIN) ? true : null;
        });
    }
}
