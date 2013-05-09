<?php

ini_set("display_errors", 1);
/**
 * @author Jitendra Singh Bhadouria
 * @desc xmlrpc for betaout plugin
 */
$current_path = dirname(__FILE__);
$wp_load = $current_path . "/wp-load.php";

while (!file_exists($wp_load)) {
    $current_path = $current_path . '/..';
    $wp_load = $current_path . "/wp-load.php";
}

require_once( $wp_load );
require_once( ABSPATH . '/wp-admin/includes/taxonomy.php' );
require_once( ABSPATH . '/wp-admin/includes/image.php' );
require_once 'includes/IPPHPSDK/IPPHPSDK.php';
require_once 'includes/UserDataManagement.php';
require_once 'includes/betaout_rpc.php';
try {
    $hash = isset($_REQUEST['hash']) ? $_REQUEST['hash'] : '';
    $timestamp = isset($_REQUEST['timestamp']) ? $_REQUEST['timestamp'] : '';
    $functionName = isset($_REQUEST['functionName']) ? $_REQUEST['functionName'] : '';
    $url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $format = isset($_REQUEST['format']) ? $_REQUEST['format'] : 'json';
    $url = explode("&hash=", $url);
    $url = $url[0];
    $betaoutApiKey = get_option("_BETAOUT_API_KEY");
    $betaoutApiKey = empty($betaoutApiKey) ? get_option("_BETAOUT_API_KEY_TEMP") : $betaoutApiKey;
    $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
    $betaoutApiSecret = empty($betaoutApiSecret) ? get_option("_BETAOUT_API_SECRET_TEMP") : $betaoutApiSecret;
    if (empty($betaoutApiKey) || empty($betaoutApiSecret)) {
        header("HTTP/1.0 200 Betaout plugin is not configured");
        SocialAxis_UserDataManagement::errorFormatingResponse('Betaout plugin is not configured!', 400, $format);
    }
    if (empty($functionName)) {
        header("HTTP/1.0 200 Missing required parameter functionName");
        SocialAxis_UserDataManagement::errorFormatingResponse('Missing required parameter functionName!', 401, $format);
    }
    if (empty($timestamp)) {
        header("HTTP/1.0 200 Missing required parameter timestamp");
        SocialAxis_UserDataManagement::errorFormatingResponse('Missing required parameter timestamp!', 405, $format);
    }
    if (time() - $timestamp > 60) {
        header("HTTP/1.0 200 Request has been expired");
        SocialAxis_UserDataManagement::errorFormatingResponse('Request has been expired!', 406, $format);
    }
    if (empty($hash)) {
        header("HTTP/1.0 200 Missing hash");
        SocialAxis_UserDataManagement::errorFormatingResponse('Missing hash!', 407, $format);
    }
    SocialAxis_UserDataManagement::checkHash($url, $hash);
    if (method_exists('betaout_rpc', $functionName))
        betaout_rpc::$functionName();
    else {
        header("HTTP/1.0 200 Not a valid function call");
        SocialAxis_UserDataManagement::errorFormatingResponse("Not a valid function call", 404, $format);
    }
    die('betaoutrpc');
} catch (Exception $ex) {
    print_r($ex);
}
?>
