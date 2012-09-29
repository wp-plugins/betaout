<?php
class ContentCloud{

	public static $wpSiteKey;
	private static $wpSiteSecret;
	public static $contentCloudApiUrl;

	public function validateSite( $wpSiteKey, $wpSiteSecret ) {
		self::$wpSiteKey = $wpSiteKey;
		self::$wpSiteSecret = $wpSiteSecret;
		self::$contentCloudApiUrl = 'http://www.newsroom.to/wp-request/';

		if( is_user_logged_in() )
		{
			$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'validate' );
			$result = self::curlRequest( self::$contentCloudApiUrl, $data );
			$publicationData = $result['data'];
			
			if( $publicationData['status'] == 'active' )
			{
				if( $publicationData['wpInitialSync'] == 'N' )
				{
					self::importWpData();
				}
				else
				{
					add_action( 'trash_post', 'ContentCloud::deletePost' );
					add_action( 'delete_post', 'ContentCloud::deletePost' );
					add_action( 'save_post', 'ContentCloud::publishPost' );
					add_action( 'create_category', 'ContentCloud::pushCategory' );
					add_action( 'edit_category', 'ContentCloud::pushCategory' );
					add_action( 'delete_category', 'ContentCloud::deleteCategory' );
				}
			}
		}
		
		include_once( 'BOGallery.php' );
			
		add_action( 'wp_head', array( 'BOGallery', 'addGalleryCss' ) );
		add_filter( 'the_content', array( 'BOGallery', 'processGallery' ) );
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
		
		$arrStories = array();
		
		//get all today published posts and sync with betaout
		$current_year = date('Y');
		$current_month = date('m');
		$current_day = date('d');
		query_posts( "post_status=publish&year=$current_year&monthnum=$current_month&day=$current_day&order=ASC" );
		while ( have_posts() ) {
			the_post();
			$wpId = get_the_ID();
			$arrStory = self::getWpPostData( $wpId );
			$arrStories[] = $arrStory;
		}
		
		//get all pending posts and sync with betaout
		query_posts( "post_status=pending&order=ASC" );
		while ( have_posts() ) {
			the_post();
			$wpId = get_the_ID();
			$arrStory = self::getWpPostData( $wpId );
			$arrStories[] = $arrStory;
		}
		
		//get all draft posts and sync with betaout
		query_posts( "post_status=draft&order=ASC" );
		while ( have_posts() ) {
			the_post();
			$wpId = get_the_ID();
			$arrStory = self::getWpPostData( $wpId );
			$arrStories[] = $arrStory;
		}
		
