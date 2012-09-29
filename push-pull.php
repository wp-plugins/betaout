<?php
$current_path = dirname(__FILE__);
$wp_load = $current_path . "/wp-load.php";

while(!file_exists($wp_load)) {
	$current_path = $current_path . '/..';
	$wp_load = $current_path . "/wp-load.php";
}

require_once( $wp_load );

require_once( ABSPATH . '/wp-admin/includes/taxonomy.php' );
require_once( ABSPATH . '/wp-admin/includes/image.php' );

$pluginName = 'betaout/index.php';
$pluginActiveStatus = in_array( $pluginName, (array) get_option( 'active_plugins', array() ) );

$message = false;
$error = false;
$code = 200;
$data = array();

if( $pluginActiveStatus ){

	$postArray = $_POST;
	$postHash = $postArray[ 'hash' ];
	unset( $postArray[ 'hash' ] );

	$hash = ContentCloud::getHash( $postArray );

	if( $hash != $postHash ) {
		$postArray[ 'action' ] = '';
	}

	switch( $postArray[ 'action' ] ) {
		case 'category' :{
			$wpCategories = $postArray['wpCategories'];
			$data = ContentCloud::moveBoCategory( $wpCategories );
			break;
		}
		case 'post' :{
			$wpPost = $postArray['wpPost'];
			$data = ContentCloud::moveBoPost( $wpPost );
			break;
		}
		case 'delete-post' :{
			$wpId = $postArray['wpId'];
			$data = ContentCloud::deleteBoPost( $wpId );
			break;
		}
		case 'delete-category' :{
			$wpCategoryId = $postArray['wpCategoryId'];
			$data = ContentCloud::deleteBoCategory( $wpCategoryId );
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