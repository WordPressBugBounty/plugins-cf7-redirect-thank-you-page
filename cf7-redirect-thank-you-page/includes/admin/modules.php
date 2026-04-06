<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Get all available modules
 * This function returns an array of all modules that can be enabled/disabled
 * 
 * @return array Array of modules with their settings
 */
function cf7rl_get_available_modules() {
	$modules = array(
		'redirect' => array(
			'name' => __( 'Redirect & Thank You Page', 'cf7rl' ),
			'description' => __( 'Enable redirect functionality for Contact Form 7 forms. Allows you to redirect users to a URL or display a thank you page after form submission.', 'cf7rl' ),
			'default' => true,
			'enabled' => true
		),
		'payments' => array(
			'name' => __( 'PayPal & Stripe Payments', 'cf7rl' ),
			'description' => __( 'Accept payments through Contact Form 7 forms via PayPal or Stripe. Supports PayPal Commerce Platform and Stripe Connect with seamless checkout experience and automatic payment tracking.', 'cf7rl' ),
			'default' => false,
			'enabled' => false
		),
		'recaptcha' => array(
			'name' => __( 'Google reCAPTCHA v2', 'cf7rl' ),
			'description' => __( 'Protect your Contact Form 7 forms from spam with Google reCAPTCHA v2. Adds a verification challenge to prevent automated bot submissions while maintaining a user-friendly experience.', 'cf7rl' ),
			'default' => false,
			'enabled' => false
		),
		'country_phone' => array(
			'name' => __( 'Country & Phone Fields', 'cf7rl' ),
			'description' => __( 'Add country dropdown and international phone number fields to your Contact Form 7 forms. Includes country selection with auto-detection, international dial codes, and customizable country lists.', 'cf7rl' ),
			'default' => false,
			'enabled' => false
		),
		'database_submissions' => array(
			'name' => __( 'Database Submissions', 'cf7rl' ),
			'description' => __( 'Track and store all Contact Form 7 submissions in the database. View submission history, export to CSV, and manage form data from the WordPress admin dashboard.', 'cf7rl' ),
			'default' => false,
			'enabled' => false
		),
		'bookings' => array(
			'name' => __( 'Bookings & Appointments', 'cf7rl' ),
			'description' => __( 'Add appointment booking functionality to your Contact Form 7 forms. Includes date/time picker fields, availability management, and automatic booking tracking with calendar integration.', 'cf7rl' ),
			'default' => false,
			'enabled' => false
		),
		'material_theme' => array(
			'name' => __( 'Material Theme', 'cf7rl' ),
			'description' => __( 'Apply Google Material Design styling to your Contact Form 7 forms. Adds modern, clean Material Design UI components with floating labels, ripple effects, and smooth animations.', 'cf7rl' ),
			'default' => false,
			'enabled' => false
		)
	);
	
	// Allow other plugins/themes to add their own modules
	$modules = apply_filters( 'cf7rl_available_modules', $modules );
	
	return $modules;
}


/**
 * Get enabled modules
 * 
 * @return array Array of enabled module keys
 */
function cf7rl_get_enabled_modules() {
	$options = get_option( 'cf7rl_options' );
	
	// If option doesn't exist or modules key doesn't exist, enable default modules
	if ( ! isset( $options['modules'] ) ) {
		$available_modules = cf7rl_get_available_modules();
		$enabled = array();
		
		foreach ( $available_modules as $key => $module ) {
			if ( isset( $module['default'] ) && $module['default'] === true ) {
				$enabled[] = $key;
			}
		}
		
		return $enabled;
	}
	
	// If modules key exists (even if empty array), return it as-is
	return $options['modules'];
}


/**
 * Check if a specific module is enabled
 * 
 * @param string $module_key The module key to check
 * @return bool True if enabled, false otherwise
 */
function cf7rl_is_module_enabled( $module_key ) {
	$enabled_modules = cf7rl_get_enabled_modules();
	return in_array( $module_key, $enabled_modules );
}


/**
 * Display the modules management page
 * 
 * @return string HTML output for the modules page
 */
function cf7rl_modules_page() {
	$available_modules = cf7rl_get_available_modules();
	$enabled_modules = cf7rl_get_enabled_modules();
	
	$output = "";
	
	$output .= "<table style='width: 100%;' class='cf7rl-modules-table'>";
	
	foreach ( $available_modules as $key => $module ) {
		$is_enabled = in_array( $key, $enabled_modules );
		$checked = $is_enabled ? 'checked' : '';
		
		$output .= "<tr class='cf7rl-module-row'>";
		$output .= "<td style='width: 80px; vertical-align: middle; padding: 15px 10px;'>";
		$output .= "<label class='cf7rl-switch'>";
		$output .= "<input type='checkbox' name='cf7rl_modules[]' value='" . esc_attr( $key ) . "' " . $checked . " id='cf7rl_module_" . esc_attr( $key ) . "'>";
		$output .= "<span class='cf7rl-slider'></span>";
		$output .= "</label>";
		$output .= "</td>";
		$output .= "<td style='padding: 15px 10px;'>";
		$output .= "<label for='cf7rl_module_" . esc_attr( $key ) . "' style='font-weight: bold; font-size: 14px; cursor: pointer;'>" . esc_html( $module['name'] ) . "</label>";
		$output .= "<p style='margin: 5px 0 0 0; color: #666;'>" . esc_html( $module['description'] ) . "</p>";
		$output .= "</td>";
		$output .= "</tr>";
	}
	
	$output .= "</table>";
	
	return $output;
}
