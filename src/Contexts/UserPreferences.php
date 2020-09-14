<?php
/**
 * Controller for Cookie User Preferences page
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Contexts;

use MACS\Cookie_Consent\Settings\Pages as Pages;
use MACS\Cookie_Consent\CookiesUIElements\Checkbox as Checkbox;
use MACS\Cookie_Consent\Taxonomy as Taxonomy;

class UserPreferences extends BaseContext {

	protected $data = [];

	public function __construct() {
		$data = Pages::get_page_data('preferences');
		$this->data = $data ?: [];
	}

	public function render_intro_content(): void {
		$data = $this->data['intro_text'] ?: '';
		echo wp_kses_post( apply_filters( 'the_content', $data ) );
	}

	public function render_other_content(): void {
		$data = $this->data['other_items_text'] ?: '';
		echo wp_kses_post( apply_filters( 'the_content', $data ) );
	}

	public function render_consent_checkboxes() {

		$necessary_obj = get_term_by( 'slug', 'necessary', Taxonomy::SLUG );

		$args = [
			'taxonomy'   => Taxonomy::SLUG,
			'order'      => 'ASC',
			'orderby'    => 'cookie_order',
			'hide_empty' => false,
			'meta_query' => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'cookie_order' => [
					'key'  => 'cookie_type_order',
					'type' => 'NUMERIC',
				],
			],
		];

		// exclude 'necessary' type to re-insert it later as first element of cookie types list
		if ( $necessary_obj instanceof \WP_Term ) {
			$args[ 'exclude' ] = $necessary_obj->term_id;
		}

		$terms = get_terms( $args );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			$terms = [];
		}

		// re-insert necessary term as first item of our terms array
		if ( $necessary_obj instanceof \WP_Term ) {
			array_unshift( $terms, $necessary_obj );
		}

		foreach( $terms as $term ) {
			Checkbox::render( $term->slug );
		}
	}
}