<?php
/**
 * Macs Cookies CLI commands
 * 
 * @package      MACS\Cookie_Consent
 * @author       Capgemini MACS PL
 * @copyright    Capgemini MACS PL
 * @license      GPL-2.0-or-later
 */

declare(strict_types = 1);

namespace MACS\Cookie_Consent;
use MACS\Cookie_Consent\Settings as Settings;

class Cli extends \WPCOM_VIP_CLI_Command {

	/**
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : test without changing the data
	 *
	 * [--filename]
	 * : csv file in /imports directory in plugin's dir
	 *
	 * ## EXAMPLES
	 *   wp macs-cookies csv_import --fielname=it.csv --dry-run=0
	 *
	 * @alias csv-import
	 */
	function csv_import( $args, $args_assoc ) {

		$this->start_bulk_operation();

		$args_assoc = wp_parse_args( 
			$args_assoc, 
			[
				'dry-run'  => true,
				'filename' => '',
			] 
		);

		$dry_run  = $args_assoc['dry-run'];
		$inserted = 0;

		if ( empty( $args_assoc['filename'] ) ) {
			\WP_CLI::error( 'No filename provided', true );
		}

		$csvfile = MACS_COOKIE_CONSENT_PATH . '/imports/' . $args_assoc['filename'];

		if ( ! file_exists( $csvfile ) ) {
			\WP_CLI::error( 'File does not exist in uploads directory', true );
		}

		if ( $dry_run ) {
			\WP_CLI::line( "-- DRY RUN IS ON -- " );
		}

		// We need to delete existing cookie posts first

		\WP_CLI::line( "Deleting all existing cookie descriptions..." );

		do {

			$posts = get_posts( // phpcs:ignore WordPressVIPMinimum.VIP.RestrictedFunctions.get_posts_get_posts
				[
					'post_type'        => PostType::SLUG,
					'post_status'      => 'any',
					'posts_per_page'   => 100,
					'paged'            => 1,
					'suppress_filters' => false,
					'no_found_rows'    => true,
					'fields'           => 'ids',
				]
			);

			foreach ( $posts as $post_id ) {
				\WP_CLI::line( " -- Deleting cookie post ID [{$post_id}]" );
				if ( ! $dry_run ) {
					wp_delete_post( $post_id, true );
				} else {
					$posts = [];
				}
			}

			$this->stop_the_insanity();

			sleep( 1 );

		} while ( count( $posts ) );

		// Now it's time to insert new posts from CSV

		\WP_CLI::line( "Importing cookie descriptions from CSV file" );

		$csvFileHandler = fopen( $csvfile, 'r' );

		$c = 0;
		while ( ! feof( $csvFileHandler ) ) {

			$c++;

			// ignore col titles
			if ( $c === 1 ) {
				continue;
			}

			$data = fgetcsv( $csvFileHandler );

			$name   = $data[0] ?: '';
			$desc   = $data[2] ?: '';
			$domain = $data[1] ?: '';
			$expiry = $data[5] ?: '';
			$type   = $data[4] ?: '';

			// name is obligatory
			if ( empty( $name ) || 'name' == $name ) {
				continue;
			}

			// Adjust type
			
			switch ( $type ) {
				case 'Targeting/Advertising':
					$type = 'targeting';
					break;

				case 'Necessary':
					$type = 'necessary';
					break;

				case 'Statistic':
					$type = 'statistic';
					break;

				case 'Functional':
					$type = 'functional';
					break;
			}

			$post_data = [
				'ID'           => 0,
				'post_content' => $desc,
				'post_title'   => $name,
				'post_type'    => PostType::SLUG,
				'post_status'  => 'publish',
			];

			if ( ! $dry_run ) {
				$new_id = wp_insert_post( $post_data );

				if ( $new_id ) {

					update_post_meta( $new_id, 'cookie_domain', $domain );
					update_post_meta( $new_id, 'cookie_expiry', $expiry );

					wp_set_object_terms( $new_id, [ $type ], Taxonomy::SLUG, false );

					\WP_CLI::line( "Success! Inserted post: {$name}, type: $type" );
					$inserted++;
				} else {
					\WP_CLI::line( "Failed at inserting a post: {$name}, type: $type" );
				}

			} else {
				\WP_CLI::line( "inserting post: {$name}, type: $type");
			}
		}

		fclose( $csvFileHandler );

		$this->end_bulk_operation();

		\WP_CLI::line( "All done. Inserted posts: {$inserted}" );
	}

