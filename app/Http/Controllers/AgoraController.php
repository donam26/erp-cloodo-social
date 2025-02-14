<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Libraries\AgoraToken\RtcTokenBuilder;
use Illuminate\Support\Facades\Log;

class AgoraController extends Controller
{
    public function generateToken(Request $request)
    {
       
        $request->validate([
            'channelName' => 'required|string'
        ]);

        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $channelName = $request->channelName;
        
        // Debug thông tin
        Log::info('Agora Token Generation:', [
            'appID' => $appID,
            'appCertificate' => $appCertificate,
            'channelName' => $channelName
        ]);

        if (empty($appID) || empty($appCertificate)) {
            return response()->json([
                'error' => 'Agora credentials not configured properly'
            ], 500);
        }

        $uid = 0; // Có thể thay đổi theo user ID của bạn
        $role = RtcTokenBuilder::RolePublisher;
        $expireTimeInSeconds = 3600; // Token hết hạn sau 1 giờ
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        try {
            $token = RtcTokenBuilder::buildTokenWithUid(
                $appID,
                $appCertificate,
                $channelName,
                $uid,
                $role,
                $privilegeExpiredTs
            );

            return response()->json([
                'token' => $token,
                'appID' => $appID,
                'channelName' => $channelName
            ]);
        } catch (\Exception $e) {
            Log::error('Agora Token Generation Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate token'
            ], 500);
        }
    }
} 