<?php
/**
 * Renders Cookie Table
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent\CookiesUIElements;

use MACS\Cookie_Consent\Taxonomy as Taxonomy;
use MACS\Cookie_Consent\PostType as PostType;
use MACS\Cookie_Consent\Fields as Fields;

class CookieTable {

	protected $cookie_types = [];

	public function __construct( array $cookie_types = [] ) {
		$this->cookie_types = $cookie_types;
	}

	protected function get_types(): array {
		if ( ! empty( $this->cookie_types ) ) {
			return $this->cookie_types;
		}

		$cookie_types = get_terms( [
			'taxonomy' => Taxonomy::SLUG,
			'fields'   => 'slugs',
			'meta_key' => 'cookie_type_order',
			'orderby'  => 'meta_value_num',
		] );

		return is_wp_error( $cookie_types ) ? [] : $cookie_types;
	}

	protected function get_by_type( string $cookie_type ): array {

		$args = [
			'post_type'           => PostType::SLUG,
			'fields'              => 'ids',
			'status'              => 'public',
			'oderby'              => 'title',
			'order'               => 'ASC',
			'posts_per_page'      => 100,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'tax_query'           => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- it's either this or high ppp
				[
					'taxonomy' => Taxonomy::SLUG,
					'field'    => 'slug',
					'terms'    => $cookie_type,
				],
			],
		];

		$items_query = new \WP_Query( $args );

		if ( ! is_array( $items_query->posts ) ) {
			return [];
		}
		
		return array_map( 
			function( $cookie_id ) {
				return [
					'name'        => get_the_title( $cookie_id ),
					'domain'      => Fields\CookieDomain::get_value( $cookie_id ),
					'description' => Fields\CookieDescription::get_value( $cookie_id ),
					'expiry'      => Fields\CookieExpiry::get_value( $cookie_id ),
				];
			},
			$items_query->posts 
		);
	}

	public function render() {
		$types = $this->get_types();

		foreach( $types as $cookie_type ) {
			$data      = $this->get_by_type( $cookie_type );
			$type_term = get_term_by( 'slug', $cookie_type, Taxonomy::SLUG );

			if ( empty( $data ) || ! $type_term instanceof \WP_Term ) {
				continue;
			}
			?>

			<div class="row">
				<div class="col-12 col-md-10">
					<h2 class="section__title"><?php echo esc_html( $type_term->name ); ?></h2>
				</div>
			</div>

			<div class="row">
				<div class="col-12 main-content section__content article-text">
					<div class="cookieConsent__table">
						<table>
							<tbody>
								<tr>
									<th><?php esc_html_e( 'Cookie name', 'macs_cookies' ); ?></th>
									<th><?php esc_html_e( 'Domain', 'macs_cookies' ); ?></th>
									<th><?php esc_html_e( 'Description', 'macs_cookies' ); ?></th>
									<th><?php esc_html_e( 'Expiry Date', 'macs_cookies' ); ?></th>
								</tr>
								<?php foreach( $data as $item ) { 
									$name        = $item['name'] ?: '';
									$domain      = $item['domain'] ?: '';
									$description = $item['description'] ?: '';
									$expiry      = $item['expiry'] ?: '';
									?>
									<tr>
										<td><?php echo esc_html( $name ); ?></td>
										<td><?php echo esc_html( $domain ); ?></td>
										<td><?php echo esc_html( $description ); ?></td>
										<td><?php echo esc_html( $expiry ); ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php
		}
	}
}