	/**
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : test without changing the data
	 *
	 * [--source_id]
	 * : site to copy settings from
	 *
	 * [--destination_id]
	 * : site to copy settings to
	 *
	 * ## EXAMPLES
	 *   wp macs-cookies copy_cookie_pages --dry-run=0
	 *
	 * @alias copy-cookie-pages
	 */
	function copy_cookie_pages( $args, $args_assoc ) {
		$this->start_bulk_operation();

		$args_assoc = wp_parse_args( 
			$args_assoc, 
			[
				'dry-run'        => true,
				'source_id'      => 0,
				'destination_id' => 0,
			] 
		);

		$is_dry_run = $args_assoc['dry-run'];
		$source_id  = $args_assoc['source_id'];
		$dest_id    = $args_assoc['destination_id'];

		if ( empty( $source_id ) ) {
			\WP_CLI::error( 'No source site ID provided. Add --source_id param.', true );
		}

		if ( empty( $dest_id ) ) {
			\WP_CLI::error( 'No destination site ID provided. Add --destination_id param', true );
		}

		if ( $is_dry_run ) {
			\WP_CLI::line( '..:: Running in DRY RUN mode ::.. ' );
		}

		$source_url = get_site_url( $source_id );
		$dest_url   = get_site_url( $dest_id );

		$to_insert = [];

		switch_to_blog( $source_id );

		\WP_CLI::line( ' -- Gathering pages data from site:' . get_bloginfo( 'url' ) );

		$to_insert = get_option( Settings\Pages::GROUP );

		restore_current_blog();

		switch_to_blog( $dest_id );

		if ( get_current_blog_id() !== absint( $dest_id ) ) {
			\WP_CLI::error( 'Error on destination site switch. Aborted.', true );
		}

		\WP_CLI::line( ' -- Updating cookie pages setup on site:' . get_bloginfo( 'url' ) );

		\WP_CLI::line( " -- Updating urls to local site: [{$source_url}] to [{$dest_url}] " );

		if ( isset( $to_insert[ 'policy' ][ 'policy_text' ] ) ) {
			$to_insert[ 'policy' ][ 'policy_text' ] = str_replace( $source_url, $dest_url, $to_insert[ 'policy' ][ 'policy_text' ] );
		}

		if ( isset( $to_insert[ 'preferences' ][ 'intro_text' ] ) ) {
			$to_insert[ 'preferences' ][ 'intro_text' ] = str_replace( $source_url, $dest_url, $to_insert[ 'preferences' ][ 'intro_text' ] );
		}

		if ( isset( $to_insert[ 'preferences' ][ 'other_items_text' ] ) ) {
			$to_insert[ 'preferences' ][ 'other_items_text' ] = str_replace( $source_url, $dest_url, $to_insert[ 'preferences' ][ 'other_items_text' ] );
		}

		if ( isset( $to_insert[ 'popup' ][ 'content' ] ) ) {
			$to_insert[ 'popup' ][ 'content' ] = str_replace( $source_url, $dest_url, $to_insert[ 'popup' ][ 'content' ] );
		}

		if ( ! $is_dry_run ) {
			update_option( Settings\Pages::GROUP, $to_insert, null );
		}

		restore_current_blog();

		\WP_CLI::line( ' - Done!' );

		$this->end_bulk_operation();
	}

