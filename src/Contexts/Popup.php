<?php
/**
 * Controller for Cookie consent popup
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Contexts;

use MACS\Cookie_Consent\Settings\Pages as Pages;

class Popup extends BaseContext {

	protected $data = [];

	public function __construct() {
		$data = Pages::get_page_data('popup');
		$this->data = $data ?: [];
	}

	public function render_title(): void {
		$data = $this->data['title'] ?: '';
		echo wp_kses_post( apply_filters( 'the_content', $data ) );
	}

	public function render_content(): void {
		$data = $this->data['content'] ?: '';
		echo wp_kses_post( apply_filters( 'the_content', $data ) );
	}

	public function render_overlay(): void {
		$data = $this->data['overlay'] ?: '';
		echo esc_attr( $data );
	}

	public function render_button(): void {
		$data = $this->data['button'] ?: '';
		echo esc_attr( $data );
	}

	public function render_decline(): void {
		$data = $this->data['decline'] ?: '';
		echo esc_attr( $data );
	}
}