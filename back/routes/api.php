<?php

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
//
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', [\App\Http\Controllers\UserController::class,'login']);
Route::post('upload/{id}', [\App\Http\Controllers\UploadController::class,'upload']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('base64/{photo}', [\App\Http\Controllers\UploadController::class,'base64']);
    Route::post('logout', [\App\Http\Controllers\UserController::class,'logout']);
    Route::post('me', [\App\Http\Controllers\UserController::class,'me']);
    Route::apiResource('cards', \App\Http\Controllers\CardController::class);
    Route::get('maxTarget', [\App\Http\Controllers\CardController::class,'maxTarget']);
    Route::apiResource('records', \App\Http\Controllers\RecordController::class);
    Route::post('history', [\App\Http\Controllers\RecordController::class,'history']);
    Route::post('query', [\App\Http\Controllers\RecordController::class,'query']);
    Route::post('queries', [\App\Http\Controllers\RecordController::class,'queries']);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::put('updatePassword/{user}', [\App\Http\Controllers\UserController::class,'updatePassword']);
    Route::apiResource('permissions', \App\Http\Controllers\PermissionController::class);
    Route::post('attach', [\App\Http\Controllers\PermissionController::class,'attach']);
});
