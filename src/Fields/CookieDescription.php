<?php
/**
 * Cookie Field - Description
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Fields;

final class CookieDescription extends BaseCookieField {
	const NAME       = 'cookie_desc';
	const TYPE       = 'text';
	const COLUMN     = true;
	const METABOX    = false;

	public static function get_value( int $post_id ): string {
		return get_the_content( '', false, $post_id );
	}

	public static function get_label(): string {
		return __( 'Description', 'macs_cookies' );
	}
}
