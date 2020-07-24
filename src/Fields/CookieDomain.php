<?php
/**
 * Cookie Field - Domain
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Fields;

final class CookieDomain extends BaseCookieField {
	const NAME       = 'cookie_domain';
	const TYPE       = 'text';
	const COLUMN     = true;
	const METABOX    = true;

	public static function get_label(): string {
		return __( 'Domain', 'macs_cookies' );
	}
}
