<?php

namespace App\Http\Middleware;

use App\Models\Conversation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMessengerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $conversation = $request->route('conversation');
        $isMember = Conversation::where('conversation_id', $conversation->id)
            ->where('user_id', auth()->user()->id)
            ->exists();

        if ($conversation->is_public || $isMember) {
            return $next($request);
        }

        // Người dùng không có quyền
        return response()->json([
            'status' => false,
            'message' => "Bạn không có quyền truy cập cuộc trò chuyện này"
        ], 403);
    }
}
