<?php

use App\Http\Controllers\Api\v1\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('v1')->group(function (){

    Route::post('/login', [LoginController::class, 'login'])->middleware('guest:sanctum');
    Route::get('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

    // admin routes
    Route::prefix('admin')->group(base_path('routes/api/v1/admin/index.php'));

    // client routes
    Route::group([], base_path('routes/api/v1/client/index.php'));

});
