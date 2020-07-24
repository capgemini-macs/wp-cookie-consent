<?php
/**
 * Controller for Cookie Policy page
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Contexts;

use MACS\Cookie_Consent\Settings\Pages as Pages;

class CookiePolicy extends BaseContext {

	protected $data = [];

	public function __construct() {
		$data = Pages::get_page_data('policy');
		$this->data = $data ?: [];
	}

	public function render_content(): void {
		$data = $this->data['policy_text'] ?: '';
		echo wp_kses_post( apply_filters( 'the_content', $data ) );
	}
}