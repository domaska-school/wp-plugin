<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Класс вывода video через ссылку (стандартный метод)
class WpEmbededRun {
	static function init() {
		wp_embed_register_handler('rutube', '#https?:\/\/(www\.)?rutube\.ru\/(video|shorts)\/([a-zA-Z0-9_-]+)#i', array( __CLASS__, 'wp_embed_rutube'));
	}


	static function wp_embed_rutube( $matches, $attr, $url, $rawattr ) {
		$video_id = esc_attr($matches[3]);
		$type = esc_attr($matches[2]);
		$class_embed = "";
		switch ($type) {
			case 'shorts':
				$class_embed = " embeded-by-shorts";
				break;
			default:
				$class_embed = " embeded-by-16x9";
				break;
		}
		$embed = sprintf(
			'<div class="embeded%2$s"><iframe id="rutube-%1$s" width="720" height="405" src="https://rutube.ru/embed/%1$s" frameBorder="0" allow="clipboard-write" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>',
			$video_id,
			$class_embed
		);
		return $embed;
	}
}

WpEmbededRun::init();
