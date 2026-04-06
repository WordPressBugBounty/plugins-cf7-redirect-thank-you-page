<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if Material Theme is enabled for a specific form
 * 
 * @param int $form_id The Contact Form 7 form ID
 * @return bool True if enabled, false otherwise
 */
function cf7rl_is_material_theme_enabled($form_id) {
	$enabled = get_post_meta($form_id, '_cf7rl_enable_material_theme', true);
	return ($enabled == '1');
}

/**
 * Get Material Theme options for a specific form
 * 
 * @param int $form_id The Contact Form 7 form ID
 * @return array Material Theme options
 */
function cf7rl_get_material_theme_options($form_id) {
	// Get form-specific settings with defaults
	$primary_color = get_post_meta($form_id, '_cf7rl_material_primary_color', true);
	$background_color = get_post_meta($form_id, '_cf7rl_material_background_color', true);
	$vertical_spacing = get_post_meta($form_id, '_cf7rl_material_vertical_spacing', true);
	$enable_floating_labels = get_post_meta($form_id, '_cf7rl_material_floating_labels', true);
	
	$options = array(
		'primary_color' => $primary_color !== '' ? $primary_color : '#1976d2',
		'background_color' => $background_color !== '' ? $background_color : '#d7d7d7',
		'vertical_spacing' => $vertical_spacing !== '' ? $vertical_spacing : '24',
		'enable_floating_labels' => $enable_floating_labels !== '' ? $enable_floating_labels : '0', // Disabled by default
	);
	
	return $options;
}

/**
 * Add Material Theme class to form wrapper
 */
function cf7rl_add_material_theme_class($class) {
	// Get the current form ID from the global contact form object
	if (function_exists('wpcf7_get_current_contact_form')) {
		$contact_form = wpcf7_get_current_contact_form();
		if ($contact_form) {
			$form_id = $contact_form->id();
			
			if (cf7rl_is_material_theme_enabled($form_id)) {
				$class .= ' cf7rl-material-theme';
			}
		}
	}
	
	return $class;
}
add_filter('wpcf7_form_class_attr', 'cf7rl_add_material_theme_class', 10, 1);

/**
 * Add inline styles for Material Theme colors
 */
function cf7rl_material_theme_inline_styles() {
	if (is_admin()) {
		return;
	}
	
	// Check if any form has Material Theme enabled
	global $wpdb;
	$forms_with_material = $wpdb->get_results(
		"SELECT post_id, meta_value FROM {$wpdb->postmeta} 
		WHERE meta_key = '_cf7rl_enable_material_theme' 
		AND meta_value = '1'"
	);
	
	if (empty($forms_with_material)) {
		return;
	}
	
	$custom_css = '';
	
	foreach ($forms_with_material as $form) {
		$form_id = $form->post_id;
		$options = cf7rl_get_material_theme_options($form_id);
		
		$primary_color = esc_attr($options['primary_color']);
		$background_color = esc_attr($options['background_color']);
		$vertical_spacing = intval($options['vertical_spacing']);
		
		// Calculate darker background for focus state (20% darker)
		$focus_bg = cf7rl_darken_color($background_color, 20);
		
		$custom_css .= "
		/* Form ID: {$form_id} - Primary: {$primary_color}, Background: {$background_color} */
		
		/* Vertical spacing - applied to form control wrappers */
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] .wpcf7-form-control-wrap {
			margin-bottom: {$vertical_spacing}px !important;
		}
		
		/* Phone group should not have margin - the parent .cf7rl-phone-wrap already has it */
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] .cf7rl-phone-group {
			margin-bottom: 0 !important;
		}
		
		/* Background color for ALL input types */
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='text'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='email'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='url'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='tel'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='number'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='date'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] textarea,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] select {
			background: {$background_color} !important;
		}
		
		/* Primary color for focused inputs - border bottom */
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='text']:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='email']:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='url']:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='tel']:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='number']:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='date']:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] textarea:focus,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] select:focus {
			border-bottom-color: {$primary_color} !important;
			background: {$focus_bg} !important;
		}
		
		/* Primary color for active labels */
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] .cf7rl-material-label.active {
			color: {$primary_color} !important;
		}
		
		/* Primary color for submit button */
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='submit'],
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] button[type='submit'] {
			background-color: {$primary_color} !important;
		}
		
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] input[type='submit']:hover,
		.wpcf7-form.cf7rl-material-theme[data-form-id='{$form_id}'] button[type='submit']:hover {
			background-color: {$primary_color} !important;
			opacity: 0.9;
		}
		";
	}
	
	if (!empty($custom_css)) {
		wp_add_inline_style('cf7rl-material-theme-style', $custom_css);
	}
}
add_action('wp_enqueue_scripts', 'cf7rl_material_theme_inline_styles', 25);

/**
 * Darken a hex color by a percentage
 */
function cf7rl_darken_color($hex, $percent) {
	$hex = str_replace('#', '', $hex);
	
	if (strlen($hex) == 3) {
		$hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
	}
	
	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));
	
	$r = max(0, min(255, $r - ($r * $percent / 100)));
	$g = max(0, min(255, $g - ($g * $percent / 100)));
	$b = max(0, min(255, $b - ($b * $percent / 100)));
	
	// Round to integers before converting to hex
	$r = round($r);
	$g = round($g);
	$b = round($b);
	
	return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * Add form ID as data attribute for styling
 */
function cf7rl_add_form_id_data_attribute($atts) {
	// Get the current contact form
	if (function_exists('wpcf7_get_current_contact_form')) {
		$contact_form = wpcf7_get_current_contact_form();
		if ($contact_form) {
			$form_id = $contact_form->id();
			
			if (cf7rl_is_material_theme_enabled($form_id)) {
				$atts['data-form-id'] = $form_id;
			}
		}
	}
	
	return $atts;
}
add_filter('wpcf7_form_additional_atts', 'cf7rl_add_form_id_data_attribute', 10, 1);