	/**
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : test without changing the data
	 *
	 * [--source_id]
	 * : site to copy settings from
	 *
	 * [--destination_id]
	 * : site to copy settings to
	 *
	 * ## EXAMPLES
	 *   wp macs-cookies copy_cookie_terms --dry-run=0
	 *
	 * @alias copy-cookie-terms
	 */
	function copy_cookie_terms( $args, $args_assoc ) {
		$this->start_bulk_operation();

		$args_assoc = wp_parse_args( 
			$args_assoc, 
			[
				'dry-run'        => true,
				'source_id'      => 0,
				'destination_id' => 0,
			] 
		);

		$is_dry_run = $args_assoc['dry-run'];
		$source_id  = $args_assoc['source_id'];
		$dest_id    = $args_assoc['destination_id'];

		if ( empty( $source_id ) ) {
			\WP_CLI::error( 'No source site ID provided. Add --source_id param.', true );
		}

		if ( empty( $dest_id ) ) {
			\WP_CLI::error( 'No destination site ID provided. Add --destination_id param', true );
		}

		if ( $is_dry_run ) {
			\WP_CLI::line( '..:: Running in DRY RUN mode ::.. ');
		}

		$to_insert = [];

		switch_to_blog( $source_id );

		\WP_CLI::line( ' -- Gathering data from site:' . get_bloginfo( 'url' ) );

		$terms = get_terms( 
			[
				'taxonomy'   => Taxonomy::SLUG,
				'hide_empty' => false,
			]
		);

		foreach ( $terms as $term ) {

			\WP_CLI::line( " -- Getting data of a term [{$term->name}]");

			$to_insert[ $term->name ] = [
				'description' => $term->description,
				'slug'        => $term->slug,
				'order'       => get_term_meta( $term->term_id, 'cookie_type_order', true ),
			];
		}

		restore_current_blog();

		switch_to_blog( $dest_id );

		if ( get_current_blog_id() !== absint( $dest_id ) ) {
			\WP_CLI::error( 'Error on destination site switch. Aborted.', true );
		}

		\WP_CLI::line( ' -- Deleting existing terms on site:' . get_bloginfo( 'url' ) );

		$terms = get_terms( 
			[
				'taxonomy'   => Taxonomy::SLUG,
				'hide_empty' => false,
				'fields'     => 'ids',
			]
		);

		foreach ( $terms as $term_id ) {
			\WP_CLI::line( " -- Deleting term ID [{$term_id}]" );

			if ( ! $is_dry_run ) {
				wp_delete_term( $term_id, Taxonomy::SLUG );
			}
		}

		\WP_CLI::line( ' -- Inserting cookie posts to site:' . get_bloginfo( 'url' ) );

		foreach ( $to_insert as $term_name => $term_data ) {

			if ( ! $is_dry_run ) {
				$term_order = $term_data['order'] ?? 1;
				unset( $term_data['order'] );
				$new_term = wp_insert_term( $term_name, Taxonomy::SLUG, $term_data );

				if ( isset( $new_term['term_id'] ) ) {
					update_term_meta( $new_term['term_id'], 'cookie_type_order', $term_order );
				}
			}

			if ( $is_dry_run ) {
				$new_term['term_id'] = 'TBD';
			}

			\WP_CLI::line( ' -- Inserted new term id:' . $new_term['term_id'] );
		}

		restore_current_blog();

		\WP_CLI::line( ' - Done!' );

		$this->end_bulk_operation();
	}

