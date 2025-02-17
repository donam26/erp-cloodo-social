<?php

namespace App\Http\Controllers\Livestream;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livestream\StoreRequest;
use App\Http\Resources\LivestreamResource;
use App\Models\Livestream;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utilities\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestreamController extends Controller
{
    public function index()
    {
        $livestreams = Livestream::liveActive()->get();
        return $this->successResponse(LivestreamResource::collection($livestreams));
    }

    public function show(Livestream $livestream)
    {
        return $this->successResponse(new LivestreamResource($livestream));
    }

    public function store(StoreRequest $request)
    {
        $imageUrl = ImageUploader::uploadBase64Image($request->image, 'livestreams');

        $livestream = Livestream::create([
            'image' => $imageUrl,
            'title' => $request->title,
            'description' => $request->description,
        ]);
        return $this->successResponse(new LivestreamResource($livestream));
    }

    public function join(Livestream $livestream)
    {
        $livestream->participants()->attach(auth()->user()->id);
        return $this->successResponse(new LivestreamResource($livestream));
    }

    public function end(Livestream $livestream)
    {
        $livestream->end_time = now();
        $livestream->save();
        return $this->successResponse(null, 'Livestream ended successfully');
    }
} 
