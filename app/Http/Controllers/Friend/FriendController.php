<?php

namespace App\Http\Controllers\Friend;

use App\Enums\FriendStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Friend;
use App\Models\User;
use App\Notifications\RequestFriend;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $friends = Auth::user()->friends()->paginate($limit);
        return $this->successResponse(UserResource::collection($friends));
    }

    public function request(User $user, $action)
    {
        // Kiểm tra cả 2 chiều của mối quan hệ
        $userRelationship = Friend::where(function($query) use ($user) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', Auth::id())
                  ->where('friend_id', $user->id);
            })->orWhere(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('friend_id', Auth::id());
            });
        })->first();

        if (!$userRelationship) {
            if ($action === 'request') {
                Friend::create([
                    'user_id' => Auth::id(),
                    'friend_id' => $user->id,
                    'status' => FriendStatus::Pending->value,
                ]);
                Auth::user()->notify(new RequestFriend($user->id));
                return $this->successResponse(['message' => FriendStatus::SendRequest->value], 201);
            }
        } else {
            $status = $userRelationship->status;
            $isSender = $userRelationship->user_id === Auth::id();

            if ($status === FriendStatus::Accepted->value) {
                if ($action === 'block') {
                    $userRelationship->update([
                        'status' => FriendStatus::Blocked->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::BlockRequest->value], 201);
                } else if ($action === 'cancel') {
                    $userRelationship->delete();
                    return $this->successResponse(['message' => FriendStatus::CancelRequest->value], 201);
                }
            } else if ($status === FriendStatus::Pending->value) {
                if (!$isSender && $action === 'accept') {
                    // Chỉ người nhận mới có thể accept
                    $userRelationship->update([
                        'status' => FriendStatus::Accepted->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::AcceptRequest->value], 201);
                } else if ($action === 'cancel') {
                    // Cả người gửi và nhận đều có thể cancel
                    $userRelationship->delete();
                    return $this->successResponse(['message' => FriendStatus::CancelRequest->value], 201);
                }
            } else if ($status === FriendStatus::Blocked->value) {
                if ($action === 'unblock') {
                    $userRelationship->update([
                        'status' => FriendStatus::Accepted->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::UnblockRequest->value], 201);
                }
            }
        }

        return $this->errorResponse(['message' => 'Invalid action'], 400);
    }

    public function suggests()
    {
        $suggestedFriends = auth()->user()->suggests()->paginate(3);
        return $this->successResponse(UserResource::collection($suggestedFriends));
    }

    public function waitAccepts(Request $request)
    {
        $limit = $request->input('limit', 10);
        $waitAccepts = auth()->user()->waitAccepts()->paginate($limit);
        return $this->successResponse(UserResource::collection($waitAccepts));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('name', 'like', '%' . $query . '%')->get();
        return $this->successResponse(UserResource::collection($users));
    }
}
