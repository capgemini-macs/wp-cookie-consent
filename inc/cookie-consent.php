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

	add_action(
		'fm_submenu_cg_cookie',
		function () {

			$fm_cookie_fields = [];

			$fm_cookie_fields['cookie-necessary-gtm'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-necessary-gtm',
					'label' => 'Cookie necessary GTM-TAG. Enter your container ID eg. GTM-123ABC',
				]
			);

			$fm_cookie_fields['cookie-preferences-gtm'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-preferences-gtm',
					'label' => 'Cookie preferences GTM-TAG. Enter your container ID eg. GTM-123ABC',
				]
			);

			$fm_cookie_fields['cookie-statistics-gtm'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-statistics-gtm',
					'label' => 'Cookie statistics GTM-TAG. Enter your container ID eg. GTM-123ABC',
				]
			);

			$fm_cookie_fields['cookie-title'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-title',
					'label' => 'Cookie title',
				]
			);

			$fm_cookie_fields['cookie-text'] = new \Fieldmanager_RichTextArea(
				[
					'name'  => 'cookie-text',
					'label' => 'Cookie text',
				]
			);

			$fm_cookie_fields['cookie-necessary'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-necessary',
					'label' => 'Checkbox necessary description',
				]
			);

			$fm_cookie_fields['cookie-preferences'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-preferences',
					'label' => 'Checkbox preferences description',
				]
			);

			$fm_cookie_fields['cookie-statistics'] = new \Fieldmanager_TextField(
				[
					'name'  => 'cookie-statistics',
					'label' => 'Checkbox statistics description',
				]
			);

			$fm_cookie_fields['decline-button'] = new \Fieldmanager_TextField(
				[
					'name'  => 'decline-button',
					'label' => 'Decline button description',
				]
			);

			$fm_cookie_fields['accept-button'] = new \Fieldmanager_TextField(
				[
					'name'  => 'accept-button',
					'label' => 'Accept button description',
				]
			);

			$fm_group = new \Fieldmanager_Group(
				[
					'name'           => 'cg_cookie',
					'collapsible'    => true,
					'children'       => $fm_cookie_fields,
					'add_to_prefix'  => false,
					'serialize_data' => true,
				]
			);

			$fm_group->activate_submenu_page();
		}
	);
}

if ( is_admin() ) {
	register_meta_box();
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'cookie-consent', esc_url( plugin_dir_url( __FILE__ ) ) . '../assets/css/cg-cookie-consent.css', [], false );
		wp_enqueue_script( 'cookie-consent-js', esc_url( plugin_dir_url( __FILE__ ) ) . '../assets/js/cg-cookie-consent.js', [ 'lodash' ], '1.0.16' );
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
			$text = esc_html__( 'We use cookies to ensure the proper functioning of this website, to remember your preferences, and, generally, to improve your experience. We also use cookies to propose personalized contents, social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services. You have the right to choose which cookie can be used to trace your navigation on the website. Please unselect the checkbox if you which to withdraw your consent.', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-necessary'] ) ) {
			$cookie_necessary = esc_html( $fm_cookie_fields['cookie-necessary'] );
		} else {
			$cookie_necessary = esc_html__( 'Necessary Cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-preferences'] ) ) {
			$cookie_preferences = esc_html( $fm_cookie_fields['cookie-preferences'] );
		} else {
			$cookie_preferences = esc_html__( 'Preferences Cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['cookie-statistics'] ) ) {
			$cookie_statistics = esc_html( $fm_cookie_fields['cookie-statistics'] );
		} else {
			$cookie_statistics = esc_html__( 'Statistics Cookies', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['decline-button'] ) ) {
			$decline_button = esc_html( $fm_cookie_fields['decline-button'] );
		} else {
			$decline_button = esc_html__( 'Decline', 'cg-cookie-consent' );
		}

		if ( ! empty( $fm_cookie_fields['accept-button'] ) ) {
			$accept_button = esc_html( $fm_cookie_fields['accept-button'] );
		} else {
			$accept_button = esc_html__( 'Accept', 'cg-cookie-consent' );
		}

		wp_localize_script(
			'cookie-consent-js',
			'cookie_script_vars',
			array(
				'title'               => $title,
				'text'                => $text,
				'cookie_necessary'    => $cookie_necessary,
				'cookie_preferences'  => $cookie_preferences,
				'cookie_statistics'   => $cookie_statistics,
				'decline'             => $decline_button,
				'decline_cookie_info' => esc_html__( 'Decline cookie information', 'cg-cookie-consent' ),
				'accept'              => $accept_button,
				'accept_cookie_info'  => esc_html__( 'Accept cookie information', 'cg-cookie-consent' ),
			)
		);
	}
);

