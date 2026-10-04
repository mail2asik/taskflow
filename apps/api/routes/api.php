<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\IssueController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\V1\ContactController;

/*
|--------------------------------------------------------------------------
| Broadcast Authentication Route
|--------------------------------------------------------------------------
|
| Registers the broadcast authentication endpoint under the /api/v1 prefix
| using Sanctum token authentication middleware.
|
*/
Broadcast::routes([
    'prefix' => 'v1',
    'middleware' => ['auth:sanctum'],
]);

Route::prefix('v1')->group(function () {
    // Public routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/activate', [AuthController::class, 'activate']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
    Route::post('/contact', [ContactController::class, 'store']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        Route::get('/users/search', [UserController::class, 'search']);

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
        Route::get('/issues/{issue}/attachments', [AttachmentController::class, 'index']);
        Route::post('/issues/{issue}/attachments', [AttachmentController::class, 'store']);
        Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download']);
        Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);
    });
});