<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\IssueController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CommentController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/activate', [AuthController::class, 'activate']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // Projects API
        Route::apiResource('projects', ProjectController::class);

        // Issues API
        Route::get('/projects/{project}/issues', [IssueController::class, 'index']);
        Route::post('/projects/{project}/issues', [IssueController::class, 'store']);
        Route::get('/issues/{issue}', [IssueController::class, 'show']);
        Route::put('/issues/{issue}', [IssueController::class, 'update']);
        Route::patch('/issues/{issue}', [IssueController::class, 'update']);
        Route::delete('/issues/{issue}', [IssueController::class, 'destroy']);

        // Comments API
        Route::get('/issues/{issue}/comments', [CommentController::class, 'index']);
        Route::post('/issues/{issue}/comments', [CommentController::class, 'store']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

        // Attachment API
        Route::post('/issues/{issue}/attachments', [AttachmentController::class, 'store']);
        Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);
    });
});