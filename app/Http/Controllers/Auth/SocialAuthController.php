<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang đăng nhập Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->stateless()
            ->redirect();
    }

    /**
     * Xử lý callback từ Google
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();
            logger(json_encode($googleUser));
            
            // Tìm hoặc tạo user mới
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'password' => Hash::make(Str::random(16)),
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar
                ]
            );

            // Tạo JWT token
            $token = JWTAuth::fromUser($user);
            
            // Redirect về frontend với token
            $frontendUrl = config('app.frontend_url');
            if (!$frontendUrl) {
                throw new \Exception('Frontend URL not configured');
            }
            return redirect()->away($frontendUrl . '?token=' . $token);
            
        } catch (\Exception $e) {
            logger()->error('Google login error: ' . $e->getMessage());
            return redirect()->away(
                config('app.frontend_url') . '?error=auth_failed'
            );
        }
    }
}
