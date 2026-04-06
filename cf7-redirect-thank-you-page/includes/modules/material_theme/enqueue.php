<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Enqueue Material Theme styles and scripts
 */
function cf7rl_enqueue_material_theme_assets() {
	// Only enqueue on frontend
	if (is_admin()) {
		return;
	}
	
	// Check if Contact Form 7 is active
	if (!function_exists('wpcf7_enqueue_scripts')) {
		return;
	}
	
	// Check if any form has Material Theme enabled
	global $wpdb;
	$has_material_theme = $wpdb->get_var(
		"SELECT COUNT(*) FROM {$wpdb->postmeta} 
		WHERE meta_key = '_cf7rl_enable_material_theme' 
		AND meta_value = '1'"
	);
	
	if (!$has_material_theme) {
		return;
	}
	
	// Enqueue Material Theme CSS
	wp_enqueue_style(
		'cf7rl-material-theme-style',
		plugins_url('assets/css/material_theme.css', dirname(dirname(dirname(__FILE__)))),
		array(),
		'1.0'
	);
	
	// Enqueue Material Theme JavaScript
	wp_enqueue_script(
		'cf7rl-material-theme-script',
		plugins_url('assets/js/material_theme.js', dirname(dirname(dirname(__FILE__)))),
		array('jquery'),
		'1.0',
		true
	);
	
	// Pass form-specific settings to JavaScript
	$forms_with_material = $wpdb->get_results(
		"SELECT post_id FROM {$wpdb->postmeta} 
		WHERE meta_key = '_cf7rl_enable_material_theme' 
		AND meta_value = '1'"
	);
	
	$material_settings = array();
	foreach ($forms_with_material as $form) {
		$form_id = $form->post_id;
		$options = cf7rl_get_material_theme_options($form_id);
		$material_settings[$form_id] = $options;
	}
	
	wp_localize_script(
		'cf7rl-material-theme-script',
		'cf7rlMaterialTheme',
		array(
			'settings' => $material_settings
		)
	);
}
add_action('wp_enqueue_scripts', 'cf7rl_enqueue_material_theme_assets', 20);

/**
 * Enqueue admin styles for Material Theme settings
 */
function cf7rl_enqueue_material_theme_admin_assets($hook) {
	// Only load on Contact Form 7 edit pages
	if ($hook !== 'toplevel_page_wpcf7' && strpos($hook, 'wpcf7') === false) {
		return;
	}
	
	// Only if Material Theme module is enabled
	if (!cf7rl_is_module_enabled('material_theme')) {
		return;
	}
	
	wp_enqueue_style(
		'cf7rl-material-theme-admin-style',
		plugins_url('assets/css/material_theme_admin.css', dirname(dirname(dirname(__FILE__)))),
		array(),
		'1.0'
	);
}
add_action('admin_enqueue_scripts', 'cf7rl_enqueue_material_theme_admin_assets');
