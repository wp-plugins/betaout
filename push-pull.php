<?php
// Insert the post into the database
//ini_set("display_errors",1);
$postArray = $_POST;

$current_path = dirname(__FILE__);
$wp_load = $current_path . "/wp-load.php";

while(!file_exists($wp_load)) {
	$current_path = $current_path . '/..';
	$wp_load = $current_path . "/wp-load.php";
}

require_once( $wp_load );

require_once( ABSPATH . 'wp-admin/includes/taxonomy.php' );
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once( ABSPATH . 'wp-admin/includes/image.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once 'wpPull.php';

$pluginName = 'betaout/index.php';
if ( is_multisite() ) {
    if(is_plugin_active_for_network($pluginName)){
       $pluginActiveStatus=true;
    }else{
        $pluginActiveStatus=false;
    }
    
}else{
    $pluginActiveStatus = in_array( $pluginName, (array) get_option( 'active_plugins', array() ) );
}


$message = false;
$error = false;
$code = 200;
$data = array();
if( $pluginActiveStatus ){
	$postHash = $postArray[ 'hash' ];
	unset( $postArray[ 'hash' ] );

	$hash = ContentCloud::newGetHash( $postArray );
	
	if( $hash != $postHash ) {
		$postArray[ 'action' ] = '';
	}

	switch( $postArray[ 'action' ] ) {		
		case 'post' :{
			$wpPost = $postArray['wpPost'];
			$data = WpPull::moveBoPost( $wpPost );
			break;
		} 
		case 'delete-post' :{
			$wpId = $postArray['wpId'];
			$data = WpPull::deleteBoPost( $wpId );
			break;
		}		
		default : {
			$message = 'Invalid request!';
			$error = true;
			$code = 401;
		}
	}
}else{
	$message = "Plugin is not Active";
	$code = 401;
}

$response = array(
		'responseCode' => $code,
		'error' => $error,
		'message' => $message,
		'data' => $data
);

die( json_encode( $response ) );