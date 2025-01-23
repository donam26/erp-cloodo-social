<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Friend\FriendController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\GroupMemberController;
use App\Http\Controllers\Messenger\ConversationController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Story\StoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('social-login')->middleware('web')->group(function () {
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
});
Route::group(['middleware' => ['api']], function ($router) {
    // Route không cần auth
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register']);


    // Routes cần auth
    Route::group(['middleware' => 'auth:api'], function ($router) {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);

        // Route Post
        Route::prefix('posts')->group(function () {
            Route::get('/', [PostController::class, 'index']);
            Route::post('/', [PostController::class, 'store']);
            Route::delete('/{post}', [PostController::class, 'delete']);
            Route::get('/waitAccepts', [PostController::class, 'waitAccepts']);
            Route::post('/{id}/{action}', [PostController::class, 'request']);
        });

        // Route Friend
        Route::prefix('friends')->group(function () {
            Route::get('/', [FriendController::class, 'index']);
            Route::get('/suggests', [FriendController::class, 'suggests']);
            Route::get('/waitAccepts', [FriendController::class, 'waitAccepts']);
            Route::post('/{id}/{action}', [FriendController::class, 'request']);
        });

        // Route Group
        Route::prefix('groups')->group(function () {
            Route::post('/', [GroupController::class, 'store']);
            Route::put('/{group}', [GroupController::class, 'update']);
            Route::delete('/{group}', [GroupController::class, 'delete']);
            Route::get('/participated', [GroupController::class, 'participated']);
            Route::get('/suggested', [GroupController::class, 'suggested']);
            // Route::middleware(['auth', 'check.group.access'])->group(function () {
            Route::middleware(['check.group.access'])->group(function () {
                Route::get('/{group}', [GroupController::class, 'show']);
            });
        });

        // Route Group Member
        Route::prefix('group-members')->group(function () {
            Route::post('/{group}/join', [GroupMemberController::class, 'join']);
            Route::post('/{group}/leave', [GroupMemberController::class, 'leave']);
            Route::post('/{group}/invite', [GroupMemberController::class, 'invite']);
            Route::post('/{group}/accept', [GroupMemberController::class, 'accept']);
            Route::post('/{group}/reject', [GroupMemberController::class, 'reject']);
            Route::post('/{group}/remove', [GroupMemberController::class, 'remove']);
        });

        // Route Conversation
        Route::prefix('conversations')->group(function () {
            Route::get('/', [ConversationController::class, 'index']);
            Route::middleware(['check.messenger.access'])->group(function () {
                Route::get('/{conversation}', [ConversationController::class, 'detail']);
                Route::post('/', [ConversationController::class, 'store']);
                Route::put('/{conversation}', [ConversationController::class, 'update']);
                Route::delete('/{conversation}', [ConversationController::class, 'delete']);
            });
        });

        // Route Story
        Route::prefix('stories')->group(function () {
            Route::get('/', [StoryController::class, 'index']);
            Route::post('/', [StoryController::class, 'store']);
            Route::delete('/{story}', [StoryController::class, 'delete']);
        });
    });
});
