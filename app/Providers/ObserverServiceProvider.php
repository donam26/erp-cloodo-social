<?php

namespace App\Providers;

use App\Models\Group;
use App\Models\Message;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Comment;
use App\Observers\GroupObserver;
use App\Observers\MessengerObserver;
use App\Observers\PostObserver;
use App\Observers\ReactionObserver;
use App\Observers\CommentObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Post::observe(PostObserver::class);
        Group::observe(GroupObserver::class);
        Message::observe(MessengerObserver::class);
        Reaction::observe(ReactionObserver::class);
        Comment::observe(CommentObserver::class);
    }
} 