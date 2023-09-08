<?php

use App\Http\Controllers\Api\v1\Admin\DepartmentsController;
use App\Http\Controllers\Api\v1\Admin\DocumentAccessManagersController;
use App\Http\Controllers\Api\v1\Admin\DocumentsController;
use App\Http\Controllers\Api\v1\Admin\LoginController;
use App\Http\Controllers\Api\v1\Admin\RolesController;
use App\Http\Controllers\Api\v1\Admin\UsersController;
use Illuminate\Support\Facades\Route;


Route::namespace('Api/v1/Admin')->group(function() {

    Route::post('/login', [LoginController::class, 'login'])->middleware('guest:sanctum');

    Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');


    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/users', [UsersController::class, 'index']);
        Route::post('/users', [UsersController::class, 'store']);
        Route::get('/users/{id}', [UsersController::class, 'show']);
        Route::post('/users/{id}', [UsersController::class, 'update']);
        Route::delete('/users/{id}', [UsersController::class, 'destroy']);

        Route::get('/roles', [RolesController::class, 'index']);
        Route::post('/roles', [RolesController::class, 'store']);
        Route::get('/roles/{id}', [RolesController::class, 'show']);
        Route::post('/roles/{id}', [RolesController::class, 'update']);
        Route::delete('/roles/{id}', [RolesController::class, 'destroy']);

        Route::get('/departments', [DepartmentsController::class, 'index']);
        Route::post('/departments', [DepartmentsController::class, 'store']);
        Route::get('/departments/{department}', [DepartmentsController::class, 'show']);
        Route::post('/departments/{department}', [DepartmentsController::class, 'update']);
        Route::delete('/departments/{department}', [DepartmentsController::class, 'destroy']);

        Route::get('/documents', [DocumentsController::class, 'index']);
        Route::post('/documents', [DocumentsController::class, 'store']);
        Route::get('/documents/{document}', [DocumentsController::class, 'show']);
        Route::post('/documents/{document}', [DocumentsController::class, 'update']);
        Route::delete('/documents/{document}', [DocumentsController::class, 'destroy']);

        Route::get('/documents/{document}/access-managers', [DocumentAccessManagersController::class, 'index']);
        Route::post('/documents/{document}/access-managers', [DocumentAccessManagersController::class, 'store']);
        Route::get('/documents/{document}/access-managers/{id}', [DocumentAccessManagersController::class, 'show']);
        Route::delete('/documents/{document}/access-managers/{id}', [DocumentAccessManagersController::class, 'destroy']);

    });



});
