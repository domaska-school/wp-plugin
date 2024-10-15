<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class RegisterStyleScript {
	static function init(){
		add_action ( 'wp_enqueue_scripts', array(__CLASS__, 'add_theme_scripts') );
		add_action ( 'admin_print_footer_scripts', array(__CLASS__, 'load_admin_style') );
		//add_action ( 'get_footer', array(__CLASS__, 'add_footer_styles') );
	}
	
	static function add_theme_scripts() {
		$url = site_url('/wp-content/plugins/domcad', '');
		wp_enqueue_style (
			'fancybox',
			$url . '/css/jquery.fancybox.min.css',
			false,
			filemtime(dirname(__FILE__) . '/css/jquery.fancybox.min.css'),
			'all'
		);
		wp_enqueue_script (
			'fancybox',
			$url . '/js/jquery.fancybox.min.js',
			array( 'jquery' ),
			filemtime(dirname(__FILE__) . '/js/jquery.fancybox.min.js'),
			true
		);
		wp_enqueue_style (
			'fancybox-main',
			$url . '/css/main.min.css',
			false,
			filemtime(dirname(__FILE__) . '/css/main.min.css'),
			'all'
		);
		wp_enqueue_script (
			'fancybox-main',
			$url . '/js/main.min.js',
			array( 'jquery' ),
			filemtime(dirname(__FILE__) . '/js/main.min.js'),
			true
		);
	}

	static function load_admin_style(){
		$url = site_url('/wp-content/plugins/domcad', '');
		wp_enqueue_style (
			'domcad-admin',
			$url . '/css/admin.min.css',
			false,
			filemtime(dirname(__FILE__) . '/css/admin.min.css'),
			'all'
		);
	}

	static function add_footer_styles() {
		wp_enqueue_style(
			'domcad-admin',
			$url . '/css/admin.min.css',
			false,
			filemtime(dirname(__FILE__) . '/css/admin.min.css'),
			'all'
		);
	}
}

RegisterStyleScript::init();
