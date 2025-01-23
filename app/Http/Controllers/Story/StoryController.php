<?php

namespace App\Http\Controllers\Story;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreRequest;
use App\Http\Resources\StoryResource;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index()
    {
        $stories = auth()->user()->stories()->get();
        return $this->successResponse(StoryResource::collection($stories));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $story = Story::create([
            'user_id' => auth()->user()->id,
            'image' => $data['image']
        ]);
        return $this->successResponse(new StoryResource($story), 'Tạo story thành công');
    }

    public function delete(Story $story)
    {
        $story->delete();
        return $this->successResponse(null, 'Xóa story thành công');
    }
}
