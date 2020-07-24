<?php

/**
 * Creates settings page for plugin options
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
use Fieldmanager_Radios;
use Fieldmanager_TextField;
use Fieldmanager_RichTextArea;

class CookiePluginSettings {

	/**
	 * Registers settings page
	 */
	public function register_settings_page(): void {
		fm_register_submenu_page( Plugin::GROUP, 'edit.php?post_type=macs_cookie', __( 'Cookie Plugin Setup', 'macs_cookies' ), null, 'edit_pages' );

		add_action(
			'fm_submenu_macs_cookies_setup',
			[ $this, 'register_settings' ]
		);
	}

	/**
	 * Registers settings
	 */
	public function register_settings(): void {
		$settings = new Fieldmanager_Group( [
			'name'     => Plugin::GROUP,
			'children' => [
				'gtm'            => new Fieldmanager_Group(
					[
						'label'          => __( 'GTM Containers', 'macs_cookies' ),
						'collapsible'    => false,
						'children'       => [
							'necessary' => new Fieldmanager_Textfield(
								[
									'label' => 'Necessary Cookies Container',
								]
							),
							'functional' => new Fieldmanager_Textfield(
								[
									'label' => 'Functional Cookies Container',
								]
							),
							'statistics' => new Fieldmanager_Textfield(
								[
									'label' => 'Statistics Cookies Container',
								]
							),
							'targeting' => new Fieldmanager_Textfield(
								[
									'label' => 'Advertising Cookies Container',
								]
							),
						],
						'add_to_prefix'  => false,
						'serialize_data' => true,
					]
				),
				'policy_version' => new Fieldmanager_TextField(
					[
						'label'         => __( 'Cookie Policy Version', 'macs_cookies' ),
						'description'   => __( sprintf( 'Change to a different value every time cookie policy or cookie item is updated. This will result in showing the consent popup again to all users to make sure they accept current policy contents. You can use any value here, but timestamp is recommended. Current timestamp is %s.', time() ), 'macs_cookies' ),
						'default_value' => time(),
					]
				),
				'status'         => new Fieldmanager_Radios( 
					[
						'label'         => __( 'Plugin status', 'macs_cookies' ),
						'options'       => [
							'off' => 'Off',
							'on'  => 'On',
						],
						'default_value' => 'off',
					]
				),
			]
		] );

		$settings->activate_submenu_page();
	}
}
