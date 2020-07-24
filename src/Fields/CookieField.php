<?php
/**
 * Cookie Field Interface
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Fields;

interface CookieField {
	public static function get_value( int $post_id ): string;
	public static function get_trimmed_value( int $post_id ): string;
	public static function get_label(): string;
}
