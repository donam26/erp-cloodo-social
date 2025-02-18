<?php

namespace App\Http\Controllers\Post;

use App\Enums\ReactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest\StoreRequest;
use App\Http\Requests\PostRequest\UpdateRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\PostImage;
use App\Utilities\ImageUploader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewCommentNotification;
use Illuminate\Support\Facades\DB;

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
        if ($request->groupId && $request->groupId !== 'null') {
            $group = Group::where('uuid', $request->groupId)->first();
            if (!$group) {
                return $this->errorResponse('Nhóm không tồn tại');
            }
        }
        $post = Post::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'privacy' => $request->privacy,
            'group_id' => $group->id ?? null
        ]);

        if ($request->input('images')) {
            foreach ($request->input('images') as $image) {
                $url = ImageUploader::uploadBase64Image($image, 'posts');
                $post->images()->create([
                    'image' => $url,
                    'post_id' => $post->id
                ]);
            }
        }
        $post->load(['author', 'images', 'comments', 'reactions']);

        return $this->successResponse(
            new PostResource($post),
            'Tạo bài viết thành công'
        );
    }


    public function update(UpdateRequest $request, Post $post)
    {
        try {
            DB::beginTransaction();
            
            // Cập nhật thông tin bài viết
            $post->update($request->only(['content', 'privacy']));

            // Xử lý xóa ảnh cũ nếu có
            if ($request->input('removed_images')) {
                $removedImages = $request->input('removed_images');
                
                foreach ($removedImages as $imageId) {
                    $image = PostImage::where('uuid', $imageId)->first();
                    if ($image) {
                        Storage::disk('s3')->delete($image->image);
                        $image->delete();
                    }
                }
            }

            // Xử lý thêm ảnh mới
            if ($request->input('images')) {
                foreach ($request->input('images') as $image) {
                    $url = ImageUploader::uploadBase64Image($image, 'posts');
                    $post->images()->create([
                        'image' => $url,
                        'post_id' => $post->id
                    ]);
                }
            }
            // Load lại relationships
            $post->load(['author', 'images', 'comments', 'reactions']);

            DB::commit();

            return $this->successResponse(
                new PostResource($post),
                'Cập nhật bài viết thành công'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Không thể cập nhật bài viết: ' . $e->getMessage());
        }
    }

    public function delete(Post $post)
    {
        try {
            // Bắt đầu transaction
            DB::beginTransaction();

            // Xóa tất cả comments của bài viết
            $post->comments()->delete();

            // Xóa tất cả reactions của bài viết
            $post->reactions()->delete();

            // Xóa tất cả images của bài viết
            foreach ($post->images as $image) {
                // Xóa file ảnh từ storage
                Storage::disk('s3')->delete($image->image);
                // Xóa record trong database
                $image->delete();
            }

            // Xóa bài viết
            $post->delete();

            // Commit transaction
            DB::commit();

            return $this->successResponse(
                null,
                'Xóa bài viết thành công'
            );
        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();
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
                'user_id' => Auth::id(),
                'content' => $validated['content']
            ]);

            // Load relationships
            $comment->load('user');

            // Gửi notification cho chủ bài viết
            if ($post->user_id !== Auth::id()) {
                $post->author->notify(new NewCommentNotification($comment));
            }

            return $this->successResponse(
                new CommentResource($comment),
                'Đã thêm bình luận'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Không thể thêm bình luận: ' . $e->getMessage());
        }
    }
}
