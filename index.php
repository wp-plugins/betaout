<?php

//ini_set("display_errors", 1);
/*
  Plugin Name: Betaout ContentCloud
  Plugin URI: http://www.betaout.com
  Description: Manage all your Wordpress sites and Editorial team from a single interface
  Version: 0.1.6
  Author: BetaOut (support@betaout.com)
  Author URI: http://www.betaout.com
  License: GPLv2 or later
 */
include_once ABSPATH . '/' . WPINC . '/pluggable.php';
include_once ABSPATH . '/' . WPINC . '/admin-bar.php';

include_once 'contentcloud.php';

defined('NEWSROOM_API_URL')
        || define('ACCESS_API_URL', 'http://access.betaout.com/api/');

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
            add_action('init',array('BetaoutConnect', 'init'));
            add_action('admin_menu', 'betaout_admin_menu');
           }else{
               
           }
    } catch (Exception $ex) {

    }
}

function betaout_admin_menu(){
     add_menu_page('BetaOut', 'BetaOut', 'manage_options', 'betaout', array('BetaoutConnect', 'betaout'), plugins_url('betaout/images/icon.png'));
}


class BetaoutConnect{
    public static function init() {
        add_action('admin_enqueue_scripts', array('BetaoutConnect', 'adminStyle'));
      add_action('create_category', array('ContentCloud','pushCategory' ));
     add_action('edit_category', array('ContentCloud','pushCategory' ));
     add_action('delete_category', array('ContentCloud','deleteCategory'));
      add_action( 'wp_head', array( $this, 'head' ), 1 );
       
     }
     
       public function head() {

           if ( is_singular() ) {
                        global $post;
                         if ( isset( $post ) && isset( $post->post_status ) && $post->post_status != 'auto-draft')
			$postid = $post->ID;
                         $custom = get_post_custom( $postid );
	             if ( !empty( $custom['betaout_metadesc'] ))
		          $metadesc =maybe_unserialize($custom['betaout_metadesc']);
	              if ( ! empty( $metadesc ) )
				echo '<meta name="description" content="' . esc_attr( strip_tags( stripslashes( $metadesc ) ) ) . '"/>' . "\n";

                      if ( !empty( $custom['betaout_seotitle'] ))
		          $metatitle =maybe_unserialize($custom['betaout_seotitle']);
                       if ( ! empty( $metatitle ) )
				echo '<meta name="title" content="' . esc_attr( strip_tags( stripslashes( $metatitle ) ) ) . '"/>' . "\n";

                }
               }
    public function adminStyle(){
        $src = plugins_url('betaout/css/common.css', dirname(__FILE__));
        wp_register_style('commonCss', $src);
        wp_enqueue_style('commonCss');
    }

    public static function betaout() {
          try {
               $betaoutApiKey = get_option("_BETAOUT_API_KEY");
                $betaoutApiSecret = get_option("_BETAOUT_API_SECRET");
                $wordpressVersion = get_bloginfo('version');
                $wordpressBoPluginUrl= plugins_url() . "/betaout";

                if (!empty($betaoutApiKey) && !empty($betaoutApiSecret)) {
                    $parameters = array('wordpressVersion' => $wordpressVersion, 'wordpressBoPluginUrl' => $wordpressBoPluginUrl);
                    try {
                        $curlResponse=ContentCloud::validateSite( $betaoutApiKey, $betaoutApiSecret );
                        $curlResponse = '{ "responseCode":200,"clientAccountName":"Internal"}';
                    } catch (Exception $ex) {
                        $curlResponse = '{ "error": "' . $ex->getMessage() . '", "responseCode": 500 }';
                    }
                    $curlResponse = json_decode($curlResponse, true);
                }
                if(isset($curlResponse['responseCode']) && $curlResponse['responseCode'] == 200) {
                    $clientAccountName = $curlResponse['clientAccountName'];
                }else{
                 $errorMessage="Not A valid Key Secret";
                }
               require_once('configuration.php');
         } catch (Exception $ex) {

        }
    }
    
   
   
}

add_action('wp_ajax_verify_betaoutkey', 'verify_betaoutkey_callback');

function verify_betaoutkey_callback() {
          $betaoutApiKey = $_POST['betaoutApiKey'];
          $betaoutApiSecret=$_POST['betaoutApiSecret'];
          $curlResponse=ContentCloud::validateSite($betaoutApiKey, $betaoutApiSecret);
          if (isset($curlResponse['status']) && $curlResponse['status'] == 'active') {
               update_option("_BETAOUT_API_KEY", $betaoutApiKey);
               update_option("_BETAOUT_API_SECRET", $betaoutApiSecret);
              echo json_encode($curlResponse);
           }else{
               return false;
           }
	die(); // this is required to return a proper result
}
 function pluginDeactivate(){
      
        $curlResponse=ContentCloud::cc_plugin_deactivated();
    }

    
register_deactivation_hook(__FILE__, 'pluginDeactivate');


