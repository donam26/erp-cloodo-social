<?php

namespace App\Libraries\AgoraToken;

class RtcTokenBuilder
{
    const RoleAttendee = 0;
    const RolePublisher = 1;
    const RoleSubscriber = 2;
    const RoleAdmin = 101;

    public static function buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs)
    {
        return self::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
    }

    public static function buildTokenWithUserAccount($appID, $appCertificate, $channelName, $userAccount, $role, $privilegeExpiredTs)
    {
        $token = AccessToken::init($appID, $appCertificate, $channelName, $userAccount);
        $Privileges = AccessToken::Privileges;
        $token->addPrivilege($Privileges["kJoinChannel"], $privilegeExpiredTs);
        if (($role == self::RoleAttendee) ||
            ($role == self::RolePublisher) ||
            ($role == self::RoleAdmin)
        ) {
            $token->addPrivilege($Privileges["kPublishVideoStream"], $privilegeExpiredTs);
            $token->addPrivilege($Privileges["kPublishAudioStream"], $privilegeExpiredTs);
            $token->addPrivilege($Privileges["kPublishDataStream"], $privilegeExpiredTs);
        }
        return $token->build();
    }
} 