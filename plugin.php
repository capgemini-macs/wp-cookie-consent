<?php
/**
 * Plugin Name: Capgemini Cookie Consent
 * Description: Allows to set an individual group of cookies, all groups or don't allow at all
 * Plugin URI: https://capgemini.com
 * Author: Capgemini
 * Author URI: https://capgemini.com
 * License: GPLv2
 * Text Domain: cg-cookie-consent
 * Domain Path: /languages/
 *
 * @package CapGemini
 * @author  Mirosław Mac <miroslaw.mac@capgemini.com>
 */

namespace CG\Cookie_Consent;

// Get external libraries.
$libs = [
	'fieldmanager/fieldmanager.php',
];

foreach ( $libs as $lib ) {
	require_once WP_PLUGIN_DIR . "/{$lib}";
}

require_once __DIR__ . '/inc/cookie-consent.php';

function load_translations() {
	load_plugin_textdomain( 'cg-cookie-consent', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', __NAMESPACE__ . '\\load_translations' );
