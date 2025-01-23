<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Luôn cho phép kết nối để test
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return true;
});
