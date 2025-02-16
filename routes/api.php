<?php

use App\Http\Controllers\AgoraController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Friend\FriendController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\GroupMemberController;
use App\Http\Controllers\Messenger\ConversationController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Messenger\MessageController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\SearchController;
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
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);

        // Route Post
        Route::prefix('posts')->group(function () {
            Route::get('/', [PostController::class, 'index']);
            Route::get('/{post}', [PostController::class, 'show']);
            Route::post('/', [PostController::class, 'store']);
            Route::post('/{post}/react', [PostController::class, 'react']);
            Route::post('/{post}/comment', [PostController::class, 'comment']);
            Route::delete('/{post}', [PostController::class, 'delete']);
            Route::put('/{post}', [PostController::class, 'update']);
        });

        // Route Friend
        Route::prefix('friends')->group(function () {
            Route::get('/', [FriendController::class, 'index']);
            Route::get('/suggests', [FriendController::class, 'suggests']);
            Route::get('/waitAccepts', [FriendController::class, 'waitAccepts']);
            Route::post('/{user}/{action}', [FriendController::class, 'request']);
            Route::get('/search', [FriendController::class, 'search']);
        });

        // Route Group
        Route::prefix('groups')->group(function () {
            Route::get('/', [GroupController::class, 'index']);
            Route::post('/', [GroupController::class, 'store']);
            Route::put('/{group}', [GroupController::class, 'update']);
            Route::delete('/{group}', [GroupController::class, 'delete']);
            Route::get('/participated', [GroupController::class, 'participated']);
            Route::get('/suggested', [GroupController::class, 'suggested']);
            // Route::middleware(['check.group.access'])->group(function () {
                Route::get('/{group}', [GroupController::class, 'show']);
            // });
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

        Route::prefix('messages')->group(function () {
            Route::post('/', [MessageController::class, 'store']);
        });

        // Route Agora
        Route::prefix('agora')->group(function () {
            Route::post('/token', [AgoraController::class, 'generateToken']);
        });

        // Route Story
        Route::prefix('stories')->group(function () {
            Route::get('/', [StoryController::class, 'index']);
            Route::post('/', [StoryController::class, 'store']);
            Route::delete('/{story}', [StoryController::class, 'delete']);
        });

        // Route Notification
        // Route::prefix('notifications')->group(function () {
        //     Route::get('/', [NotificationController::class, 'index']);
        // });

        // Route Profile
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'index']);
            Route::get('/{user}', [ProfileController::class, 'show']);
            Route::get('/{user}/mutual-friends', [ProfileController::class, 'mutualFriends']);
        });

        Route::get('/search', [SearchController::class, 'search']);
    });
});
