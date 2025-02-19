<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest\StoreRequest;
use App\Http\Requests\GroupRequest\UpdateRequest;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupMemberController extends Controller
{
    public function join(Group $group)
    {
        if($group->admin_id == auth()->user()->id) {
            return $this->errorResponse('Bạn không thể tham gia vào nhóm của chính mình');
        }
        if(GroupMember::where('group_id', $group->id)->where('user_id', auth()->user()->id)->exists()) {
            return $this->errorResponse('Bạn đã tham gia vào nhóm này');
        }
        $group->members()->create([
            'user_id' => auth()->user()->id,
        ]);
        return $this->successResponse(null, 'Đã tham gia nhóm');
    }

    public function leave(Group $group)
    {
        $groupMember = GroupMember::where('group_id', $group->id)->where('user_id', auth()->user()->id)->first();
        $groupMember->delete();
        return $this->successResponse('Thoát nhóm thành công');
    }

    public function invite(Group $group)
    {
        if(GroupMember::where('group_id', $group->id)->where('user_id', auth()->user()->id)->exists()) {
            return $this->errorResponse('Bạn đã mời người vào nhóm này');
        }
        $groupMember = GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->user()->id,
        ]);
        return $this->successResponse(new GroupMemberResource($groupMember));
    }

    public function accept(Group $group)
    {
        if($group->admin_id == auth()->user()->id) {
            return $this->errorResponse('Bạn không thể chấp nhận lời mời của chính mình');
        }
        $groupMember = GroupMember::where('group_id', $group->id)->where('user_id', auth()->user()->id)->first();
        $groupMember->update(['status' => 'accepted']);
        return $this->successResponse('Chấp nhận lời mời thành công');
    }

    public function reject(Group $group)
    {
        $groupMember = GroupMember::where('group_id', $group->id)->where('user_id', auth()->user()->id)->first();
        $groupMember->update(['status' => 'rejected']);
        return $this->successResponse('Từ chối lời mời thành công');
    }   

    public function remove(Group $group)
    {
        $groupMember = GroupMember::where('group_id', $group->id)->where('user_id', auth()->user()->id)->first();
        $groupMember->delete();
        return $this->successResponse('Xóa thành viên thành công');
    }
}
