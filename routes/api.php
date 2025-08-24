<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\VerifyStaticApiKey;

Route::middleware(VerifyStaticApiKey::class)->group(function () {

    Route::prefix('organizations')->group(function () {
        Route::get('{organization}', [OrganizationController::class, 'show'])
            ->name('organizations.show');

        Route::get('search/name', [OrganizationController::class, 'searchByName'])
            ->name('organizations.search.name');

        Route::get('search/activity/{activity}', [OrganizationController::class, 'searchByActivity'])
            ->name('organizations.search.activity');

        Route::get('search/geo', [OrganizationController::class, 'searchByGeo'])
            ->name('organizations.search.geo');
    });

    Route::get('/buildings/{building}/organizations', [BuildingController::class, 'organizations'])->name('buildings.organizations');
    Route::get('/activities/{activity}/organizations', [ActivityController::class, 'organizations'])->name('activities.organizations');
});