add_action(
	'wp_head',
	function() {
		$fm_cookie_fields = get_option( 'cg_cookie', [] );

		if ( ! empty( $fm_cookie_fields['cookie-necessary-gtm'] ) ) :
			?>
			<script type='text/plain' data-name='cookie_necessary'>
				(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $fm_cookie_fields['cookie-necessary-gtm'] ); ?>');
			</script>
			<?php
		endif;

		if ( ! empty( $fm_cookie_fields['cookie-preferences-gtm'] ) ) :
			?>
			<script type='text/plain' data-name='cookie_preferences'>
				(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $fm_cookie_fields['cookie-preferences-gtm'] ); ?>');
			</script>
			<?php
		endif;

		if ( ! empty( $fm_cookie_fields['cookie-statistics-gtm'] ) ) :
			?>
			<script type='text/plain' data-name='cookie_statistics'>
				(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $fm_cookie_fields['cookie-statistics-gtm'] ); ?>');
			</script>
			<?php
		endif;
		?>
		
		<script type='text/javascript'>
			var dataLayerItems = <?php echo wp_json_encode( data_layers() ); ?>
		</script>
		<?php
	},
	1,
	1
);

/**
 * Outputs the dataLayer object
 *
 * Use cg_cookies_data_layer_{$type} filter to add custom values to the dataLayer
 * available $types:
 * - necessary
 * - preferences
 * - statistics
 *
 * add_filter( 'cg_cookies_data_layer_necessary', function( $data ) {
 *     $data['my_var'] = 'hello';
 *     return $data;
 * } );
 *
 * 
 * @return string
 */
function data_layers() {

	$data = [
		'cookie_necessary'   => [],
		'cookie_preferences' => [],
		'cookie_statistics'  => [],
	];


	if ( is_user_logged_in() ) {
		$user                                  = wp_get_current_user();
		$data['cookie_necessary']['logged_in'] = $user->get( 'user_nicename' );
		$data['cookie_necessary']['role']      = implode( ',', array_keys( $user->caps ) );
	}

	if ( is_404() ) {
		$data['cookie_statistics']['404'] = true;
	}

	if ( is_multisite() ) {
		$data['cookie_statistics']['blog']    = home_url();
		$data['cookie_statistics']['network'] = network_home_url();
	}

	if ( is_front_page() ) {
		$data['cookie_statistics']['front_page'] = true;
	}

	if ( is_singular() ) {
		$data['cookie_statistics']['post_type'] = get_post_type();
		$data['cookie_statistics']['post_id']   = get_the_ID();
	}
	if ( is_archive() ) {
		$data['cookie_statistics']['archive'] = true;
		if ( is_date() ) {
			$data['cookie_statistics']['archive'] = 'date';
			$data['cookie_statistics']['date']    = get_the_date();
		}
		if ( is_search() ) {
			$data['cookie_statistics']['archive'] = 'search';
			$data['cookie_statistics']['search']  = get_search_query();
		}
		if ( is_post_type_archive() ) {
			$data['cookie_statistics']['archive'] = get_post_type();
		}
		if ( is_tag() || is_category() || is_tax() ) {
			$data['cookie_statistics']['archive'] = get_queried_object()->taxonomy;
			$data['cookie_statistics']['term']    = get_queried_object()->slug;
		}
		if ( is_author() ) {
			$data['cookie_statistics']['archive'] = 'author';
			$data['cookie_statistics']['author']  = get_queried_object()->user_nicename;
		}
	}

	$data = [
		'cookie_necessary'   => apply_filters( 'cg_cookies_data_layer_necessary', $data['cookie_necessary'] ),
		'cookie_preferences' => apply_filters( 'cg_cookies_data_layer_preferences', $data['cookie_preferences'] ),
		'cookie_statistics'  => apply_filters( 'cg_cookies_data_layer_statistics', $data['cookie_statistics'] ),
	];

	return $data;
}
