<?php
class WpPull{

	// move category
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

	// delete category
	public static function deleteBoCategory( $wpCategoryId ) {
		$result = wp_delete_category( $wpCategoryId );
	}
	
	// move post
	public static function moveBoPost( $wpPost, $structuredPostData ) {
		$personaUserId = $wpPost['post_author'];
		$wpUserId = 0;
		
		$version = get_bloginfo('version');
		if( $version <= 3.0 ){
			$users = get_users_of_blog();
			foreach( $users as $user )
			{
				if( $user->ID > 0 && $wpUserId == 0 )
				{
 					$wpPersonaUserId = get_user_meta( $user->ID, 'personaUserId', true ); 
 					if( $wpPersonaUserId == $personaUserId )
 					{
 						$wpUserId = $user->ID;
 						break;
 					}
				}
			}
		}else{
			$users = get_users( array( 'meta_key' => 'personaUserId', 'meta_value' => $personaUserId ) );
			foreach( $users as $user )
			{
				if( $user->ID > 0 && $wpUserId == 0 )
				{
					$wpUserId = $user->ID;
					break;
				}
			}
		}
		if( $wpUserId == 0 )
		{
			$wpUserId = 1;
		}
		
		$wpPost['post_author'] = $wpUserId;
		
		$boAssets = $wpPost[ 'boAssets' ];
		$boAssetsPostData = $wpPost[ 'boAssetsPostData' ];
		
		$templateId = $wpPost[ 'templateId' ];
		$templateType = $wpPost[ 'templateType' ];
		$storyfolderId = $wpPost[ 'storyfolderId' ];
		$featureImage = $wpPost[ 'featureImage' ];
		
		unset( $wpPost[ 'boAssets' ], $wpPost[ 'boAssetsPostData' ], $wpPost[ 'templateId' ], $wpPost[ 'templateType' ], $wpPost[ 'storyfolderId' ], $wpPost[ 'featureImage' ] );
		
		kses_remove_filters();
		
		if( NULL == get_post( $wpPost[ 'ID' ] ) )
		{
			$wpPost[ 'ID' ] = 0;
		}

		$post_id = wp_insert_post( $wpPost, $wp_error );
		
		$data = array();
		
		if( $post_id )
		{
			update_post_meta( $post_id, 'bo_assets', serialize( $boAssets ) );
			
			update_post_meta( $post_id, 'templateId', $templateId );
			update_post_meta( $post_id, 'templateType', $templateType );
			update_post_meta( $post_id, 'storyfolderId', $storyfolderId );
			
			$current_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true);
			$current_cc_thumbnail_id = get_post_meta( $current_thumbnail_id, '_cc_thumbnail_id', true);
			
			if( $current_cc_thumbnail_id != $featureImage[ 'guid' ] )
			{
				if( count( $featureImage ) > 0 ){
					$attach_id = wp_insert_attachment( $featureImage, $featureImage[ 'guid' ], $post_id );
					update_post_meta( $attach_id, '_cc_thumbnail_id', $featureImage[ 'guid' ] );
					update_post_meta( $post_id, '_thumbnail_id', $attach_id );
				}
			}
			
			if( $templateType == 'structured' ){
				self::saveStructuredData( $post_id, $structuredPostData );
			}
			
			$assetsWpId = array();
			foreach( $boAssetsPostData as $assetId => $boAssetPostData ){
				$asset_post_id = wp_insert_post( $boAssetPostData, $wp_error );
				if( $asset_post_id ){
					update_post_meta( $asset_post_id, 'bo_asset', serialize( $boAssetPostData['boAsset'] ) );
					
					$assetsWpId[ $assetId ] = $asset_post_id;
				}
			}
		
			clean_post_cache( $post_id );
			$getPost = get_post( $post_id );
			$categories = wp_get_post_categories( $post_id );
			
			$data = array(
					'wpId' => $post_id,
					'assetsWpId' => $assetsWpId,
					'storySlug' => $getPost->post_name,
					'categories' => $categories,
					'storyPermalink' => get_permalink( $post_id ),
					'wpversion' => $version,
					'wpUserId' => $wpUserId
			);
		}
		return $data;
	}
	
	// delete wordpress post
	public static function deleteBoPost( $wpId ) {
		wp_trash_post($wpId);
	}
	
	// save post structured data
	private function saveStructuredData( $post_id, $structuredPostData ){
		
		if( count( $structuredPostData ) == 0 || $post_id == 0 )
			return;
		
		$structured_keys = get_post_meta( $post_id, 'structured_keys', true);
		
		if( is_array( $structured_keys ) ){
			foreach( $structured_keys as $structured_key )
			{
				delete_post_meta( $post_id, $structured_key);
			}
		}

		$structured_keys = array_keys( $structuredPostData );
		update_post_meta( $post_id, "structured_keys", $structured_keys );
		
		foreach( $structuredPostData as $structuredPostKey => $structuredPostValue ){
			update_post_meta( $post_id, $structuredPostKey, $structuredPostValue );
		}
	}
}

