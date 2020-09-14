<?php
/**
 * Loads Cookie Scripts and Styles
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;

use MACS\Cookie_Consent\Settings\Plugin as Plugin;

class Scripts {

	/**
	 * Registers plugin scripts and styles
	 */
	public function register() {
		wp_enqueue_script( 'macs-cookies-es5', MACS_COOKIE_CONSENT_URL . '/assets/js/macs-cookies-es5.js', [ 'jquery', 'lodash' ], MACS_COOKIE_CONSENT_JS_VER, true );

		wp_localize_script( 
			'macs-cookies-es5',
			'MACS_COOKIES',
			[
				'coookiePolicyVersion' => Plugin::get_policy_version(),
				'ID'                   => crc32( get_site_url() ),
				'embedCookiesYtb'      => esc_html__( 'Please allow statistical cookies to see this Youtube embed', 'macs_cookies' ),
				'embedCookiesSnd'      => esc_html__( 'Please allow statistical cookies to see this Soundcloud embed', 'macs_cookies' ),
			]
		);
		
		wp_enqueue_style( 'macs-cookies-css', MACS_COOKIE_CONSENT_URL . '/assets/css/macs-cookie-consent.css', [], MACS_COOKIE_CONSENT_JS_VER );
	}

	/**
	 * Renders GTM scripts for each container (one container per one cookie group)
	 */
	public function render_script_templates() {

		$gtm_containers = Plugin::get_containers();

		$this->render_data_layer();
		$this->render_overall_statistics_script();

		foreach( $gtm_containers as $type => $id ) :
			if ( empty( $id ) ) {
				continue;
			}
			?>		
			<script type="text/plain" class="macs_cookies_gtm_<?php echo esc_attr( $type ); ?>">
				(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','<?php echo esc_attr( $id ); ?>');
			</script>
			<?php
		endforeach;
	}

	/**
	 * Renders custom data layer items (uses 'macs_cookies_gtm_datalayer' filter)
	 */
	protected function render_data_layer(): void {
		$data = apply_filters( 'macs_cookies_gtm_datalayer', [] );

		if ( ! empty( $data ) ) {
			echo sprintf( '<script>var dataLayer = %s;</script>',
				wp_json_encode( array( $data ) )
			);
		}
	}

	protected function render_overall_statistics_script() {

		$id = apply_filters( 'macs_cookies_network_statistics_id', '' );

		if ( empty( $id ) ) {
			return;
		}
		?>
			<script type="text/plain" class="macs_cookies_gtm_statistics">
				(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','<?php echo esc_attr( $id ); ?>');
			</script>
		<?php
	}
}
