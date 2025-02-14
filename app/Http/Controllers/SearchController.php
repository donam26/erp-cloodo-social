<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type', 'all'); 
        $limit = $request->input('limit', 10);

        $results = [];

        if ($type === 'all' || $type === 'posts') {
            $posts = Post::search($query)
                ->query(function ($builder) {
                    $builder->whereIn('user_id', function ($query) {
                        $query->select('friend_id')
                            ->from('friends')
                            ->where('user_id', Auth::id())
                            ->where('status', 'accepted')
                            ->union(
                                DB::table('friends')
                                    ->select('user_id')
                                    ->where('friend_id', Auth::id())
                                    ->where('status', 'accepted')
                            );
                    })->orWhere('user_id', Auth::id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            $results['posts'] = [
                'total' => $posts->total(),
                'items' => PostResource::collection($posts),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage()
            ];
        }

        if ($type === 'all' || $type === 'users') {
            // Tìm kiếm người dùng
            $users = User::search($query)
                ->query(function($builder) {
                    $builder->where('id', '!=', Auth::id());
                })
                ->paginate($limit);

            $results['users'] = [
                'total' => $users->total(),
                'items' => UserResource::collection($users),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage()
            ];
        }

        return $this->successResponse($results);
    }
}
