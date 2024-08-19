<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Todo\Entities\User;
use Modules\Todo\Http\Controllers\AuthController;
use Modules\Todo\Http\Controllers\CategoryController;
use Modules\Todo\Http\Controllers\LikeTaskController;
use Modules\Todo\Http\Controllers\PasswordResetController;
use Modules\Todo\Http\Controllers\SearchController;
use Modules\Todo\Http\Controllers\TaskController;
use Modules\Todo\Http\Controllers\UserController;
use Modules\Todo\Http\Controllers\WorkspaceController;





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
    Route::fallback([TaskController::class, 'fallback']);
    Route::get('/search', [SearchController::class, 'index'])->middleware('auth:sanctum');
    Route::post('like/{workspace:name}', [LikeTaskController::class, 'index'])->middleware('auth:sanctum'); 

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::delete('/logout/{id}', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);


    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['prefix' => '/users'], function () {
            Route::get('', [UserController::class, 'index']);
            Route::get('/{id}', [UserController::class, 'show']);
            Route::put('/{id}', [UserController::class, 'update']);
        });
    
        Route::group(['prefix' => '/tasks'], function () {
            Route::get('', [TaskController::class, 'index']);
            Route::get('/{id}', [TaskController::class, 'show']);
            Route::post('', [TaskController::class, 'store']);
            Route::put('/{id}', [TaskController::class, 'update']);
            Route::delete('/{id}', [TaskController::class, 'destroy']);
        });
    
        Route::group(['prefix' => '/workspace'] ,function () {
            Route::get('', [WorkspaceController::class, 'index']);
            Route::get('/{id}', [WorkspaceController::class, 'show']);
            Route::post('', [WorkspaceController::class, 'store']);
            Route::put('/{id}', [WorkspaceController::class, 'update']);
            Route::delete('/{id}', [WorkspaceController::class, 'destroy']);
        });
    
        Route::group(['prefix' => 'category'], function () {
            Route::get('', [CategoryController::class, 'index']);
            Route::get('/{id}', [CategoryController::class, 'show']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
            Route::post('', [CategoryController::class, 'store']);
            Route::put('/{id}', [CategoryController::class, 'update']);
        });

    });