	/**
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : test without changing the data
	 *
	 * [--source_id]
	 * : site to copy settings from
	 *
	 * [--destination_id]
	 * : site to copy settings to
	 *
	 * ## EXAMPLES
	 *   wp macs-cookies copy_cookie_table --dry-run=0
	 *
	 * @alias copy-cookie-table
	 */
	function copy_cookie_table( $args, $args_assoc ) {
		$this->start_bulk_operation();

		$args_assoc = wp_parse_args( 
			$args_assoc, 
			[
				'dry-run'        => true,
				'source_id'      => 0,
				'destination_id' => 0,
			] 
		);

		$is_dry_run = $args_assoc['dry-run'];
		$source_id  = $args_assoc['source_id'];
		$dest_id    = $args_assoc['destination_id'];

		if ( empty( $source_id ) ) {
			\WP_CLI::error( 'No source site ID provided. Add --source_id param.', true );
		}

		if ( empty( $dest_id ) ) {
			\WP_CLI::error( 'No destination site ID provided. Add --destination_id param', true );
		}

		if ( $is_dry_run ) {
			\WP_CLI::line( '..:: Running in DRY RUN mode ::.. ' );
		}

		$to_insert = [];

		switch_to_blog( $source_id );

		\WP_CLI::line( ' -- Gathering data from site:' . get_bloginfo( 'url' ) );

		$posts_page = 1;

		do {

			$posts = get_posts( // phpcs:ignore WordPressVIPMinimum.VIP.RestrictedFunctions.get_posts_get_posts
				[
					'post_type'        => PostType::SLUG,
					'post_status'      => 'any',
					'posts_per_page'   => 100,
					'paged'            => $posts_page,
					'suppress_filters' => false,
					'no_found_rows'    => true,
				]
			);

			foreach ( $posts as $post_obj ) {

				\WP_CLI::line( " -- Getting data from cookie post ID [{$post_obj->ID}]" );

				$post_arr = $post_obj->to_array();
				$type     = wp_get_object_terms( $post_obj->ID, Taxonomy::SLUG, [ 'fields' => 'slugs' ] );
				$expiry   = get_post_meta( $post_obj->ID, 'cookie_expiry', true );
				$domain   = get_post_meta( $post_obj->ID, 'cookie_domain', true );

				$post_arr['meta_input'] = [
					'cookie_expiry' => $expiry,
					'cookie_domain' => $domain,
				];

				$post_arr['tax_input'] = [
					Taxonomy::SLUG => $type,
				];

				unset( $post_arr['ID'] );

				$to_insert[] = $post_arr;
			}

			$this->stop_the_insanity();

			sleep( 1 );

			$posts_page++;

		} while ( count( $posts ) );

		restore_current_blog();

		switch_to_blog( $dest_id );

		if ( get_current_blog_id() !== absint( $dest_id ) ) {
			\WP_CLI::error( 'Error on destination site switch. Aborted.', true );
		}

		\WP_CLI::line( ' -- Deleting existing cookie posts on site:' . get_bloginfo( 'url' ) );

		do {

			$posts = get_posts( // phpcs:ignore WordPressVIPMinimum.VIP.RestrictedFunctions.get_posts_get_posts
				[
					'post_type'        => PostType::SLUG,
					'post_status'      => 'any',
					'posts_per_page'   => 100,
					'paged'            => 1,
					'suppress_filters' => false,
					'no_found_rows'    => true,
					'fields'           => 'ids',
				]
			);

			foreach ( $posts as $post_id ) {
				\WP_CLI::line( " -- Deleting cookie post ID [{$post_id}]" );
				if ( ! $is_dry_run ) {
					wp_delete_post( $post_id, true );
				} else {
					$posts = [];
				}
			}

			$this->stop_the_insanity();

			sleep( 1 );

		} while ( count( $posts ) );

		\WP_CLI::line( ' -- Inserting cookie posts to site:' . get_bloginfo( 'url' ) );

		foreach ( $to_insert as $new_item ) {
			if ( ! $is_dry_run ) {
				$new_id = wp_insert_post( $new_item );
				// reinserting the taxonomy terms (might fail before due to caps restriction)
				$type_terms = $new_item['tax_input'][ Taxonomy::SLUG ] ?? [];
				wp_set_object_terms( $new_id, $type_terms, Taxonomy::SLUG );
			} else {
				$new_id = 0;
			}

			\WP_CLI::line( ' -- Inserted new cookie post id:' . $new_id );
		}

		if ( ! $is_dry_run ) {
			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- command is aimed to run on the initial plugin setup, so the cookie post type archive might not be accessible on the front-end before flushing the rules.
			flush_rewrite_rules();
		}

		restore_current_blog();

		\WP_CLI::line( ' - Done!' );

		$this->end_bulk_operation();
	}
}
