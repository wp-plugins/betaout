<?php
class ContentCloud{

	public static $wpSiteKey;
	private static $wpSiteSecret;
	public static $contentCloudApiUrl='http://www.contentcloudhq.com/api/wp-request/';

	public function validateSite( $wpSiteKey, $wpSiteSecret) {
		self::$wpSiteKey = $wpSiteKey;
		self::$wpSiteSecret = $wpSiteSecret;
		self::$contentCloudApiUrl = 'http://www.contentcloudhq.com/api/wp-request/';
        if( current_user_can( 'manage_options' ) )
		{
			$publicationUrl = get_option( 'siteurl' );
			$permalinkStructure = get_option( 'permalink_structure' );
			$wordpressBoPluginUrl = plugins_url('betaout');
			$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'validate', 'publicationUrl' => $publicationUrl, 'permalinkStructure' => $permalinkStructure, 'wordpressBoPluginUrl' => $wordpressBoPluginUrl );
			$result = self::curlRequest( self::$contentCloudApiUrl, $data );
			$publicationData = $result['data'];
			
			if( $publicationData['status'] == 'active' )
			{
				if( $publicationData['wpInitialSync'] == 'N' )
				{
					self::importWpData();
				}
				                                
			}
            return $publicationData = $result['data'];
		}
	}	
		
	private static function pushAllWpCategories()
	{
		//get all existing categories and sync with betaout
		$wpCategories = array();
		$categories = get_categories( 'hide_empty=0' );
        $i=0;
		foreach($categories as $category)
		{
			$wpCategories[$i] = array( 'parentId'=>$category->category_parent, 'categoryName'=>$category->cat_name,
					'categorySlug'=>$category->slug, 'categoryDescription'=>$category->category_description, 'wpCategoryId'=>$category->cat_ID );
			$wpParentId[$i] = $category->category_parent;
			$wpCategoryId[$i] = $category->cat_ID;
			$i++;
		}
		array_multisort( $wpParentId, SORT_ASC, $wpCategoryId, SORT_ASC, $wpCategories);
		
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'category', 'wpCategories' => $wpCategories );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}
	
	public static function importWpData()
	{
		self::pushAllWpCategories();
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'sync-complete' );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}

        public static function pullPostData($assignmentId,$publicationId,$status)
	{
            self::$wpSiteKey = get_option("_BETAOUT_API_KEY");
	    self::$wpSiteSecret = get_option("_BETAOUT_API_SECRET");
            $data = array( 'siteKey' => self::$wpSiteKey,'action' => 'pull-post','assignmentId'=>$assignmentId,'publicationId'=>$publicationId,'status'=>$status);
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
                return $result;
	}

	public static function pushCategory( $categoryId ){

		clean_term_cache( $categoryId, 'category', true );
		
		$wpCategories = array();
		$categories = array();
		$currentCategory = get_category( $categoryId );
		$categories[] = $currentCategory;
		while( $currentCategory->category_parent > 0 ) {
			$currentCategory = get_category( $currentCategory->category_parent );
			$categories[] = $currentCategory;
		}

		$categories = array_reverse( $categories );

		foreach($categories as $category)
		{
			$wpCategories[ $category->cat_ID ] = array( 'parentId'=>$category->category_parent, 'categoryName'=>$category->cat_name,
					'categorySlug'=>$category->slug, 'categoryDescription'=>$category->category_description, 'wpCategoryId'=>$category->cat_ID );
		}

        self::$wpSiteKey = get_option("_BETAOUT_API_KEY");
	    self::$wpSiteSecret = get_option("_BETAOUT_API_SECRET");
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'category', 'wpCategories' => $wpCategories );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}
	
	public static function deleteCategory($wpCategoryId ){
              
        self::$wpSiteKey = get_option("_BETAOUT_API_KEY");
	    self::$wpSiteSecret = get_option("_BETAOUT_API_SECRET");
        $data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'delete-category', 'wpCategoryId' => $wpCategoryId );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
		
		self::pushAllWpCategories();
	}	
	
	public static function getHash( $postArray ) {
		$string = http_build_query( self::stripslashes_deep( $postArray ) );
        return urlencode( base64_encode( hash_hmac( 'sha1', $string, str_replace( '+', ' ', str_replace( '%7E', '~', rawurlencode( ( self::$wpSiteSecret ) ) ) ), true ) ) );
	}
        public static function newGetHash( $postArray ) {
              self::$wpSiteSecret = get_option("_BETAOUT_API_SECRET");
		$string = http_build_query( self::stripslashes_deep( $postArray ) );
        return urlencode( base64_encode( hash_hmac( 'sha1', $string, str_replace( '+', ' ', str_replace( '%7E', '~', rawurlencode( ( self::$wpSiteSecret ) ) ) ), true ) ) );
	}

	public static function curlRequest( $url, $post = array() ) {
        $ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		//curl_setopt( $ch, CURLOPT_USERAGENT, json_encode( ( array( 'trace', debug_backtrace(), 'server' => $_SERVER ) ) ) );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 100 );
		if( count( $post ) > 0 ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			ksort( $post );
			$post[ 'hash' ] = self::getHash( $post );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
		}
              
		$output = curl_exec( $ch );
		curl_close( $ch );
        return json_decode($output, true);
	}
	
	public static function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map( array(self, 'stripslashes_deep'), $value) : stripslashes($value);
		return $value;
	}
	
	public static function cc_plugin_deactivated()
	{    
        self::$wpSiteKey = get_option("_BETAOUT_API_KEY");
		self::$wpSiteSecret = get_option("_BETAOUT_API_SECRET");
            
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'deactivate' );
        $result = self::curlRequest( self::$contentCloudApiUrl, $data );               
	}
}