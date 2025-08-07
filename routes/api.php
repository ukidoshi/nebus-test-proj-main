<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\OrganizationActivityController;

// Организации
Route::get('/organizations/in-building', [OrganizationController::class, 'inBuilding']);
Route::get('/organizations/by-activity', [OrganizationController::class, 'byActivity']);
Route::get('/organizations/by-radius', [OrganizationController::class, 'byRadius']);
Route::get('/organizations/by-box', [OrganizationController::class, 'byBox']);
Route::get('/organizations/by-name', [OrganizationController::class, 'byName']);
Route::get('/organizations', [OrganizationController::class, 'index']);
Route::post('/organizations', [OrganizationController::class, 'store']);
Route::put('/organizations/{id}', [OrganizationController::class, 'update']);
Route::get('/organizations/{id}', [OrganizationController::class, 'show']);
Route::delete('/organizations/{id}', [OrganizationController::class, 'destroy']);

// Здания организации
Route::get('/buildings', [BuildingController::class, 'index']);
Route::post('/buildings', [BuildingController::class, 'store']);
Route::put('/buildings/{id}', [BuildingController::class, 'update']);
Route::delete('/buildings/{id}', [BuildingController::class, 'destroy']);

// Виды деятельности
Route::get('/activities', [ActivityController::class, 'index']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

// Привязка видов деятельности к организациям
Route::post('/organizations/{id}/activities/attach', [OrganizationActivityController::class, 'attach']);
Route::post('/organizations/{id}/activities/detach', [OrganizationActivityController::class, 'detach']);
