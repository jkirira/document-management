<?php

use App\Http\Controllers\Api\v1\Admin\AccessRequestController;
use App\Http\Controllers\Api\v1\Admin\DepartmentsController;
use App\Http\Controllers\Api\v1\Admin\DocumentAccessController;
use App\Http\Controllers\Api\v1\Admin\DocumentAccessManagersController;
use App\Http\Controllers\Api\v1\Admin\DocumentsController;
use App\Http\Controllers\Api\v1\Admin\FoldersController;
use App\Http\Controllers\Api\v1\Admin\LoginController;
use App\Http\Controllers\Api\v1\Admin\RolesController;
use App\Http\Controllers\Api\v1\Admin\UserAccessController;
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
        Route::get('/documents/{id}', [DocumentsController::class, 'show']);
        Route::post('/documents/{id}', [DocumentsController::class, 'update']);
        Route::delete('/documents/{id}', [DocumentsController::class, 'destroy']);

        Route::get('/documents/{document}/access-managers', [DocumentAccessManagersController::class, 'index']);
        Route::post('/documents/{document}/access-managers', [DocumentAccessManagersController::class, 'store']);
        Route::get('/documents/{document}/access-managers/{id}', [DocumentAccessManagersController::class, 'show']);
        Route::delete('/documents/{document}/access-managers/{id}', [DocumentAccessManagersController::class, 'destroy']);

        Route::get('/documents/{document}/document-access', [DocumentAccessController::class, 'index']);
        Route::post('/documents/{document}/document-access', [DocumentAccessController::class, 'store']);
        Route::get('/documents/{document}/document-access/{access}', [DocumentAccessController::class, 'show']);
        Route::post('/documents/{document}/document-access/update', [DocumentAccessController::class, 'update']);
        Route::delete('/documents/{document}/document-access/{access}', [DocumentAccessController::class, 'destroy']);

        Route::get('/folders', [FoldersController::class, 'index']);
        Route::post('/folders', [FoldersController::class, 'store']);
        Route::get('/folders/{folder}', [FoldersController::class, 'show']);
        Route::post('/folders/{folder}', [FoldersController::class, 'update']);
        Route::delete('/folders/{folder}', [FoldersController::class, 'destroy']);

        Route::get('/documents/{document}/user-access', [UserAccessController::class, 'index']);
        Route::post('/documents/{document}/user-access', [UserAccessController::class, 'store']);
        Route::get('/documents/{document}/user-access/{access}', [UserAccessController::class, 'show']);
        Route::post('/documents/{document}/user-access/update', [UserAccessController::class, 'update']);
        Route::delete('/documents/{document}/user-access/{access}', [UserAccessController::class, 'destroy']);

        Route::get('/access-requests', [AccessRequestController::class, 'index']);
        Route::post('/access-requests', [AccessRequestController::class, 'store']);
        Route::get('/access-requests/{accessRequest}', [AccessRequestController::class, 'show']);
        Route::post('/access-requests/{accessRequest}', [AccessRequestController::class, 'update']);
        Route::delete('/access-requests/{accessRequest}', [AccessRequestController::class, 'destroy']);

    });



});
