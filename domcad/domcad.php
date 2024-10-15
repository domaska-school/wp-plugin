<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/ProjectSoft-STUDIONIONS
 * @since             1.0.0
 * @package           domcad
 *
 * @wordpress-plugin
 * Plugin Name:       СП ДС ГБОУ СОШ с. Домашка
 * Plugin URI:        https://github.com/domaska-school/wp-plugin
 * Description:       Плагин для сайта Структурное подразделение детский сад ГБОУ СОШ с. Домашка
 * Version:           1.0.0
 * Author:            ProjectSoft
 * Author URI:        https://github.com/ProjectSoft-STUDIONIONS/
 * License:           MIT
 * License URI:       https://mit-license.org/
 * Text Domain:       domcad
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'DOMCAD_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_domcad() {}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_domcad() {}

register_activation_hook( __FILE__, 'activate_domcad' );
register_deactivation_hook( __FILE__, 'deactivate_domcad' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

if ( ! function_exists( 'twentyfourteen_posted_on' ) ) :
	/**
	 * Print HTML with meta information for the current post-date/time and author.
	 *
	 * @since Twenty Fourteen 1.0
	 */
	function twentyfourteen_posted_on() {
		if ( is_sticky() && is_home() && ! is_paged() ) {
			echo '<span class="featured-post">' . __( 'Sticky', 'twentyfourteen' ) . '</span>';
		}

		// Set up and print post meta information.
		printf(
			'<span class="entry-date"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span>',
			esc_url( get_permalink() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);
	}
endif;

function run_domcad() {

	function domcad_admin_footer () {
		$footer_text = array(
			'GitHub:&nbsp;<a href="https://github.com/domaska-school/" target="_blank">https://github.com/domaska-school/</a>',
			'Поддержка:&nbsp;<a href="https://github.com/ProjectSoft-STUDIONIONS" target="_blank">ProjectSoft</a>',
			'Тел.:&nbsp;<a href="tel:+79376445464" target="_blank">+7(937)644-54-64</a>',
			'Email:&nbsp;<a href="mailto:projectsoft2009@yandex.ru?subject=Проблемы с сайтом СП ДС ГБОУ СОШ с. Домашка">projectsoft2009@yandex.ru</a>'
		);
		return implode( ' • ', $footer_text);
	}
	 
	add_filter('admin_footer_text', 'domcad_admin_footer');

	//
	remove_action( 'do_feed_rdf',  'do_feed_rdf',  10, 1 );
	remove_action( 'do_feed_rss',  'do_feed_rss',  10, 1 );
	// remove_action( 'do_feed_rss2', 'do_feed_rss2', 10, 1 );
	remove_action( 'do_feed_atom', 'do_feed_atom', 10, 1 );

	//
	add_action( 'wp', function(){
		//remove_action('wp_head', 'parent_post_rel_link', 10, 0); // prev link
		//remove_action('wp_head', 'start_post_rel_link', 10, 0); // start link
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'feed_links_extra', 3);
		// remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'rsd_link');

		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'index_rel_link');
		remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
		remove_action('wp_head', 'wp_oembed_add_discovery_links');

		remove_action('wp_head', 'rel_canonical');
		remove_action('wp_head', 'rest_output_link_wp_head');

		// Emoji
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('admin_print_scripts', 'print_emoji_detection_script');
		remove_action('admin_print_styles', 'print_emoji_styles');
		// remove_action('template_redirect', 'rest_output_link_header', 11, 0);
		remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		remove_filter('the_content_feed', 'wp_staticize_emoji');
		remove_filter('comment_text_rss', 'wp_staticize_emoji');
	});

	//
	require_once(dirname(__FILE__) . '/Video.php');
	require_once(dirname(__FILE__) . '/WpRunClass.php');
	require_once(dirname(__FILE__) . '/RegisterStyleScript.php');
	require_once(dirname(__FILE__) . '/YandexFormCode.php');
	require_once(dirname(__FILE__) . '/GalleryShortCode.php');
	require_once(dirname(__FILE__) . '/VideoShortCode.php');
	require_once(dirname(__FILE__) . '/WpEmbededRun.php');
	require_once(dirname(__FILE__) . '/WpBashBoardWidgets.php');
	
}

run_domcad();

// Собственный RSS
// add_action('init', 'customRSS');
// function customRSS(){
// 	add_feed('rssfeed', 'customRSSFunc');
// }
// function customRSSFunc(){
// 	// load_template( dirname( __FILE__ ) . '/rss.php' );
// 	include(dirname( __FILE__ ) . '/rss.php');
// }