<?php

/**
 * Copyright 2011 Instapress, Inc.
 * @author Jitendra Singh Bhadouria
 */
if (!function_exists('curl_init')) {
    throw new Exception('SocialAxis needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('SocialAxis needs the JSON PHP extension.');
}

defined('SOCIALAXIS_DOMAIN')
        || define('SOCIALAXIS_DOMAIN', "http://clientapi.betasa.info/");

abstract class IPPHPSDKBase {

    protected $apiKey;
    protected $apiSecret;
    protected $requestUrl;
    protected $params;
    protected $signatureMethod = 'HMAC-SHA1';
    protected $hash;
    protected $functionUrlMap = array(
        'getTopContributors' => 'publication/topcontributors/',
        'getContentLikes' => 'publication/contentlikes/',
        'likeContent' => 'user/likecontent/',
        'getRecentBadges' => 'publication/recentbadges/',
        'getUserFollowers' => 'user/followers/',
        'getUserFollowing' => 'user/following/',
        'getLatestActivities' => 'publication/latestactivities/',
        'getUserFriendsActivityStream' => 'user/friendsactivitystream/',
        'getUserActivities' => 'user/activities/',
        'getUserNotifications' => 'user/notifications/',
        'postContentRating' => 'user/contentrating/',
        'getContentRating' => 'publication/contentrating/',
        'getUserAchievements' => 'user/achievements/',
        'getCurrentOnlineUser' => 'client/currentonlineuser/',
        'getUserStalkers' => 'user/stalkers/',
        'getProfileSnap' => 'user/profilesnap/',
        'loginUser' => 'user/login/',
        'requestGamification' => 'user/requestgamification/'
    );

    public function __construct($apiKey, $apiSecret) {
        $this->setApiKey($apiKey);
        $this->setApiSecret($apiSecret);
    }

    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_USERAGENT => 'instapress-engageapi-php-1.0',
    );

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function setHash($hash) {
        $this->hash = $hash;
        return $this;
    }

    public function getHash() {
        return $this->hash;
    }

    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

    public function getParams() {
        return $this->params;
    }

    public function setApiSecret($apiSecret) {
        $this->apiSecret = $apiSecret;
        return $this;
    }

    public function getApiSecret() {
        return $this->apiSecret;
    }

    public function getRequestUrl() {
        return $this->requestUrl;
    }

    public function setRequestUrl($requestUrl) {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    public function makeParams($params=false) {
        if (!is_array($params) && !empty($params))
            throw new Exception("paramter should be associative array!");
        try {
            if (!isset($params['publicationKey']))
                $params['publicationKey'] = $this->getApiKey();
            ksort($params);
            $paramUrl = http_build_query($params, null, "&");
            $this->setParams($paramUrl);
        } catch (Exception $ex) {
            throw new Exception($ex->getCode() . ":" . $ex->getMessage());
        }
    }

    function __call($functionName, $argumentsArray) {
        $apiKey = $this->getApiKey();
        $apiSecret = $this->getApiSecret();
        if (empty($apiKey))
            throw new Exception("Invalid Api call, Api key must be provided!");
        if (empty($apiSecret))
            throw new Exception("Invalid Api call, Api Secret must be provided!");
        if (!isset($this->functionUrlMap[$functionName]))
            throw new Exception("Invalid Function call!");
        try {
            $requestUrl = SOCIALAXIS_DOMAIN . $this->functionUrlMap[$functionName]; //there should be error handling to make sure function name exist
            if (isset($argumentsArray[0]) && is_array($argumentsArray[0]) && count($argumentsArray[0]) > 0)
                $this->makeParams($argumentsArray[0]);
            else
                $this->makeParams();
            $requestUrl.="?" . $this->getParams();
            $this->setRequestUrl($requestUrl);
            $this->signString();
            $requestUrl = $this->getRequestUrl() . "&hash=" . $this->getHash();

            return $this->makeRequest($requestUrl);
        } catch (Exception $ex) {
            throw new Exception($ex->getCode() . ":" . $ex->getMessage());
        }
    }

    protected function signString() {
        switch ($this->signatureMethod) {
            case 'HMAC-SHA1':
                $key = $this->encode_rfc3986($this->apiSecret);
                $requestUrl = $this->getRequestUrl();
                if (empty($requestUrl))
                    throw new Exception("Requesting Url is not valid");
                $hash = urlencode(base64_encode(hash_hmac('sha1', $this->getRequestUrl(), $key, true)));
                $this->setHash($hash);
                break;
            default :
                throw new Exception("Signature method is not valid");
                break;
        }
    }

    protected function encode_rfc3986($string) {
        return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode(($string))));
    }

    protected function makeRequest($requestUrl, $ch=null) {
        if (!$ch) {
            $ch = curl_init();
        }

        $options = self::$CURL_OPTS;
        $options[CURLOPT_URL] = $requestUrl;

// disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
// for 2 seconds if the server does not support this header.
        if (isset($options[CURLOPT_HTTPHEADER])) {
            $existing_headers = $options[CURLOPT_HTTPHEADER];
            $existing_headers[] = 'Expect:';
            $options[CURLOPT_HTTPHEADER] = $existing_headers;
        } else {
            $options[CURLOPT_HTTPHEADER] = array('Expect:');
        }

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if ($result === false) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

}

?>
