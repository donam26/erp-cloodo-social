<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoryController extends Controller
{
    public function index()
    {
        // Lấy stories của bạn bè và bản thân
        $stories = Story::query()
            ->with('author')
            ->where(function ($query) {
                // Stories của bản thân
                $query->where('user_id', Auth::id())
                    // Stories của bạn bè
                    ->orWhereIn('user_id', function ($subQuery) {
                        $subQuery->select('friend_id')
                            ->from('friends')
                            ->where('user_id', Auth::id())
                            ->where('status', 'accepted')
                            ->union(
                                DB::table('friends')
                                    ->select('user_id')
                                    ->where('friend_id', Auth::id())
                                    ->where('status', 'accepted')
                            );
                    });
            })
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(StoryResource::collection($stories));
    }

    public function store(Request $request)
    {
        logger($request->all());
        $data = $request->all();

        $story = Story::create([
            'user_id' => Auth::id(),
            'background' => $data['background'],
            'text' => $data['text'] ?? null,
            'expired_at' => now()->addHours(24) // Story hết hạn sau 24h
        ]);

        return $this->successResponse(
            new StoryResource($story),
            'Tạo story thành công',
            201
        );
    }

    public function delete(Story $story)
    {
        try {
            if ($story->user_id !== Auth::id()) {
                return $this->errorResponse('Bạn không có quyền xóa story này', 403);
            }

            $story->delete();
            return $this->successResponse(null, 'Xóa story thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Không thể xóa story: ' . $e->getMessage());
        }
    }
}
