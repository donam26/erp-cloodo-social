<?php

namespace App\Services;

use App\Services\Agora\RtcTokenBuilder;

class AgoraService
{
    private $appId;
    private $appCertificate;

    public function __construct()
    {
        $this->appId = config('services.agora.app_id');
        $this->appCertificate = config('services.agora.app_certificate');
    }

    public function generateToken($channelName, $uid = 0)
    {
        $role = RtcTokenBuilder::RolePublisher;
        $expireTimeInSeconds = 3600; // 1 giờ
        $currentTimestamp = time();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        return RtcTokenBuilder::buildTokenWithUid(
            $this->appId,
            $this->appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs
        );
    }
} 