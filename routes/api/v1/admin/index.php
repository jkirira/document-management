<?php

use App\Http\Controllers\Api\v1\Admin\LoginController;
use App\Http\Controllers\Api\v1\Admin\RolesController;
use App\Http\Controllers\Api\v1\Admin\UsersController;
use Illuminate\Http\Request;
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

    });



});