// get all the active groups as a key value pairs
function get_post_groups( $post_id = 0, $group_type = '' ){
	$post_id = $post_id == 0 || $post_id == '' ? get_the_ID() : $post_id;
	
	$group_types = get_post_group_types( $post_id );
	
	$arr_group_types = array();
	if( is_array( $group_type ) ){
		$arr_group_types = $group_type;
	}elseif( $group_type != '' ){
		$arr_group_types = array( $group_type );
	}	
	
	if( count( $arr_group_types ) > 0 ){
		$tmp_group_types = array();
		foreach( $arr_group_types as $group_type ){
			if( isset( $group_types[ $group_type ] ) ){
				$tmp_group_types[ $group_type ] =  $group_types[ $group_type ];
			}
		}
		$group_types = $tmp_group_types;
	}
	
	$arrGroups = array();
	foreach( $group_types as $key => $value ){
		if( $key == 'Primary' )
			continue;
		
		if( $value > 0 ){
			$groupElements = get_post_meta( $post_id, $key.'_Elements', true);
			if( $groupElements != '' ){
				$arrGroupElements = explode( "||", $groupElements );
				
				$arrGroupsData = array();
				for( $i=0;$i<$value;$i++){
					$arrGroupData = array();
					foreach( $arrGroupElements as $groupElement ){
						$elementKey = $key . '_' . $i . '_' . $groupElement;
						$arrGroupData[ $groupElement ] = get_post_meta( $post_id, $elementKey, true);
					}
					$arrGroupsData[ $i ] = $arrGroupData;
				}
				$arrGroups[ $key ] = $arrGroupsData;
			}
		}
	}
	return $arrGroups;
}

// get primary group data
function get_post_group_primary( $post_id = 0 ){

	$post_id = $post_id == 0 ? get_the_ID() : $post_id;

	$primary_elements = array();

	$primaryElements = get_post_meta( $post_id, 'Primary_Elements' , true);

	if( $primaryElements != '' ){
		$arrPrimaryElements = explode( "||", $primaryElements );
		foreach( $arrPrimaryElements as $key => $value ){
			$primary_elements[ $value ] = get_post_meta( $post_id, 'Primary_'.$value, true);
		}
	}
	return $primary_elements;
}

// get all active gorups name with their element counts
function get_post_group_types( $post_id = 0 ){

	$post_id = $post_id == 0 ? get_the_ID() : $post_id;

	$group_types = array();	

	$groups = get_post_meta( $post_id, 'Groups_Types' , true);

	if( $groups != '' ){
		$arrGroups = explode( "||", $groups );
		foreach( $arrGroups as $key => $value ){
			$group_types[ $value ] = get_post_meta( $post_id, $value.'_Count', true);
		}
	}
	return $group_types;
}

