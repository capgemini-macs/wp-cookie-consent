<?php
/**
 * Modifies Cookie Post List in the dasboard
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

class CookiePostList {

	protected $fields = [];

	protected $fields_by_name = [];

	/**
	 * Constructor
	 * @param array $fields fully qualified paths to cookie field classes
	 */
	public function __construct( array $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Ads columns for cookie post meta that's set for display in corresponding field class
	 *
	 * @param $columns
	 */
	public function set_custom_columns( $columns ): array {

		$whitelist = $this->get_allowed_cols();
		
		/**
		 * Leave only whitelisted columns
		 * @var array
		 */
		$columns = array_filter( 
			$columns,
			function( $col ) use ( $whitelist ) {
				return in_array( $col, $whitelist, true );
			},
			ARRAY_FILTER_USE_KEY
		);

		foreach( $this->fields as $field ) {
			if ( ! $field::COLUMN ) {
				continue;
			}
			$columns[ $field::NAME ] = $field::get_label();
		}

		return $columns;
	}

	/**
	 * Populates custom column
	 * 
	 * @param  string $column  
	 * @param  int    $post_id
	 */
	public function custom_column_value( string $column, int $post_id ): void {

		$cookie_cols = $this->get_fields_by_name();

		if ( isset( $cookie_cols[ $column ] ) ) {
			$field = $cookie_cols[ $column ];
			echo esc_html( $field::get_trimmed_value( $post_id ) );
		}
	}

	/**
	 * Whitelists basic columns for cookie post type
	 * 
	 * @return array
	 */
	protected function get_allowed_cols(): array {
		$whitelist = [
			'cb',
			'title',
			'taxonomy-' . Taxonomy::SLUG
		];

		return $whitelist;
	}

	/**
	 * Returns a list of fields as name => class array
	 * @return array
	 */
	protected function get_fields_by_name(): array {

		// internal cache
		if ( ! empty( $this->fields_by_name ) ) {
			return $this->fields_by_name;
		}

		$by_name = [];

		foreach( $this->fields as $field ) {
			$by_name[ $field::NAME ] = $field;
		}

		return $by_name;
	}
}
