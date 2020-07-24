<?php
/**
 * Adds custom routes for plugin static pages
 *
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Templates;
use MACS\Cookie_Consent\PostType as PostType;

use Fieldmanager_Group;
use Fieldmanager_TextField;
use Fieldmanager_RichTextArea;

class Loader {

	/**
	 * Loads plugin pages in custom routes.
	 *
	 * Cookie Policy page template replaces plugin's post type archive.
	 * If the archive file is not included in the current theme, will load default template from plugin.
	 *
	 * User preferences page has it's own rewrite rule and uses query var.
	 *
	 * @param  string $template
	 * @return string
	 */
	public function load_template( $template ): string {

		global $wp;

		if ( isset( $wp->query_vars['macs_cookie_user_preferences'] ) && 'true' === $wp->query_vars['macs_cookie_user_preferences'] ) {
			$template = MACS_COOKIE_CONSENT_PATH . 'src/Templates/cookie-user-preferences.php';
		}

		if ( is_post_type_archive( PostType::SLUG ) && ! locate_template( 'macs-cookie-policy.php' ) ) {
			$template = MACS_COOKIE_CONSENT_PATH . 'src/Templates/cookie-policy.php';
		}

		return $template;
	}

	/**
	 * Adds rewrite rule for user preferences page
	 */
	public function user_settings_rewrite(): void {
		add_rewrite_rule( '^cookie-settings$', 'index.php?macs_cookie_user_preferences=true', 'top' );
	}

	/**
	 * Sets custom query var for user preferences page
	 * @param  mixed $public_query_vars
	 * @return array
	 */
	public function user_settings_query_var( $public_query_vars ): array {
		$public_query_vars = (array) $public_query_vars;
		$public_query_vars[] = 'macs_cookie_user_preferences';
		return $public_query_vars;
	}
}
