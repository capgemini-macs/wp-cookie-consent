<?php
/**
 * Cookie Field - Expiry
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Fields;

final class CookieExpiry extends BaseCookieField {
	const NAME       = 'cookie_expiry';
	const TYPE       = 'text';
	const COLUMN     = true;
	const METABOX    = true;

	public static function get_label(): string {
		return __( 'Expiry', 'macs_cookies' );
	}
}
