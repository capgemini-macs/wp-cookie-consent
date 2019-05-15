<?php
/**
 * Allows to set an individual group of cookies, all groups or don't allow at all
 *
 * @package CapGemini
 */

namespace CG\Cookie_Consent;

function register_meta_box() {

	if ( function_exists( 'fm_register_submenu_page' ) ) {
		fm_register_submenu_page( 'cg_cookie', 'options-general.php', __( 'Cookie', 'cg-cookie-consent' ), null, 'edit_pages' );
	}

	add_action('fm_submenu_cg_cookie', function () {

		$fm_cookie_fields = [];

		$fm_cookie_fields['cookie-necessary-gtm'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-necessary-gtm',
			'label' => 'Cookie nececary GTM-TAG. Enter your container ID eg. GTM-123ABC',
		]);

		$fm_cookie_fields['cookie-preferences-gtm'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-preferences-gtm',
			'label' => 'Cookie preferences GTM-TAG. Enter your container ID eg. GTM-123ABC',
		]);

		$fm_cookie_fields['cookie-statistics-gtm'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-statistics-gtm',
			'label' => 'Cookie statistics GTM-TAG. Enter your container ID eg. GTM-123ABC',
		]);

		$fm_cookie_fields['cookie-title'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-title',
			'label' => 'Cookie title',
		]);

		$fm_cookie_fields['cookie-text'] = new \Fieldmanager_RichTextArea([
			'name'  => 'cookie-text',
			'label' => 'Cookie text',
		]);

		$fm_cookie_fields['cookie-necessary'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-necessary',
			'label' => 'Checkbox necessary description',
		]);

		$fm_cookie_fields['cookie-preferences'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-preferences',
			'label' => 'Checkbox preferences description',
		]);

		$fm_cookie_fields['cookie-statistics'] = new \Fieldmanager_TextField([
			'name'  => 'cookie-statistics',
			'label' => 'Checkbox statistics description',
		]);

		$fm_cookie_fields['decline-button'] = new \Fieldmanager_TextField([
			'name'  => 'decline-button',
			'label' => 'Decline button description',
		]);

		$fm_cookie_fields['accept-button'] = new \Fieldmanager_TextField([
			'name'  => 'accept-button',
			'label' => 'Accept button description',
		]);

		$fm_group = new \Fieldmanager_Group( [
			'name'           => 'cg_cookie',
			'collapsible'    => true,
			'children'       => $fm_cookie_fields,
			'add_to_prefix'  => false,
			'serialize_data' => true,
		] );

		$fm_group->activate_submenu_page();
	} );
}

if ( is_admin() ) {
	register_meta_box();
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'cookie-consent', esc_url( plugin_dir_url( __FILE__ ) ) . '../assets/css/cg-cookie-consent.css', [], false );
		wp_enqueue_script( 'cookie-consent-js', esc_url( plugin_dir_url( __FILE__ ) ) . '../assets/js/cg-cookie-consent.js', array( ), false );
		wp_add_inline_script( 'cookie-consent-js', 'window.onload = runCookiesPlugin', 'after' );
		$fm_cookie_fields = get_option( 'cg_cookie', [] );

		if ( ! empty( $fm_cookie_fields['cookie-title'] ) ) { 
			$title = esc_html__( $fm_cookie_fields['cookie-title'] );
		} else { 
			$title = esc_html__( 'This website uses cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-text'] ) ) { 
			$text = wp_kses( 
				$fm_cookie_fields['cookie-text'],
				[
					'strong' => [],
					'em'     => [],
					'b'      => [],
					'i'      => [],
					'p'      => [],
					'a'      => [
						'href'  => [],
						'title' => [],
					],
				]
			);
		} else { 
			$text = esc_html__( 'We use cookies to ensure the proper functionning of this website, to remember your preferences, and, generally, to improve your experience. We also use cookies to propose personalized contents, social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services. You have the right to choose which cookie can be used to trace your navigation on the website. Please unselect the checkbox if you which to withdraw your consent.', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-necessary'] ) ) { 
			$cookie_necessary = esc_html__( $fm_cookie_fields['cookie-necessary'] );
		} else { 
			$cookie_necessary = esc_html__( 'Necessary Cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-preferences'] ) ) { 
			$cookie_preferences = esc_html__( $fm_cookie_fields['cookie-preferences'] );
		} else { 
			$cookie_preferences = esc_html__( 'Preferences Cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-statistics'] ) ) { 
			$cookie_statistics = esc_html__( $fm_cookie_fields['cookie-statistics'] );
		} else { 
			$cookie_statistics = esc_html__( 'Statistics Cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['decline-button'] ) ) { 
			$decline_button = esc_html__( $fm_cookie_fields['decline-button'] );
		} else { 
			$decline_button = esc_html__( 'Decline', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['accept-button'] ) ) { 
			$accept_button = esc_html__( $fm_cookie_fields['accept-button'] );
		} else { 
			$accept_button = esc_html__( 'Accept', 'cg-cookie-consent' );
		}

		wp_localize_script('cookie-consent-js', 'cookie_script_vars', array(
			'title' => $title,
			'text' => $text,
			'cookie_necessary' => $cookie_necessary,
			'cookie_preferences' => $cookie_preferences,
			'cookie_statistics' => $cookie_statistics,
			'decline' => $decline_button,
			'decline_cookie_info' => esc_html__( 'Decline cookie information', 'cg-cookie-consent' ),
			'accept' => $accept_button,
			'accept_cookie_info' => esc_html__( 'Accept cookie information', 'cg-cookie-consent' ),
		));
	}
);

add_action( 'wp_enqueue_scripts', function() {
	$fm_cookie_fields = get_option( 'cg_cookie', [] );
	?>
	<script type='text/plain' data-name='cookie_necessary'>
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php esc_js($fm_cookie_fields['cookie-necessary-gtm']) ?>');
	</script>
	<script type='text/plain' data-name='cookie_preferences'>
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php esc_js($fm_cookie_fields['cookie-preferences-gtm']) ?>');
	</script>
	<script type='text/plain' data-name='cookie_statistics'>
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php esc_js($fm_cookie_fields['cookie-statistics-gtm']) ?>');
	</script>
	<?php
}, 1, 1 ); 
