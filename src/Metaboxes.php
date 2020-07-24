<?php
/**
 * Registers Cookie Post metaboxes
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

use Fieldmanager_TextField;
use Fieldmanager_TextArea;

class Metaboxes {

	protected $fields = [];

	/**
	 * Constructor
	 * @param array $fields fully qualified paths to cookie field classes
	 */
	public function __construct( array $fields ) {
		$this->fields = $fields;
	}

	public function register() {
		foreach( $this->fields as $field ) {

			if ( ! $field::METABOX ) {
				continue;
			}

			$args = [
				'name' => $field::NAME,
			];

			switch( $field::TYPE ) {
				case 'textarea':
					$fm = new Fieldmanager_TextArea( $args );
					break;

				default:
					$fm = new Fieldmanager_TextField( $args );
			}

			$fm->add_meta_box( 
				$field::get_label(), 
				PostType::SLUG
	    	);
		}
	}

	/**
	 * Removes useless metaboxes
	 */
	public function deregister_other(): void {
		$screens = [ PostType::SLUG ];

		remove_meta_box( 'fm_meta_box_notifications', $screens, 'normal' );
		remove_meta_box( 'fm_meta_box_Images', $screens, 'normal' );
		remove_meta_box( 'fm_meta_box_owner', $screens, 'normal' );
		remove_meta_box( 'fm_meta_box_post_author_override', $screens, 'normal' );
		remove_meta_box( 'edit-flow-editorial-comments', $screens, 'normal' );
	}
}
