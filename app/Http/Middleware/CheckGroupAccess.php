<?php

namespace App\Http\Middleware;

use App\Models\GroupMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->user()->id)
            ->exists();

        if ($group->is_public || $isMember) {
            return $next($request);
        }

        // Người dùng không có quyền
        return response()->json([
            'status' => false,
            'message' => "Bạn không có quyền truy cập nhóm này"
        ], 403);
    }
}
