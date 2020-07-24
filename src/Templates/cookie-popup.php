<?php
/**
 * Template to display cookie consent popup
 */

namespace MACS\Cookie_Consent;

use MACS\Cookie_Consent\Contexts\Popup as CookiePopup;

$context = new CookiePopup();

?>
<div class="cookieConsent__overlay <?php $context->render_overlay(); ?>"></div>
<div class="cookieConsent__popup " id="popup-cookieConsent">
	<div class="container">
		<h3 class="cookieConsent__popup--title"><?php $context->render_title(); ?></h3>
		<div class="row">
			<div class="cookieConsent__popup--text col-12 col-lg-9">
				<?php $context->render_content(); ?>
			</div>
			<div class="cookieConsent__popup--buttons col-12 col-lg-3">
				<a href="<?php echo esc_url( home_url() ); ?>" id="macs_cookies_accept_all" class="macs_cookies_accept_all cookieConsent__popup--buttons_button" target="_self"><?php esc_html_e( 'Allow all cookies', 'macs_cookies' ); ?></a>
				<a href="<?php echo esc_url( home_url( 'cookie-settings' ) ); ?>" id="" class="cookieConsent__popup--buttons_button" target="_self"><?php esc_html_e( 'Manage cookie settings', 'macs_cookies' ); ?></a>
				<a href="<?php echo esc_url( home_url() ); ?>" id="macs_cookies_accept_necessary" class="macs_cookies_accept_necessary cookieConsent__popup--buttons_button" target="_self"><?php esc_html_e( 'Decline all cookies', 'macs_cookies' ); ?></a>
			</div>
		</div>
	</div>
	<button type="button" class="cookieConsent__popup--close macs_cookies_accept_necessary <?php $context->render_button(); ?>"><span class="sr-only"><?php esc_html_e( 'Accept only necessary cookies and close window', 'macs_cookies' ); ?></span></button>
</div>
