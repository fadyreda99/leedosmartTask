<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TestHttpRequestFromJobController;
use App\Http\Controllers\TwoFactoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::middleware(['two_factory_verify'])->group(function (){
        Route::apiResource('/tags', TagController::class);
        Route::apiResource('/posts', PostController::class);
        Route::get('/posts/all/deleted', [PostController::class, 'deletedPosts']);
        Route::get('/posts/restore/{id}', [PostController::class, 'restoreFromDeletes']);
        Route::get('/stats', [StatsController::class, 'getStats']);
    });
    Route::post('/verify', [TwoFactoryController::class, 'store']);
});
Route::get('/test', [TestHttpRequestFromJobController::class, 'testHttpRequestFromJob']);
