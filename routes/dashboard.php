<?php

use App\Http\Controllers\Dashboard\Form\FormBuilderController;
use App\Http\Controllers\Dashboard\Profile\StakeholderController;
use App\Http\Controllers\Dashboard\Setup\AssociationTypeController;
use App\Http\Controllers\Dashboard\User\RoleController;
use App\Models\Profile\Stakeholder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\User\UserController;
use Laravel\Fortify\Features;

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Roles Management
    Route::middleware(['can:role_access'])->controller(RoleController::class)->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::post('roles/{id}/restore', 'restore')->name('roles.restore');
    });

      Route::resource('users', UserController::class);
    Route::prefix('users')
        ->middleware(['can:user_access'])
        ->controller(UserController::class)
        ->group(function () {
            Route::patch('{user}/password', 'password')->name('users.password');
        });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::resource('stakeholders', StakeholderController::class)->except(['create', 'show', 'edit'])->middleware(['can:stakeholder_access']);
    });

    // Setup management
    Route::prefix('setup')->name('setup.')->group(function () {
        Route::resource('association-type', AssociationTypeController::class)->except(['show'])->middleware(['can:association_type_access']);

        Route::get('dynamic-form/inputs', [FormBuilderController::class, 'inputs'])->name('dynamic-form.inputs');
        Route::resource('dynamic-form', FormBuilderController::class);
    });
});
