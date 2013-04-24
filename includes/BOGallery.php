<?php

	class BOGallery {

		const imageColumns = 3;
		const imageHtml = '<dl class="gallery-item">
				<dt class="gallery-icon">
					<a title="${imageTitle}" href="${imageLink}">
						<img width="150" title="${imageTitle}" alt="${imageTitle}" class="attachment-thumbnail" src="${imageUri}">
					</a>
				</dt>
			</dl>';
		const galleryCss = ".custom-gallery{margin: auto}\n.custom-gallery .gallery-item{float: left;margin-top: 10px;text-align: center;width: 33%}\n.custom-gallery-title{padding:0 0 10px 0;font-size:18px};\n.custom-gallery-title a:hover{text-decoration:none};\n.custom-gallery img{border: 2px solid #cfcfcf}\n.custom-gallery .gallery-caption{margin-left: 0}";

		private static $postGalleries = array();
		private static $currentAssetPost = null;

		public static function processGallery( $content ) {
			global $post;
			self::$postGalleries = get_post_meta( $post->ID, 'bo_assets', true );

			if( !is_array( self::$postGalleries ) || count( self::$postGalleries ) == 0 ) {
				return $content;
			}

			$galleries = self::getComments( $content );
			foreach( $galleries as $gallery ) {
				if( strtolower( substr( trim( $gallery ), 0, 7 ) ) != 'gallery' && strtolower( substr( trim( $gallery ), 0, 9 ) ) != 'slideshow' ) {
					continue;
				}
				$id = explode( ':', trim( $gallery ) );
				if( isset( $id[ 1 ] ) && isset( self::$postGalleries[ $id[ 1 ] ] ) ) {
					// clean_post_cache( self::$postGalleries[ $id[ 1 ] ][ 'wpId' ] );
					self::$currentAssetPost = get_post( self::$postGalleries[ $id[ 1 ] ][ 'wpId' ] );
					if( $galleryHtml = self::getGalleryHtml( self::$postGalleries[ $id[ 1 ] ][ 'wpId' ] ) ) {
						$content = str_replace( '<!--' . $gallery . '-->', $galleryHtml, $content );
					}
				}
			}
			return $content;
		}

		public static function addGalleryCss() {
			echo '<style type="text/css">' . self::galleryCss . '</style>';
		}

		private static function getGalleryHtml( $galleryId ) {
			$galleryImages = array();
			$images = get_post_meta( $galleryId, 'bo_asset', true );
			foreach( $images as $image ) {
				$galleryImages[] = array(
					'url' => $image[ 'view' ],
					'title' => $image[ 'title' ],
					'desc' => $image[ 'desc' ]
				);
			}
			return self::generateHtml( $galleryImages );
		}

		public static function generateHtml( $images = array() ) {
			if( count( $images ) > 0 ) {
				$singleImageHtml = self::imageHtml;
				$html = '';
				foreach( $images as $i => $image ) {
					$html .= str_replace( '${imageDesc}', $image[ 'desc' ], str_replace( '${imageLink}', $image[ 'url' ], str_replace( '${imageUri}', $image[ 'url' ], str_replace( '${imageTitle}', $image[ 'title' ], $singleImageHtml ) ) ) );
					if( ( ( $i + 1 ) % self::imageColumns ) == 0 ) {
						$html .= '<br style="clear: both;" />';
					}
				}
				return '<div class="custom-gallery gallery gallery-columns-' . self::imageColumns . ' gallery-size-thumbnail">
						<div class="custom-gallery-title"><a title="' . self::$currentAssetPost->post_title . '" href="' . get_permalink( self::$currentAssetPost->ID ) . '">' . self::$currentAssetPost->post_title . '</a></div>
						' . $html . '
						<br style="clear: both;" />
					</div>';
			}
			return '';
		}

		private static function getComments( $text ) {
			preg_match_all ( "<!--(.*?)-->", $text, $regs );
			if( count( $regs ) < 1 ) {
				return array();
			} else {
				return $regs[ 1 ];
			}
		}
	}