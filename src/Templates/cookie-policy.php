<?php
/**
 * Template to display cookie policy page
 */

namespace MACS\Cookie_Consent;

use MACS\Cookie_Consent\Contexts\CookiePolicy as CookiePolicy;

$context = new CookiePolicy();

get_header();
	?>

	<header>
		<?php
		get_extended_template_part( 'component/hero/hero-default', null, [
			'title' => __( 'Cookie Policy', 'macs_cookies' ),
		] );
		?>
	</header>

	<article>
		<div class="section container cookie_section cookie_section--policy">
			<div class="row">
				<div class="col-12 main-content section__content article-text cookie_section__article-text cookie_section__article-text--policy">
					<?php $context->render_content(); ?>
				</div>
			</div>
		</div>
	</article>

	<?php
get_footer();
