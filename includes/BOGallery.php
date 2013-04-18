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
		const galleryCss = ".custom-gallery{margin: auto}\n.custom-gallery .gallery-item{float: left;margin-top: 10px;text-align: center;width: 33%}\n.custom-gallery img{border: 2px solid #cfcfcf}\n.custom-gallery .gallery-caption{margin-left: 0}";

		private static $postGalleries = array();
		public static function processGallery( $content ) {
			global $post;
			self::$postGalleries = @unserialize( get_post_meta( $post->ID, 'bo_galleries', true ) );

			if( !is_array( self::$postGalleries ) || count( self::$postGalleries ) == 0 ) {
				return $content;
			}

			$galleries = self::getComments( $content );
			foreach( $galleries as $gallery ) {
				if( strtolower( substr( trim( $gallery ), 0, 7 ) ) != 'gallery' ) {
					continue;
				}
				$id = explode( ':', trim( $gallery ) );
				if( isset( $id[ 1 ] ) && isset( self::$postGalleries[ $id[ 1 ] ] ) ) {				
					if( $galleryHtml = self::getGalleryHtml( $id[ 1 ] ) ) {
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
			$images = self::$postGalleries[ $galleryId ];
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
