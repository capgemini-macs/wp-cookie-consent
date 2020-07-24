<?php
/**
 * Base Cookie Field
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Fields;

/**
 * Extensions of this class store definitions of Cookie Post (cookie items descriptions) data. 
 * We refer to their constants to work with post meta.
 * Static methods are helpers used to retrieve meta from DB.
 * This allows us to change meta keys easily or quickly switch metaboxes / admin columns on or off
 * or modify meta field types.
 */
abstract class BaseCookieField implements CookieField {

	public static function get_value( int $post_id ): string {
		return get_post_meta( $post_id, static::NAME, true );
	}

	public static function get_trimmed_value( int $post_id ): string {
		return mb_strimwidth( static::get_value( $post_id ), 0, 160, '...' );
	}

	/**
	 * Gets label for this post meta (i.e. when rendering admin columns)
	 * We can't use a constant here due to i18n
	 * @return string
	 */
	abstract public static function get_label(): string;
}
