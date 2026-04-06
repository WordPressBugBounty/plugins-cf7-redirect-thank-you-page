<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enqueue Google reCAPTCHA script
 * We enqueue it on all frontend pages where CF7 is loaded
 * The actual display is controlled per-form
 */
function cf7rl_enqueue_recaptcha_scripts() {
	// Only enqueue on frontend
	if (is_admin()) {
		return;
	}
	
	// Check if Contact Form 7 is active
	if (!function_exists('wpcf7_enqueue_scripts')) {
		return;
	}
	
	// Check if any form has reCAPTCHA enabled
	global $wpdb;
	$has_recaptcha = $wpdb->get_var(
		"SELECT COUNT(*) FROM {$wpdb->postmeta} 
		WHERE meta_key = '_cf7rl_enable_recaptcha' 
		AND meta_value = '1'"
	);
	
	if (!$has_recaptcha) {
		return;
	}
	
	// Enqueue reCAPTCHA CSS
	wp_enqueue_style(
		'cf7rl-recaptcha-style',
		plugins_url('assets/css/recaptcha.css', dirname(dirname(dirname(__FILE__)))),
		array(),
		'1.0'
	);
	
	// Enqueue Google reCAPTCHA API with explicit rendering
	// Using ?render=explicit allows us to control rendering and callbacks
	wp_enqueue_script(
		'google-recaptcha',
		'https://www.google.com/recaptcha/api.js?render=explicit',
		array(),
		null,
		true
	);
	
	// Enqueue our custom reCAPTCHA handler
	wp_enqueue_script(
		'cf7rl-recaptcha-handler',
		plugins_url('assets/js/recaptcha.js', dirname(dirname(dirname(__FILE__)))),
		array('google-recaptcha'),
		'1.0',
		true
	);
}
add_action('wp_enqueue_scripts', 'cf7rl_enqueue_recaptcha_scripts', 20);
