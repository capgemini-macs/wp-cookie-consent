<?php

/**
 * Gets Plugin Settings
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Settings;

abstract class Plugin {

	/**
	 * Name of option group where data is stored
	 */
	const GROUP = 'macs_cookies_setup';

	/**
	 * Returns GTM containers for cookie types according on dashboard settings
	 * @return array
	 */
	public static function get_containers(): array {
		$data = get_option( self::GROUP );
		return $data['gtm'] ?? [];
	}

	/**
	 * Returns plugin status according do dashboard setting
	 * @return string
	 */
	public static function status(): string {
		$data = get_option( self::GROUP );
		return $data['status'] ?? 'off';
	}

	/**
	 * Returns cookie policy verision setting
	 * @return string
	 */
	public static function get_policy_version(): string {
		$data = get_option( self::GROUP );
		return $data['policy_version'] ?? '';
	}
}
