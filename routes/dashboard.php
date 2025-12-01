<?php

use App\Http\Controllers\Dashboard\Form\FormBuilderController;
use App\Http\Controllers\Dashboard\Setup\AssociationTypeController;
use App\Http\Controllers\Dashboard\User\RoleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
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

    // Setup management
    Route::prefix('setup')->name('setup.')->group(function () {
        Route::resource('association-type', AssociationTypeController::class)->except(['show'])->middleware(['can:association_type_access']);

        Route::get('form-builder/inputs', [FormBuilderController::class, 'inputs'])->name('form-builder.inputs');
        Route::resource('form-builder', FormBuilderController::class);
    });
});
