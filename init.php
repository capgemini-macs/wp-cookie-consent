<?php
/**
 * Initialise the plugin
 *
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

namespace MACS\Cookie_Consent;

use MACS\Cookie_Consent\Templates\Loader as Loader;
use MACS\Cookie_Consent\Settings as Settings;

if ( ! defined( 'MACS_COOKIE_CONSENT_PATH' ) ) {
	define( 'MACS_COOKIE_CONSENT_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'MACS_COOKIE_CONSENT_URL' ) ) {
	define( 'MACS_COOKIE_CONSENT_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MACS_COOKIE_CONSENT_JS_VER' ) ) {
	define( 'MACS_COOKIE_CONSENT_JS_VER', '0.0.1' );
}

/**
 * Following classes store definitions of Cookie Post (cookie items descriptions) data. 
 * We refer to their constants to work with post meta.
 * This allows us to change meta keys easily or quickly switch metaboxes / admin columns on or off.
 */
if ( ! defined( 'MACS_COOKIE_FIELDS' ) ) {
	define( 
		'MACS_COOKIE_FIELDS',
		[
			'MACS\Cookie_Consent\Fields\CookieDescription',
			'MACS\Cookie_Consent\Fields\CookieExpiry',
			'MACS\Cookie_Consent\Fields\CookieDomain',
		] 
	);
}

// Kick it off!
run();

function run() {

	// Check requirements first.
	if ( false === check_requirements() ) {
		return;
	}

	// i18n
	add_action( 'init', __NAMESPACE__ . '\\load_translations' );

	// Create Settings Pages (uses Fieldmanager)
	// 1. General plugin settings
	// 2. Cookie Pages settings (for contents of cookie policy page, user preferences page and the popup) 
	$main_settings = new Settings\CookiePluginSettings();
	add_action( 'init', [ $main_settings, 'register_settings_page' ] );
	
	$pages_settings = new Settings\CookiePagesSettings();
	add_action( 'init', [ $pages_settings, 'register_settings_page' ] );

	// Register custom Post Type
	$post_type = new PostType();
	add_action( 'init', [ $post_type, 'register' ] );

	// Register Taxonomy for cookie types
	// Remove other Taxonomies from our Post Type edit screen
	$taxonomy = new Taxonomy();
	add_action( 'init', [ $taxonomy, 'register' ] );
	add_action( 'init', [ $taxonomy, 'deregister_other' ], 20 );
	add_action( 'fm_term_' . Taxonomy::SLUG, [ $taxonomy, 'register_order_field' ] );

	// Register Metaboxes for cookie fields with Fieldmanager
	// Also do some cleanup on cookie post edit screen
	$metaboxes = new Metaboxes( MACS_COOKIE_FIELDS );
	add_action( 'fm_post_' . PostType::SLUG, [ $metaboxes, 'register' ] );
	add_action( 'admin_init', [ $metaboxes, 'deregister_other' ], 20 );

	// Adjust Cookies posts admin list
	$list = new CookiePostList( MACS_COOKIE_FIELDS );
	add_filter( 'manage_' . PostType::SLUG . '_posts_columns', [ $list, 'set_custom_columns' ], 100, 1 );
	add_filter( 'manage_' . PostType::SLUG . '_posts_custom_column', [ $list, 'custom_column_value' ], 10, 2 );


	/**
	 * Followind actions will be fired only if user sets plugin's status as active (ON)
	 * 
	 * NOTE: This is related to the plugin settings, not WordPress plugin activation
	 *       The additional activation option is here to allow setting up plugin options before
	 *       actially putting it to work.
	 */
	if ( 'on' !== Settings\Plugin::status() ) {
		return;
	}

	// add JS & CSS
	$scripts = new Scripts();
	add_action( 'wp_enqueue_scripts', [ $scripts, 'register' ] );
	add_action( 'wp_head', [ $scripts, 'render_script_templates' ] );

	// Routing for Cookies Static Pages
	$template_loader = new Loader();
	add_filter( 'template_include', [ $template_loader, 'load_template' ], 10, 1 );

	add_action( 'init', [ $template_loader, 'user_settings_rewrite' ], 10 );
	add_filter( 'query_vars', [ $template_loader, 'user_settings_query_var' ], 10, 1 );

	// Activate Cookie Consent Popup
	$popup = new Popup();
	add_action( 'wp_footer', [ $popup, 'render' ], 100 );

	// Register Cookie Table Shortcode
	$shortcode = new Shortcode();
	add_shortcode( 'cookie_table', [ $shortcode, 'render_cookie_table' ] );

	// Add CLI commands
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		\WP_CLI::add_command( 'macs-cookies', __NAMESPACE__ . '\\Cli' );
	}
}

/**
 * Loads plugin textdomain
 */
function load_translations(): void {
	load_plugin_textdomain( 'macs_cookies', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Checks if all requirements for the plugin are met
 *
 * Called early in run() function to stop plugin initialization if something is missing.
 * 
 * @return bool
 */
function check_requirements(): bool {

	// Check if FieldManager Exists
	if ( ! defined( '\FM_VERSION' ) ) {
		return false;
	}
	return true;
}

/**
 * MACS Cookie settings visible only for site admin
 */
function remove_macs_cookie_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_menu_page( 'edit.php?post_type=macs_cookie' );
	}
}
add_action( 'admin_menu', __NAMESPACE__ . '\\remove_macs_cookie_settings', 10, 1 );