// get all the active groups html
function the_post_groups(){
	$groups = get_post_groups();
	echo "<ul>";
	foreach( $groups as $key => $groupData ){
		echo "<li>$key : <br/>";
		foreach( $groupData as $arrData ){
			echo "<ul>";
			foreach( $arrData as $dataKey => $data ){
				if( is_array( $data ) ){
					echo "<li>$dataKey : ";
					// is gallery
					if( isset( $data[ 'elements' ] ) ){
						echo "<ul><li>Gallery Title : " . $data[ 'title' ] . '<br />Gallery Description : ' . $data[ 'desc' ] . '<ul>';
						$mediaElements = $data[ 'elements' ];
						foreach( $mediaElements as $mediaElement ){
							echo "<li>Image Title : " . $mediaElement[ 'title' ] . '</li>';
							echo "<li>Image Description : " . $mediaElement[ 'desc' ] . '</li>';
							echo '<li>small image : <img src="' . $mediaElement[ 'image' ] . '" /></li>';
							if( $mediaElement[ 'elementType' ] == 'image' ){
								echo '<li>full image : <img src="' . $mediaElement[ 'view' ] . '" /></li>';
							}elseif( $mediaElement[ 'elementType' ] == 'video' ){
								echo '<li>Video : ' . $mediaElement[ 'view' ] . '</li>';
							}
						}
						echo "</ul></li></ul>";
					}else{
						echo "<ul>";
						echo "<li>Image Title : " . $data[ 'title' ] . '</li>';
						echo "<li>Image Description : " . $data[ 'desc' ] . '</li>';
						echo '<li>small image : <img src="' . $data[ 'image' ] . '" /></li>';
						if( $data[ 'type' ] == 'image' ){
							echo '<li>full image : <img src="' . $data[ 'view' ] . '"/></li>';
						}elseif( $data[ 'type' ] == 'video' ){
							echo '<li>Video : ' . $data[ 'view' ] . '</li>';
						}
						echo "</ul>";
					}
					echo "</li>";
				}else{
					echo "<li>$dataKey : $data</li>";
				}
			}
			echo "</ul>";
		}
		echo "</li>";
	}
	echo "</ul>";
}

// get primary group data html
function the_post_group_primary(){
	echo "<ul>";
	$group_primary = get_post_group_primary();
	foreach ( $group_primary as $key => $value ){
		if( is_array( $value ) ){
			echo "<li>$key :";
			// is gallery
			if( isset( $value[ 'elements' ] ) ){
				echo "<ul><li>Gallery Title : " . $value[ 'title' ] . '<br />Gallery Description : ' . $value[ 'desc' ] . '<ul>';
				$mediaElements = $value[ 'elements' ];
				foreach( $mediaElements as $mediaElement ){
					echo "<li>Image Title : " . $mediaElement[ 'title' ] . '</li>';
					echo "<li>Image Description : " . $mediaElement[ 'desc' ] . '</li>';
					echo '<li>small image : <img src="' . $mediaElement[ 'image' ] . '" /></li>';
					if( $mediaElement[ 'elementType' ] == 'image' ){
						echo '<li>full image : <img src="' . $mediaElement[ 'view' ] . '" /></li>';
					}elseif( $mediaElement[ 'elementType' ] == 'video' ){
						echo '<li>Video : ' . $mediaElement[ 'view' ] . '</li>';
					}
				}
				echo "</ul></li></ul>";
			}else{
				echo "<ul>";
				echo "<li>Image Title : " . $value[ 'title' ] . '</li>';
				echo "<li>Image Description : " . $value[ 'desc' ] . '</li>';
				echo '<li>small image : <img src="' . $value[ 'image' ] . '" /></li>';
				if( $value[ 'type' ] == 'image' ){
					echo '<li>full image : <img src="' . $value[ 'view' ] . '"/></li>';
				}elseif( $value[ 'type' ] == 'video' ){
					echo '<li>Video : ' . $value[ 'view' ] . '</li>';
				}
				echo "</ul>";
			}
			echo "</li>";
		}else{
			echo "<li>$key : $value</li>";
		}
	}
	echo "</ul>";
}

// get all active gorups name with html
function the_post_group_types(){
	$group_types = get_post_group_types();
	echo "<ul>";
	foreach( $group_types as $key => $value ){
		echo "<li>$key</li>";
	}
	echo "</ul>";
}