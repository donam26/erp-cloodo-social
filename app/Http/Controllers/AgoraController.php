<?php

namespace App\Http\Controllers;

use App\Services\AgoraService;
use Illuminate\Http\Request;

class AgoraController extends Controller
{
    protected $agoraService;

    public function __construct(AgoraService $agoraService)
    {
        $this->agoraService = $agoraService;
    }

    public function generateToken(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'channel_name' => 'required|string',
                'uid' => 'nullable|integer'
            ]);

            $channelName = $request->channel_name;
            $uid = $request->uid ?? 0;

            $token = $this->agoraService->generateToken($channelName, $uid);

            return response()->json([
                'token' => $token,
                'channel_name' => $channelName,
                'uid' => $uid,
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 