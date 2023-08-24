<?php

use App\Http\Controllers\Api\v1\Admin\LoginController;
use App\Http\Controllers\Api\v1\Admin\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::namespace('Api/v1/Admin')->group(function() {

    Route::post('/login', [LoginController::class, 'login'])->middleware('guest:sanctum');

    Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');


    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/users', [UsersController::class, 'index']);

    });



});
