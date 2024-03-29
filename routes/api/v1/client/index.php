<?php

use App\Http\Controllers\Api\v1\Client\AccessRequestController;
use App\Http\Controllers\Api\v1\Client\UserCategoriesController;
use App\Http\Controllers\Api\v1\Client\DocumentAccessController;
use App\Http\Controllers\Api\v1\Client\DocumentAccessManagersController;
use App\Http\Controllers\Api\v1\Client\DocumentHistoryController;
use App\Http\Controllers\Api\v1\Client\DocumentsController;
use App\Http\Controllers\Api\v1\Client\FoldersController;
use App\Http\Controllers\Api\v1\Client\UserAccessController;
use App\Http\Controllers\Api\v1\Client\UsersController;
use Illuminate\Support\Facades\Route;


Route::namespace('Api/v1/Client')->group(function() {

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/users/{id}', [UsersController::class, 'show']);

        Route::get('/documents', [DocumentsController::class, 'index']);
        Route::post('/documents', [DocumentsController::class, 'store']);
        Route::get('/documents/{id}', [DocumentsController::class, 'show']);
        Route::post('/documents/{id}', [DocumentsController::class, 'update']);
        Route::delete('/documents/{id}', [DocumentsController::class, 'destroy']);

        Route::get('/documents/{id}/history', [DocumentHistoryController::class, 'show']);

        Route::get('/documents/{document}/access-managers', [DocumentAccessManagersController::class, 'index']);
        Route::post('/documents/{document}/access-managers', [DocumentAccessManagersController::class, 'store']);
        Route::get('/documents/{document}/access-managers/{id}', [DocumentAccessManagersController::class, 'show']);
        Route::delete('/documents/{document}/access-managers/{id}', [DocumentAccessManagersController::class, 'destroy']);

        Route::get('/documents/{document_id}/document-access', [DocumentAccessController::class, 'index']);
        Route::get('/document-access', [DocumentAccessController::class, 'index']);
        Route::get('/document-access/access-abilities', [DocumentAccessController::class, 'accessAbilities']);
        Route::post('/document-access', [DocumentAccessController::class, 'store']);
        Route::get('/document-access/{id}', [DocumentAccessController::class, 'show']);
        Route::post('/document-access/{id}', [DocumentAccessController::class, 'update']);
        Route::post('/document-access/{id}/revoke', [DocumentAccessController::class, 'revoke']);
        Route::delete('/document-access/{id}', [DocumentAccessController::class, 'destroy']);

        Route::post('/documents/{document}/document-access/update-access', [DocumentAccessController::class, 'updateDocumentAccess']);

        Route::get('/folders', [FoldersController::class, 'index']);
        Route::post('/folders', [FoldersController::class, 'store']);
        Route::get('/folders/{folder}', [FoldersController::class, 'show']);
        Route::post('/folders/{folder}', [FoldersController::class, 'update']);
        Route::delete('/folders/{folder}', [FoldersController::class, 'destroy']);

        Route::get('/folders/{folder}/content', [FoldersController::class, 'content']);
        Route::get('/folders/{folder}/breadcrumbs', [FoldersController::class, 'breadcrumbs']);

        Route::get('/categories', [UserCategoriesController::class, 'index']);
        Route::post('/categories', [UserCategoriesController::class, 'store']);
        Route::get('/categories/{id}', [UserCategoriesController::class, 'show']);
        Route::post('/categories/{id}', [UserCategoriesController::class, 'update']);
        Route::delete('/categories/{id}', [UserCategoriesController::class, 'destroy']);

//        Route::get('/document-user-access', [UserAccessController::class, 'index']);
//        Route::post('/document-user-access', [UserAccessController::class, 'store']);
//        Route::get('/document-user-access/{id}', [UserAccessController::class, 'show']);
//        Route::post('/document-user-access/{id}', [UserAccessController::class, 'update']);
//        Route::post('/document-user-access/{id}/revoke', [UserAccessController::class, 'revoke']);
//        Route::delete('/document-user-access/{id}', [UserAccessController::class, 'destroy']);
//
//        Route::get('/documents/{document}/user-access', [UserAccessController::class, 'userAccess']);

        Route::get('/access-requests', [AccessRequestController::class, 'index']);
        Route::post('/access-requests', [AccessRequestController::class, 'store']);
        Route::get('/access-requests/{accessRequest}', [AccessRequestController::class, 'show']);
        Route::post('/access-requests/{accessRequest}', [AccessRequestController::class, 'update']);
        Route::delete('/access-requests/{accessRequest}', [AccessRequestController::class, 'destroy']);
        Route::post('/access-requests/{accessRequest}/approve', [AccessRequestController::class, 'approve']);
        Route::post('/access-requests/{accessRequest}/reject', [AccessRequestController::class, 'reject']);

    });

});
