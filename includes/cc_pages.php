<?php
class ccPages{
	
	private static $boDataApiUrl;
	public static $boSiteLink;
	
	public static function addMenu( $topMenu = 'betaout' ){
		self::$boDataApiUrl = 'http://www.newsroom.to/bo-request/';
		
		add_submenu_page($topMenu, 'Workflows', 'Workflows', 'manage_options', 'workflows', 'ccPages::workflows');
		add_submenu_page($topMenu, 'Desks', 'Desks', 'manage_options', 'desks', 'ccPages::desks');
		add_submenu_page($topMenu, 'Story Templates', 'Story Templates', 'manage_options', 'templates', 'ccPages::templates');
		add_submenu_page($topMenu, 'User Roles', 'User Roles', 'manage_options', 'userroles', 'ccPages::userroles');
	}
	
	public static function getWorkflows(){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBOWorkflows' );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function getWorkflowDesks( $workflowId ){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBOWorkflowDesks', 'workflowId' => $workflowId );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function getDesks(){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBODesks' );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function getDeskUsers( $deskId, $quantity=1000 ){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBODeskUsers', 'deskId' => $deskId, 'quantity' => $quantity  );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function getTemplates(){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBOTemplates' );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function getTemplateWorkflows( $templateId ){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBOTemplateWorkflows', 'templateId' => $templateId );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function getRoles(){
		$data = array( 'siteKey' => ContentCloud::$wpSiteKey, 'action' => 'getBOUserRoles' );
		$result = ContentCloud::curlRequest( self::$boDataApiUrl, $data );
		if( $result[ 'error' ] ){
			throw new Exception( $result[ 'message' ] );
		}
		self::$boSiteLink = $result[ 'clientBOSiteLink' ];
		return $result[ 'data' ];
	}
	
	public static function workflows(){
		include_once 'cc_html/workflows.php';
	}

	public static function desks(){
		include_once 'cc_html/desks.php';
	}

	public static function templates(){
		include_once 'cc_html/templates.php';
	}
	
	public static function userroles(){
		include_once 'cc_html/userroles.php';
	}
}