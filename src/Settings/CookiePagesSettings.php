<?php

/**
 * Creates settings page for plugin static pages
 *
 * Uses Fieldmanager Plugin
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\Settings;

use Fieldmanager_Group;
use Fieldmanager_TextField;
use Fieldmanager_RichTextArea;
use Fieldmanager_Radios;

class CookiePagesSettings {

	public function register_settings_page() {
		fm_register_submenu_page( Pages::GROUP, 'edit.php?post_type=macs_cookie', __( 'Cookie Pages Setup', 'macs_cookies' ), null, 'edit_pages' );

		add_action(
			'fm_submenu_macs_cookies_pages',
			[ $this, 'register_settings' ]
		);
	}

	public function register_settings() {
		$settings = new Fieldmanager_Group( [
			'name'     => Pages::GROUP,
			'tabbed'   => 'horizontal',
			'children' => [
				'policy' => new Fieldmanager_Group(
					[
						'label'          => __( 'Cookie Policy', 'macs_cookies' ),
						'collapsible'    => false,
						'children'       => [
							'policy_text' => new Fieldmanager_RichTextArea(
								[
									'label' => 'Main Content',
									'buttons_1' => [ 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'link' ],
									'buttons_2' => [],
									'editor_settings' => [
										'quicktags'     => false,
										'media_buttons' => false,
										'editor_height' => 400,
									],
								]
							),
						],
						'add_to_prefix'  => false,
						'serialize_data' => true,
					]
				),
				'preferences' => new Fieldmanager_Group(
					[
						'label'          => __( 'User Preferences Page', 'macs_cookies' ),
						'collapsible'    => false,
						'children'       => [
							'intro_text' => new Fieldmanager_RichTextArea(
								[
									'label' => 'Intro Text',
									'buttons_1' => [ 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'link' ],
									'buttons_2' => [],
									'editor_settings' => [
										'quicktags'     => false,
										'media_buttons' => false,
										'editor_height' => 250,
									],
								]
							),
							'other_items_text' => new Fieldmanager_RichTextArea(
								[
									'label' => '"Other Non-Cookie Technologies" Field',
									'buttons_1' => [ 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'link' ],
									'buttons_2' => [],
									'editor_settings' => [
										'quicktags'     => false,
										'media_buttons' => false,
										'editor_height' => 250,
									],
								]
							),
						],
						'add_to_prefix'  => false,
						'serialize_data' => true,
					]
				),
				'popup' => new Fieldmanager_Group(
					[
						'label'          => __( 'Popup', 'macs_cookies' ),
						'collapsible'    => false,
						'children'       => [
							'title' => new Fieldmanager_TextField(
								[
									'label' => 'Popup Title',
								]
							),
							'content' => new Fieldmanager_RichTextArea(
								[
									'label' => 'Popup Text',
									'buttons_1' => [ 'formatselect', 'bold', 'italic', 'bullist', 'numlist', 'link' ],
									'buttons_2' => [],
									'editor_settings' => [
										'quicktags'     => false,
										'media_buttons' => false,
										'editor_height' => 300,
									],
								]
							),
							'overlay'         => new Fieldmanager_Radios( 
								[
									'label'         => __( 'Full Screen Overlay', 'macs_cookies' ),
									'options'       => [
										'overlay-off' => __( 'Off', 'macs_cookies' ),
										'overlay-on'  => __( 'On', 'macs_cookies' ),
									],
									'default_value' => 'overlay-off',
								]
							),
							'button'         => new Fieldmanager_Radios( 
								[
									'label'         => __( 'Hide close button', 'macs_cookies' ),
									'options'       => [
										'button-off' => __( 'Off', 'macs_cookies' ),
										'button-on'  => __( 'On', 'macs_cookies' ),
									],
									'default_value' => 'button-off',
								]
							),
							'decline'         => new Fieldmanager_Radios( 
								[
									'label'         => __( 'Hide "Decline All" button', 'macs_cookies' ),
									'options'       => [
										'decline-off' => __( 'Off', 'macs_cookies' ),
										'decline-on'  => __( 'On', 'macs_cookies' ),
									],
									'default_value' => 'decline-off',
								]
							),

						],
						'add_to_prefix'  => false,
						'serialize_data' => true,
					]
				),
			]
		] );

		$settings->activate_submenu_page();
	}
}
