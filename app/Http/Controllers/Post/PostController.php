<?php

namespace App\Http\Controllers\Post;

use App\Enums\ReactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest\StoreRequest;
use App\Http\Requests\PostRequest\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 15);
        $posts = Auth::user()->newsFeed()->paginate($limit);
        return $this->successResponse(PostResource::collection($posts), 'Lấy bài viết thành công');
    }

    public function show(Post $post)
    {
        $post->load(['author', 'comments', 'reactions', 'images']);
        return $this->successResponse(new PostResource($post), 'Lấy bài viết thành công');
    }

    public function store(Request $request)
    {
       
        $file = $request->file('image');

        $fileName = time() . '_' . $file->getClientOriginalName();

        // Lưu file lên S3
        $path = Storage::disk('s3')->put('posts', $file);

        // Lấy URL file trên S3
        $url = Storage::disk('s3')->url($path);

        return response()->json([
            'message' => 'Upload thành công!',
            'url' => $url,
        ]);
    }

    // public function store(StoreRequest $request)
    // {
    //     $data = $request->validated();
    //     $post = Post::create($data);

    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $filePath = $image->store('images', 'public');
    //             PostImage::create([
    //                 'post_id' => $post->id,
    //                 'image' => $filePath
    //             ]);
    //         }
    //     }

    //     return $this->successResponse(
    //             new PostResource($post),
    //         'Tạo bài viết thành công',
    //         201
    //     );
    // }

    public function update(UpdateRequest $request, Post $post)
    {
        try {
            $data = $request->validated();
            $post->update($data);
            return $this->successResponse(
                new PostResource($post),
                'Cập nhật bài viết thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Không thể cập nhật bài viết: ' . $e->getMessage());
        }
    }

    public function delete(Post $post)
    {
        try {
            $post->delete();
            return $this->successResponse(
                null,
                'Xóa bài viết thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Không thể xóa bài viết: ' . $e->getMessage());
        }
    }

    public function react(Request $request, Post $post)
    {
        try {
            $user = Auth::user();
            $reaction = $post->reactions()->where('user_id', $user->id)->first();

            if ($reaction) {
                $reaction->delete();
                $message = 'Đã bỏ reaction';
            } else {
                $post->reactions()->create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'type' => $request->input('type', ReactionType::Like)
                ]);
                $message = 'Đã thêm reaction';
            }

            return $this->successResponse(
                new PostResource($post),
                $message
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Không thể thực hiện reaction: ' . $e->getMessage());
        }
    }

    public function comment(Request $request, Post $post)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:1000'
            ]);

            $comment = $post->comments()->create([
                'post_id' => $post->id,
                'content' => $validated['content']
            ]);

            return $this->successResponse(
                new CommentResource($comment),
                'Đã thêm bình luận'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Không thể thêm bình luận: ' . $e->getMessage());
        }
    }
}
