<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('projects', ProjectController::class);
    Route::get('projects/{id}/tasks', [TaskController::class, 'indexByProject']);
    Route::post('projects/{id}/tasks', [TaskController::class, 'storeInProject']);

    Route::apiResource('tasks', TaskController::class)->except(['index', 'store']);
    Route::get('tasks/{id}/comments', [CommentController::class, 'indexByTask']);
    Route::post('tasks/{id}/comments', [CommentController::class, 'storeInTask']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);
});
