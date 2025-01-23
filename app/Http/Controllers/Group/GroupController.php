<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest\StoreRequest;
use App\Http\Requests\GroupRequest\UpdateRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function show(Group $group)
    {
        return $this->successResponse(new GroupResource($group));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        // Kiểm tra và xử lý file ảnh
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $filePath = $request->file('image')->store('images', 'public');
            $data['image'] = $filePath; 
        }

        $group = Group::create($data);
        return $this->successResponse(new GroupResource($group));
    }

    public function update(UpdateRequest $request, Group $group)
    {
        $data = $request->validated();
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $filePath = $request->file('image')->store('images', 'public');
            $data['image'] = $filePath; 
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
        // $participatedGroupIds = GroupMember::where('user_id', auth()->user()->id)->pluck('group_id')->toArray();

        // $suggestedGroups = Group::whereIn('id', $participatedGroupIds)->get();
        // return GroupResource::collection($suggestedGroups);
    }

    public function suggested()
    {
        // $participatedGroupIds = GroupMember::where('user_id', auth()->user()->id)->pluck('group_id')->toArray();

        // $suggestedGroups = Group::whereNotIn('id', $participatedGroupIds)->get();
        // return GroupResource::collection($suggestedGroups);
    }
}
