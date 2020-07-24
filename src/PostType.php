<?php
/**
 * Registers Cookie Post Type
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

/**
 * Registers Cookie Post Type
 *
 * This post type is used to store human readable information on cookies, 
 * which can be later displayed on cookie policy or user preferences page as a table.
 */
class PostType {

	const SLUG = 'macs_cookie';

	/**
	 * Registers Post Type
	 */
	public function register() {
		$args = [
			'label'               => __( 'Cookie', 'macs_cookies' ),
			'description'         => __( 'Cookie Item', 'macs_cookies' ),
			'labels'              => [
				'name'                  => _x( 'Cookies', 'Post Type General Name', 'macs_cookies' ),
				'singular_name'         => _x( 'Cookie', 'Post Type Singular Name', 'macs_cookies' ),
			],
			'supports'            => [ 'title', 'editor' ],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 75,
			'menu_icon'           => 'dashicons-buddicons-community',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'rewrite'             => [ 'slug' => 'macs_cookies' ],
			'has_archive'         => 'cookie-policy',
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'manage_options',
			'show_in_rest'        => true,
		];

		register_post_type( self::SLUG, $args );
	}
}
