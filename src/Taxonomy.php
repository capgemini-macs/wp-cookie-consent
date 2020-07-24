<?php
/**
 * Registers Cookie Type taxonomy
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

use Fieldmanager_TextField;

/**
 * Registers Cookie Type taxonomy
 *
 * This is used to categorize cookies into seperate groups
 * and then display it as a table/summary on cookie policy or user preferences pages.
 */
class Taxonomy {

	const SLUG = 'macs_cookie_type';

	/**
	 * Registers Taxonomy
	 */
	public function register(): void {

		$args = array(
			'labels'                     => [
				'name'          => _x( 'Cookie Type', 'Taxonomy General Name', 'macs_cookies' ),
				'singular_name' => _x( 'Cookie Type', 'Taxonomy Singular Name', 'macs_cookies' ),
				'menu_name'     => __( 'Cookie Type', 'macs_cookies' ),
			],
			'hierarchical'               => true,
			'public'                     => false,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
		);

		register_taxonomy( self::SLUG, [ PostType::SLUG ], $args );
	}

	/**
	 * Deregisters exisiting taxonomies
	 */
	public function deregister_other(): void {
		$taxonomies = get_taxonomies( [], 'names', 'and' );

		foreach( $taxonomies as $tax ) {

			if ( $tax === self::SLUG ) {
				continue;
			}

			unregister_taxonomy_for_object_type( $tax, PostType::SLUG );
		}
	}

	/**
	 * Registers order field
	 * Uses Fieldmanager
	 */
	public function register_order_field(): void {
		$fm = new Fieldmanager_TextField( [
			'name' => 'cookie_type_order',
		] );
		
		$fm->add_term_meta_box( __( 'Order', 'macs_cookies' ), self::SLUG );
	}
}
