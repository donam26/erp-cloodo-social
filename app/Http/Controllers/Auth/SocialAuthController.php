<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang đăng nhập Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Xử lý callback từ Google
     */
    public function handleGoogleCallback(Request $request)
    {
        $urlLoginToken = env('FRONTEND_URL');
        // $urlLoginToken = config('app.FRONTEND_URL');
        $token = 'fake_token';
        logger($token . $urlLoginToken);

        $user = Socialite::driver('google')->stateless()->user();
        return redirect()->away($urlLoginToken . $token);
    }
}
