<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Класс вывода Яндекс форм
/**
 * [yandex_form id=""]
 */
class YandexFormCode {

	static $add_script;

	static function init() {
		add_shortcode('yandex_form', array(__CLASS__, 'yandex_form_func'));
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_head', array(__CLASS__, 'print_script'));
	}

	static function yandex_form_func( $atts ) {
		self::$add_script = true;
		extract( shortcode_atts( array(
			'id' => false,
		), $atts ));
		if($id) {
			$frame = '<script src="https://yastatic.net/s3/frontend/forms/_/embed.js"></script><iframe src="https://forms.yandex.ru/u/' . $id . '/?iframe=1" frameborder="0" name="ya-form-' . $id . '" style="display: block; width: 100% !important;"></iframe>';
			return $frame;
		}
		return "";
	}

	static function register_script() {
		//wp_register_script( 'yandex-form', 'https://yastatic.net/s3/frontend/forms/_/embed.js');
	}

	static function print_script () {
		if ( !self::$add_script ) return;
		wp_print_scripts('yandex-form');
	}
}

YandexFormCode::init();
