<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Класс отключения/включения чего либо )))
class WpRunClass {
	static function init() {
		// Увеличение массы загружаемого файла
		@ini_set( 'upload_max_size' , '20MB' );
		@ini_set( 'post_max_size'   , '25MB' );
		@ini_set( 'memory_limit'    , '30MB' );
		
		// Отключение обновления тем
		remove_action('load-update-core.php', 'wp_update_themes');
		add_filter('pre_site_transient_update_themes', create_function('$a', "return null;"));
		wp_clear_scheduled_hook('wp_update_themes');
		
		// Отключаем сообщение о выводе объектного кеша
		add_filter( 'site_status_should_suggest_persistent_object_cache', '__return_false' );
		add_filter( 'site_status_page_cache_supported_cache_headers', '__return_false' );
		
		// Отключаем RSS комментариев
		add_filter( 'feed_links_show_comments_feed', '__return_false' );
		add_action( 'template_redirect', array(__CLASS__, 'redirect_attachment_page') );
		// add_filter( 'wp_default_editor', array(__CLASS__, 'wp_default_editor_filter') );
		add_shortcode( 'author', '__return_false' );
		add_filter( 'the_content', array(__CLASS__, 'author'), 25 );
		add_shortcode('child', array(__CLASS__, 'child'));
		add_shortcode('childs', array(__CLASS__, 'child'));

	}

	static function author($content) {
		switch (get_post_type()) {
			case "post":
				return $content . '<p><strong class="text-right display-block"><em rel="author">' . get_the_author_meta("display_name", 1) . '</em></strong></p>';
				break;
			default:
				return $content;
				break;
		}
	}
	
	static function child(){
		global $post;
		if( $post->post_parent ){
			$children = wp_list_pages( "title_li=&child_of=" . $post->post_parent . "&echo=0&sort_column=menu_order&sort_order=ASC" );
		}
		else {
			$children = wp_list_pages( "title_li=&child_of=" . $post->ID . "&echo=0&sort_column=menu_order&sort_order=ASC" );
		}
		return ($children ? "<ul>" . $children . "</ul>" : "");
	}

	static function redirect_attachment_page(){
		if ( is_attachment() ):
			global $post;
			if ( $post && $post->post_parent ):
				wp_redirect( esc_url( get_permalink( $post->post_parent ) ), 301 );
				exit;
			else:
				wp_redirect( esc_url( home_url( '/' ) ), 301 );
				exit;
			endif;
		endif;
	}

	static function wp_default_editor_filter($r) {
		return ' ';
	}
}

WpRunClass::init();