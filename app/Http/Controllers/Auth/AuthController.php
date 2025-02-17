<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest\RegisterRequest;
use App\Http\Requests\AuthRequest\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use App\Utilities\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->errorResponse([
                'Unauthorized',
            ], 401);
        }
        return $this->successResponse(LoginResource::make($this->respondWithToken($token)), 'Login successfully');
    }

    public function me()
    {
        $token = JWTAuth::fromUser(Auth::user());
        return $this->successResponse(LoginResource::make($this->respondWithToken($token)), 'Data user');
    }

    public function refresh()
    {   
        return $this->successResponse(LoginResource::make($this->respondWithToken(auth()->refresh())), 'Refresh token');
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => Auth::user(),
        ];
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if($request->hasFile('image')){
            $image = ImageUploader::upload($request->file('image'), 'images/originals');
            $data['image'] = $image['url'];
        } else {
            $data['image'] = "https://cloodo-social.s3.ap-southeast-1.amazonaws.com/avatar.jpg";
        }

        $data['password'] = Hash::make($data['password']);
        User::create($data);
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->errorResponse('Unauthorized', 401);
        }
        return $this->successResponse(LoginResource::make($this->respondWithToken($token)), 'Register successfully');
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse([], 'Successfully logged out');
    }
}
