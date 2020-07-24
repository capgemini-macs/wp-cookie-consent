<?php

/**
 * Gets Cookie Pages Settings
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Settings;

abstract class Pages {

	/**
	 * Name of option group where data is stored
	 */
	const GROUP = 'macs_cookies_pages';

	/**
	 * Returns an array of settings for Cookie Pages
	 * (values of Fieldmanager Groups)
	 * 
	 * @param  string $page policy|settings|popup
	 * @return array
	 */
	public static function get_page_data( string $page ): array {
		$data = get_option( self::GROUP );
		return $data[ $page ] ?: [];
	}
}
