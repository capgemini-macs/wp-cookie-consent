<?php
/**
 * Plugin Name: MACS Cookie Consent
 *
 * This file should only use syntax available in PHP 5.6 or later.
 *
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       MACS Cookie Consent
 * Plugin URI:        https://github.com/wpcomvip/capgemini
 * Description:       Cookie Consent plugin for GTM based setup.
 * Version:           0.1.0
 * Author:            Lech Dulian, Elżbieta Wróbel (Capgemini MACS PL)
 * Text Domain:       macs_cookies
 * License:           GPL-2.0-or-later
 * Requires PHP:      7.3
 * Requires WP:       4.7
 */

namespace MACS\Cookie_Consent;

require plugin_dir_path( __FILE__ ) . '/autoloader.php';
require plugin_dir_path( __FILE__ ) . '/init.php';
