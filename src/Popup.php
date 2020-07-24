<?php
/**
 * Renders Cookie Consent Popup
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

class Popup {
	public function render() {
		require MACS_COOKIE_CONSENT_PATH . 'src/Templates/cookie-popup.php';
	}
}
