<?php

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;

// Broadcasting routes không cần auth để test
Broadcast::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-echo/{conversation_id}', function ($conversation_id) {
    return view('test-echo', compact('conversation_id'));
});

// Route test gửi event
Route::get('/test-send/{conversation_id}', function ($conversation_id) {
    // Tạo message object mà không lưu vào database
    $message = new \stdClass();
    $message->uuid = Str::uuid();
    $message->conversation_id = $conversation_id;
    $message->content = 'Test message at ' . now();
    $message->sender_id = 1;
    $message->created_at = now();

    event(new MessageSent($message));

    return "Đã gửi event thành công! Hãy kiểm tra tab khác.";
});
