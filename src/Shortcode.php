<?php
/**
 * Registers Shortcode for Cookie Table
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

use MACS\Cookie_Consent\CookiesUIElements\CookieTable as CookieTable;

class Shortcode {

	public function render_cookie_table( $atts ) {
		ob_start();
		$table = new CookieTable();
		$table->render();
		return ob_get_clean();
	}
}
