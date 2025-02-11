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
        $userRelationship = Friend::where('user_id', Auth::id())
            ->where('friend_id', $user->id)
            ->first();

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

            if ($status === FriendStatus::Accepted->value) {
                if ($action === 'block') {
                    $userRelationship->update([
                        'status' => FriendStatus::Blocked->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::BlockRequest->value], 201);
                } else if ($action === 'cancel') {
                    $userRelationship->update([
                        'status' => FriendStatus::Cancel->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::CancelRequest->value], 201);
                }
            } else if ($status === FriendStatus::Pending->value) {
                if ($action === 'accept') {
                    $userRelationship->update([
                        'status' => FriendStatus::Accepted->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::AcceptRequest->value], 201);
                } else if ($action === 'cancel') {
                    $userRelationship->update([
                        'status' => FriendStatus::Cancel->value
                    ]);
                    return $this->successResponse(['message' => FriendStatus::CancelRequest->value], 201);
                }
            } else if ($status === FriendStatus::Blocked) {
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
        $suggestedFriends = auth()->user()->waitAccepts()->paginate($limit);
        return $this->successResponse(UserResource::collection($suggestedFriends));
    }
}
