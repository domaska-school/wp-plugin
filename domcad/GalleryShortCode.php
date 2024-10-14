<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

$_wp_additional_image_sizes['domcad-size'] = array(
	'width'  => absint( 500 ),
	'height' => absint( 0 ),
	'crop'   => false,
);
// Класс вывода галерей
class GalleryShortCode {
	static function init() {
		/**
		 ** Отключаем srcset
		**/
		add_filter( 'wp_calculate_image_srcset', array(__CLASS__, 'disable_srcset'     ) );
		/**
		 ** Переопределяем стандартную галерею
		**/
		add_filter( 'post_gallery',              array(__CLASS__, 'gallery_function'   ), 10, 2 );
		
		add_action( 'wp_head', array( __CLASS__, 'ps_gallery_function' ) );
	}
	
	static function disable_srcset( $sources ) {
		return false;
	}
	
	static function gallery_function( $output, $atts ) {
		global $post; 
		$out = '';
		if($atts["ids"]):
			$arrs = explode(',', $atts["ids"]);
			if(count($arrs)):
				$out = '<figure class="wp-block-gallery has-nested-images columns-default is-cropped">';
				foreach($arrs as $idstr):
					$id = (int)$idstr;
					if($src = wp_get_attachment_url($id)):
						$medium = wp_get_attachment_image_src($id, "domcad-size");
						$out .= '
			<figure class="wp-block-image size-large">
				<a href="' . $src . '" target="_blank" data-fancybox="gallery-' . $post->ID . '">
					<img decoding="async" width="' . $medium[1] . '" height="' . $medium[2] . '" data-id="' . $id . '" class="wp-image-' . $id . '" src="' . $medium[0] . '" alt="">
				</a>
			</figure>';
					endif;
				endforeach;
				$out .= '
	</figure>';
			endif;
		endif;
		return $out;
	}
	
	static function ps_gallery_function($atts){
		return self::gallery_function(false, $atts);
	}
}

GalleryShortCode::init();
