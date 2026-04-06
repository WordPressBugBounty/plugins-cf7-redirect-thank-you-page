<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enqueue country phone module styles and scripts
 */
add_action('wp_enqueue_scripts', 'cf7rl_country_phone_enqueue_frontend');
function cf7rl_country_phone_enqueue_frontend() {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('country_phone')) {
		return;
	}
	
	wp_enqueue_style(
		'cf7rl-country-phone',
		CF7RL_URL . 'assets/css/country_phone.css',
		array(),
		'1.0.0'
	);
	
	// Add inline CSS for custom widths
	$options = cf7rl_get_country_phone_options();
	$country_width = !empty($options['country_dropdown_width']) ? intval($options['country_dropdown_width']) : 200;
	$phone_width = !empty($options['phone_dropdown_width']) ? intval($options['phone_dropdown_width']) : 100;
	
	$custom_css = "
		.wpcf7-countryselect {
			max-width: {$country_width}px !important;
		}
		.wpcf7-tel-dialcode {
			max-width: {$phone_width}px !important;
		}
		.cf7rl-phone-group .cf7rl-select-container {
			max-width: {$phone_width}px !important;
		}
		.cf7rl-phone-group input[type=\"tel\"] {
			width: {$phone_width}px !important;
			max-width: {$phone_width}px !important;
		}
	";
	wp_add_inline_style('cf7rl-country-phone', $custom_css);
	
	wp_enqueue_script(
		'cf7rl-country-phone',
		CF7RL_URL . 'assets/js/country_phone.js',
		array('jquery'),
		'1.0.0',
		true
	);
	
	// Debug script for phone field spacing issues - TEMPORARY
	wp_enqueue_script(
		'cf7rl-country-phone-debug',
		CF7RL_URL . 'assets/js/country_phone_debug.js',
		array('jquery', 'cf7rl-country-phone'),
		'1.0.0',
		true
	);
}

/**
 * Enqueue admin styles for settings page
 */
add_action('admin_enqueue_scripts', 'cf7rl_country_phone_admin_enqueue');
function cf7rl_country_phone_admin_enqueue($hook) {
	// Load on all admin pages (lightweight, only affects country dropdowns)
	// This ensures it works regardless of menu structure
	
	// Enqueue custom CSS for searchable dropdowns
	wp_enqueue_style(
		'cf7rl-country-phone-admin',
		CF7RL_URL . 'assets/css/country_phone_admin.css',
		array(),
		'1.0.3'
	);
	
	// Enqueue custom JavaScript for searchable dropdowns
	// Add wpcf7-admin-taggenerator dependency if on CF7 pages
	$dependencies = array('jquery');
	if (strpos($hook, 'wpcf7') !== false) {
		$dependencies[] = 'wpcf7-admin-taggenerator';
	}
	
	wp_enqueue_script(
		'cf7rl-country-phone-admin',
		CF7RL_URL . 'assets/js/country_phone_admin.js',
		$dependencies,
		'1.0.3',
		true
	);
}
