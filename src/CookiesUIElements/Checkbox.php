<?php

/**
 * Renders Cookie Checkbox based on taxonomy settings
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\CookiesUIElements;

use MACS\Cookie_Consent\Taxonomy as Taxonomy;

class Checkbox {

	public static function render( string $cookie_type ) {
		$type_term = get_term_by( 'slug', $cookie_type, Taxonomy::SLUG );

		if ( ! $type_term instanceof \WP_Term ) {
			return;
		}
		?>
		<div class="cookieConsent__checkbox_container">
			<div class="cookieConsent__checkbox">
				<input type="checkbox" id="accept_cookie_<?php echo esc_attr( $cookie_type ); ?>" name="accept_cookies[]" value="<?php echo esc_attr( $cookie_type ); ?>" <?php checked( 'necessary' === $type_term->slug ); disabled( 'necessary' === $type_term->slug ); ?>>
				<label for="accept_cookie_<?php echo esc_attr( $cookie_type ); ?>">
					<span class="cookieConsent__checkbox_title">
						<?php
						// translators: %s is cookie type name i.e. 'Functional Cookies'
						echo sprintf( esc_html__( 'I accept "%s"', 'macs_cookies' ), esc_html( $type_term->name ) );
						?>
					</span>
				</label>
			</div>
			<span class="cookieConsent__checkbox_text">
				<?php echo wp_kses_post( $type_term->description ); ?>
			</span>
		</div>
		<?php
	}
}