		if(count($arrStories)>0)
		{
			$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'post', 'wpStories' => $arrStories );
			$result = self::curlRequest( self::$contentCloudApiUrl, $data );
		} 
		
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'sync-complete' );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}
	
	private static function getWpPostData( $wpId )
	{
		clean_post_cache( $wpId );
		
		$singlePost = get_post( $wpId );
		$categories = wp_get_post_categories( $wpId );
		
		$tags = wp_get_post_tags( $wpId );
		$arrTag = array();
		foreach( $tags as $tag )
		{
			$arrTag[] = array( 'termId'=> $tag->term_id, 'name'=> $tag->name, 'slug'=> $tag->slug );
		}
			
		$wpPostAuthor = $singlePost->post_author;
		$personaUserId = get_user_meta( $wpPostAuthor, 'personaUserId', true );
			
		$arrStory = array();
		$arrStory['post_id'] = $wpId;
		$arrStory['post_title'] = $singlePost->post_title;
		$arrStory['post_content'] = wpautop( $singlePost->post_content, false );
		$arrStory['post_excerpt'] = $singlePost->post_excerpt;
		$arrStory['post_name'] = $singlePost->post_name;
		$arrStory['post_author'] = $personaUserId;
		$arrStory['post_date'] = $singlePost->post_date_gmt;
		if( $singlePost->post_date_gmt == '0000-00-00 00:00:00' )
		{
			$arrStory['post_date'] = $singlePost->post_date;
		}		
		$arrStory['post_status'] = $singlePost->post_status;
		$arrStory['post_permalink'] = get_permalink( $wpId );
		
		$arrStory['post_categories'] = $categories;
		$arrStory['post_tags'] = $arrTag;
		return $arrStory;
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

		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'category', 'wpCategories' => $wpCategories );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}
	
	
	public static function deleteCategory( $wpCategoryId ){
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'delete-category', 'wpCategoryId' => $wpCategoryId );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
		
		self::pushAllWpCategories();
	}
	
	public static function publishPost( $wpId ){
		
		if ( !wp_is_post_revision( $wpId ) ) {
			try{
				$arrStory = self::getWpPostData( $wpId );
				
				$arrStories[] = $arrStory;
								
				if( count($arrStories) > 0 )
				{
					if( $arrStory['post_status'] == 'publish' )
					{
						$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'post', 'wpStories' => $arrStories );
						$result = self::curlRequest( self::$contentCloudApiUrl, $data );
					}
				}
			}catch( Exception $ex ){
				echo $ex->getMessage() . "\n" . $ex->getTraceAsString();
				exit;
			}
		}
	}
	
	public static function deletePost( $wpId ){
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'delete-post', 'wpId' => $wpId );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}
	
	public static function moveBoCategory( $wpCategories ) {
		$data = array();
		
		$lastCategoryId = -1;
		$lastWpCategoryId = -1;
		foreach( $wpCategories as $categoryId => $wpCategory ) {
			if ( !get_category( $wpCategory['cat_ID'] ) )
				$wpCategory['cat_ID'] = 0;

			if( $lastWpCategoryId > -1 && $lastCategoryId == $wpCategory[ 'parentId' ] ) {
				$wpCategory[ 'category_parent' ] = $lastWpCategoryId;
			}

			unset( $wpCategory[ 'parentId' ] );
			
			$wpCategoryId = wp_insert_category( $wpCategory, true );
			if( is_object( $wpCategoryId ) ) {
				$wpCategoryId = $wpCategoryId->error_data[ 'term_exists' ];
			}
			
			clean_term_cache( $wpCategoryId, 'category', true );
			
			$lastWpCategoryId = $wpCategoryId;
			$lastCategoryId = $categoryId;
			
			$category = get_category( $wpCategoryId );
			$data[] = array(
					'categoryId' => $categoryId,
					'wpCategoryId' => $wpCategoryId,
					'categorySlug' => $category->slug
			);
		}
		return $data;
	}
	
	public static function moveBoPost( $wpPost ) {
		$personaUserId = $wpPost['post_author'];
		
		$wpUserId = 0;
		$users = get_users( array( 'meta_key' => 'personaUserId', 'meta_value' => $personaUserId ) );
		foreach( $users as $user )
		{
			if( $user->ID > 0 && $wpUserId == 0 )
			{
				$wpUserId = $user->ID;
				break;
			}
		}
		if( $wpUserId == 0 )
		{
			$wpUserId = 1;
		}
		
		$wpPost['post_author'] = $wpUserId;
		
		$wpImages = $wpPost[ 'wpImages' ];
		unset( $wpPost[ 'wpImages' ] );
		
		kses_remove_filters();
		
		$post_id = wp_insert_post( $wpPost, $wp_error );
		
		update_post_meta( $post_id, 'bo_galleries', serialize( $wpImages ) );
		
		$data = array();
		if( $post_id )
		{
			clean_post_cache( $post_id );
			$getPost = get_post( $post_id );
			$categories = wp_get_post_categories( $post_id );
			
			$data = array(
					'wpId' => $post_id,
					'storySlug' => $getPost->post_name,
					'categories' => $categories,
					'storyPermalink' => get_permalink( $post_id )
			);
		}
		return $data;
	}
	
	public static function deleteBoPost( $wpId ) {
		wp_trash_post($wpId);
	}
	
	public static function deleteBoCategory( $wpCategoryId ) {
		$result = wp_delete_category( $wpCategoryId );
	}
	
	public static function getHash( $postArray ) {
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
		//echo $output;
		return json_decode($output, true);
	}
	
	public static function stripslashes_deep($value)
	{
		$value = is_array($value) ? array_map( array(self, 'stripslashes_deep'), $value) : stripslashes($value);
		return $value;
	}
	
	public static function cc_plugin_deactivated()
	{
		$data = array( 'siteKey' => self::$wpSiteKey, 'action' => 'deactivate' );
		$result = self::curlRequest( self::$contentCloudApiUrl, $data );
	}
}