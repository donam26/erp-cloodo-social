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
        $friendStatus = $currentUser->checkFriend($user->id);
        
        return $this->successResponse([
            'profile' => new ProfileResource($user),
            'friend_status' => $friendStatus ? $friendStatus->status : null,
            'is_self' => $currentUser->id === $user->id
        ]);
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