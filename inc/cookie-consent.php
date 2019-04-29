<?php
/**
 * Allows to set an individual group of cookies, all groups or don't allow at all
 *
 * @package CapGemini
 */
namespace CG\Cookie_Consent;

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'cookie-consent', esc_url( plugin_dir_url( __FILE__ ) ) . '../assets/css/cg-cookie-consent.css', [], false );
		wp_enqueue_script( 'cookie-consent-js', esc_url( plugin_dir_url( __FILE__ ) ) . '../assets/js/cg-cookie-consent.js', array( ), false );
		wp_add_inline_script( 'cookie-consent-js', 'window.onload = runCookiesPlugin', 'after' );
		wp_localize_script('cookie-consent-js', 'cookie_script_vars', array(
			'title' => esc_html__( 'This website uses cookies', 'capgemini' ),
			'text' => esc_html__( 'We use cookies to personalise content and ads, to provide social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services.', 'capgemini' ),
			'cookie_necessary' => esc_html__( 'Necessary', 'capgemini' ),
			'cookie_preferences' => esc_html__( 'Preferences', 'capgemini' ),
			'cookie_statistics' => esc_html__( 'Statistics', 'capgemini' ),
			'decline' => esc_html__( 'Decline', 'capgemini' ),
			'decline_cookie_info' => esc_html__( 'Decline cookie information', 'capgemini' ),
			'accept' => esc_html__( 'Accept', 'capgemini' ),
			'accept_cookie_info' => esc_html__( 'Accept cookie information', 'capgemini' ),
		));
	}
);
