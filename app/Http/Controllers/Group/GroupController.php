<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest\StoreRequest;
use App\Http\Requests\GroupRequest\UpdateRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Utilities\ImageUploader;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\GroupDetailResource;
use App\Http\Resources\UserResource;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::latest()->paginate(10);
        return $this->successResponse(GroupResource::collection($groups));
    }

    public function show(Group $group)
    {
        $group->loadCount('members');
        $group->load([
            'posts' => function ($query) {
                $query->latest()->limit(15);
            }
        ]);
        
        return $this->successResponse(new GroupDetailResource($group));
    }

    public function store(StoreRequest $request)
    {
        // Upload ảnh và lấy URL
        $imageUrl = ImageUploader::uploadBase64Image($request->image, 'groups');

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imageUrl,
            'status' => $request->status,
        ]);

        return $this->successResponse(new GroupResource($group));
    }

    public function update(UpdateRequest $request, Group $group)
    {
        $data = $request->validated();

        if ($request->has('image')) {
            $data['image'] = ImageUploader::uploadBase64Image($request->image, 'groups');
        }

        $group->update($data);
        return $this->successResponse(new GroupResource($group));
    }

    public function delete(Group $group)
    {
        $group->delete();
        return $this->successResponse('Xóa nhóm thành công');
    }

    public function participated()
    {
        $groups = Group::whereHas('members', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            GroupResource::collection($groups)
        );
    }

    public function manage()
    {
        $groups = Group::where('admin_id', Auth::id())
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            GroupResource::collection($groups)
        );
    }

    public function suggested()
    {
        $groups = Group::whereDoesntHave('members', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->latest()
            ->paginate(10);

        return $this->successResponse(
            GroupResource::collection($groups)
                ->additional([
                    'meta' => [
                        'total' => $groups->total(),
                        'page' => $groups->currentPage(),
                        'last_page' => $groups->lastPage()
                    ]
                ])
        );
    }

    public function members(Group $group)
    {
        $users = $group->members()->paginate(20);
        $group->load('admin');
        return $this->successResponse(
            UserResource::collection($users)
        );
    }
}
