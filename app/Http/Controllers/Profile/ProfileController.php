<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return $this->successResponse(new ProfileResource(Auth::user()));
    }

    public function show(User $user)
    {
        $currentUser = Auth::user();
        $friendStatus = null;
        
        if ($currentUser) {
            $friendRecord = $currentUser->checkFriend($user->id);
            if ($friendRecord) {
                if ($friendRecord->status === 'pending') {
                    if ($friendRecord->user_id === $currentUser->id) {
                        $friendStatus = 'pending_sent';
                    } else {
                        $friendStatus = 'pending';
                    }
                } else {
                    $friendStatus = $friendRecord->status;
                }
            }
        }

        $request = request();
        $request->merge(['friend_status' => $friendStatus]);

        return $this->successResponse(
            new ProfileResource($user),
        );
    }

    public function mutualFriends(Request $request, User $user)
    {
        $limit = $request->input('limit', 10);
        $currentUser = Auth::user();

        $mutualFriends = $currentUser->mutualFriends($user->id)->paginate($limit);

        return $this->successResponse([
            'total' => $mutualFriends->total(),
            'items' => UserResource::collection($mutualFriends),
            'current_page' => $mutualFriends->currentPage(),
            'last_page' => $mutualFriends->lastPage()
        ]);
    }
}
