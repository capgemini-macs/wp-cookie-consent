<?php
/**
 * Template to display user preferences page
 */

namespace MACS\Cookie_Consent;

use MACS\Cookie_Consent\Contexts\UserPreferences as UserPreferences;

get_header();

	$context = new UserPreferences();
	?>

	<header>
		<?php do_action( 'macs_cookies_preferences_header' ); ?>
	</header>

	<div class="content">
		<section class="section container cookie_section cookie_section--settings">
			<div class="row">
				<div class="col-12 main-content section__content article-text cookie_section__article-text cookie_section__article-text--settings">
					<?php $context->render_intro_content(); ?>
				</div>
			</div>

			<div class="row">
				<div class="col-12 col-md-10">
					<h2 class="section__title"><?php echo esc_html_e( 'Set-up your preferences', 'macs_cookies' ); ?></h2>
				</div>
			</div>

			<div class="row">
				<div class="col-12 main-content section__content article-text cookie_section__article-text">					
						<?php $context->render_consent_checkboxes(); ?>						

						<div class="col-12 text-left">
							<a href="#" class="section__button" id="macs_cookies_save_preferences"><?php esc_html_e( 'Save my settings', 'macs_cookies' ); ?></a> <span class="macs_cookies_saved"><?php esc_html_e( 'Settings saved!', 'macs_cookies' ); ?></span>
						</div>

					<h2><?php esc_html_e( 'Other non-cookie technologies', 'macs_cookies' ); ?></h2>
					<?php $context->render_other_content(); ?>
				</div>
				<div class="col-12 main-content section__content article-text cookie_section__article-text">
					<p>
					<?php 
					$policy_url = get_bloginfo( 'url' ) . '/cookie-policy';
					// translators: %s is cookie policy url
					printf( __( 'For more information related to the cookies, please visit our <a href="%s">cookie policy</a>', 'macs_cookies' ), esc_url( $policy_url ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- dynamic part escaped
					?>
					</p>
				</div>
			</div>
		</section>
	</div>

	<?php
get_footer();
