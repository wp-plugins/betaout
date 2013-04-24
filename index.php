<?php

//ini_set("display_errors", 1);
/*
  Plugin Name: Betaout ContentCloud
  Plugin URI: http://www.betaout.com
  Description: Manage all your Wordpress sites and Editorial team from a single interface
  Version: 0.1.5
  Author: BetaOut (support@betaout.com)
  Author URI: http://www.betaout.com
  License: GPLv2 or later
 */
include_once ABSPATH . '/' . WPINC . '/pluggable.php';
include_once ABSPATH . '/' . WPINC . '/admin-bar.php';

if (isset($_REQUEST['personasessionid']))
    $personaSessionId = $_REQUEST['personasessionid'];
elseif (isset($_COOKIE['personasessionid']))
    $personaSessionId = $_COOKIE['personasessionid'];

include_once 'includes/IPPHPSDK/IPPHPSDK.php';
include_once 'includes/socialAxis.php';
//include_once 'includes/UserProfileSnapWidget.php';
//include_once 'includes/LoginWidget.php';
//include_once 'includes/LeaderBoardWidget.php';
//include_once 'includes/FollowBarWidget.php';
//include_once 'includes/RatingWidget.php';
//include_once 'includes/RecentBadgesWidget.php';
include_once 'includes/UserDataManagement.php';
include_once 'includes/betaout_rpc.php';
include_once 'includes/wpcontentcloud.php';


defined('ACCESS_API_URL')
        || define('ACCESS_API_URL', 'http://access.betaout.com/api/');

defined('PERSONA_API_URL')
        || define('PERSONA_API_URL', 'http://persona.to/clientapi/');
//------------------------------------------------------------------------------
//the plugin will work function if cURL and add_function exist and the appropriate version of PHP is available.
$adminErrorMessage = "";

if (version_compare(PHP_VERSION, '5.2.0', '<')) {
    $adminErrorMessage .= "PHP 5.2 or newer not found!<br/>";
}

if (!function_exists("curl_init")) {
    $adminErrorMessage .= "cURL library was not found!<br/>";
}

if (!function_exists("session_start")) {
    $adminErrorMessage .= "Sessions are not enabled!<br/>";
}

if (!function_exists("json_decode")) {
    $adminErrorMessage .= "JSON was not enabled!<br/>";
}

if (function_exists('add_action') && function_exists('add_filter')) {
    try {
        if (empty($adminErrorMessage)) {
            add_action('init', 'socialAxis_plugin::socialAxis_addFiles');
//            add_action('register_form', 'socialAxis_plugin::socialAxisLogin_form');
            add_action('admin_print_scripts-' . $page, 'socialaxis_admin_scripts');
            if (!empty($personaSessionId)) {
                try {
                    setcookie("personasessionid", $personaSessionId, time() + 60 * 60 * 24 * 30, '/');
                } catch (Exception $e) {

                }
            }
            add_action('admin_menu', 'socialAxis_plugin::socialAxisPluginMenu');
//            add_action('publish_post', 'socialAxis_plugin::post_myfunction');
        }
    } catch (Exception $ex) {

    }
}
//SocialAxis_UserDataManagement::checkIfWpSync();
//add_filter('template_redirect', 'SocialAxis_UserDataManagement::betaout_rpc');
register_activation_hook(__FILE__, 'SocialAxis_UserDataManagement::myplugin_activate');
register_deactivation_hook(__FILE__, 'SocialAxis_UserDataManagement::myplugin_deactivate');
register_uninstall_hook(__FILE__, 'SocialAxis_UserDataManagement::myplugin_uninstall');
add_action('profile_update', 'SocialAxis_UserDataManagement::editUser');
add_action('user_register', 'SocialAxis_UserDataManagement::sendNewUserToPersona');
//add_filter('the_content', 'socialAxis_plugin::socialAxisAddShareButtons');
//add_filter('wp_footer', 'socialAxis_plugin::claimBadgePopUp');
//add_filter('wp_footer', 'socialAxis_plugin::bottomBar');



