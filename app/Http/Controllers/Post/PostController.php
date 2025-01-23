<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest\StoreRequest;
use App\Http\Requests\PostRequest\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->input('limit', 15);
        $posts = auth()->user()->newsFeed()->paginate($limit);
        return $this->successResponse(PostResource::collection($posts));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $post = Post::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filePath = $image->store('images', 'public');
                PostImage::create([
                    'post_id' => $post->id,
                    'image' => $filePath
                ]);
            }
        }

        return $this->successResponse(
                new PostResource($post),
            'Tạo bài viết thành công',
            201
        );
    }

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
}
