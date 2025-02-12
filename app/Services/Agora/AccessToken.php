<?php
namespace App\Services\Agora;

class AccessToken {
    const VERSION = "006";
    
    public $appID;
    public $appCertificate;
    public $channelName;
    public $expire;
    public $uid;
    public $decodedAppID;
    
    public $messages = array();
    
    public function __construct() {
        $this->message = array();
    }
    
    public function setUid($uid) {
        $this->uid = $uid;
    }
    
    public function is_nonempty_string($name, $str) {
        if (is_string($str) && $str !== "") {
            return true;
        }
        echo $name . " check failed, should be a non-empty string";
        return false;
    }
    
    public static function init($appID, $appCertificate, $channelName, $uid = "") {
        $accessToken = new AccessToken();
        
        if (!$accessToken->is_nonempty_string("appID", $appID) ||
            !$accessToken->is_nonempty_string("appCertificate", $appCertificate) ||
            !$accessToken->is_nonempty_string("channelName", $channelName)) {
            return null;
        }
        
        $accessToken->appID = $appID;
        $accessToken->appCertificate = $appCertificate;
        $accessToken->channelName = $channelName;
        
        $accessToken->expire = time() + 24 * 3600;
        $accessToken->uid = $uid;
        
        $accessToken->decodedAppID = hex2bin($appID);
        
        // Build và trả về token string
        return $accessToken->build();
    }

    public function build() {
        $signature = md5($this->appID . $this->appCertificate . $this->channelName . $this->uid . $this->expire);
        return self::VERSION . $signature;
    }
} 