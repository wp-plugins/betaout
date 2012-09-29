<?php
if (!function_exists("curl_init")) {
	$adminErrorMessage .= "cURL library was not found!<br/>";
}

if (!function_exists("json_decode")) {
	$adminErrorMessage .= "JSON was not enabled!<br/>";
}

$wpSiteKey = get_option( '_BETAOUT_API_KEY' );
$wpSiteSecret = get_option( '_BETAOUT_API_SECRET' );

/* echo $wpSiteKey;
echo "<br/>";

echo $wpSiteSecret;
echo "<br/>"; */

include_once 'cc_pages.php';
include_once 'contentcloud.php';

if( !$wpSiteKey || !$wpSiteSecret )
{
	function getInvalidApiSecretMessage(){
		//echo 'Please set valid BetaOUT API Key and Secret.';
        //echo "<div id='betaout-warning' class='updated fade'><p><strong>".__('Betaout is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your Betaout API key</a> for it to work.'), "admin.php?page=betaout")."</p></div>";
	}

	//add_action( 'admin_notices', 'getInvalidApiSecretMessage' );
	return;
}

ContentCloud::validateSite( $wpSiteKey, $wpSiteSecret );
