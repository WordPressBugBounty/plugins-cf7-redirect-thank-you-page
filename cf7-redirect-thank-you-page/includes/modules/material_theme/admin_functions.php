<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Add Material Theme tab to Contact Form 7 editor
 */
function cf7rl_material_theme_editor_panel($panels) {
	$new_panel = array(
		'MaterialTheme' => array(
			'title' => __('Material Theme', 'cf7rl'),
			'callback' => 'cf7rl_material_theme_admin_settings'
		)
	);
	
	$panels = array_merge($panels, $new_panel);
	return $panels;
}
add_filter('wpcf7_editor_panels', 'cf7rl_material_theme_editor_panel');

/**
 * Display Material Theme settings on form edit page
 */
function cf7rl_material_theme_admin_settings($cf7) {
	$post_id = absint($_GET['post']);
	
	$enable_material = get_post_meta($post_id, '_cf7rl_enable_material_theme', true);
	$primary_color = get_post_meta($post_id, '_cf7rl_material_primary_color', true) ?: '#1976d2';
	$background_color = get_post_meta($post_id, '_cf7rl_material_background_color', true) ?: '#d7d7d7';
	$vertical_spacing = get_post_meta($post_id, '_cf7rl_material_vertical_spacing', true);
	if ($vertical_spacing === '') { $vertical_spacing = '24'; }
	$floating_labels = get_post_meta($post_id, '_cf7rl_material_floating_labels', true);
	
	// Set defaults
	// Floating labels disabled by default
	if ($floating_labels === '') { $floating_labels = '0'; }
	
	$checked = ($enable_material == '1') ? 'CHECKED' : '';
	$checked_floating = ($floating_labels == '1') ? 'CHECKED' : '';
	
	$output = '';
	$output .= '<h2>' . __('Material Theme Settings', 'cf7rl') . '</h2>';
	$output .= '<div class="mail-field"></div>';
	
	$output .= '<table class="cf7rl_tabs_table_main"><tr>';
	
	$output .= '<td colspan="3"><b>' . __('General Settings', 'cf7rl') . '</b></td></tr>';
	
	$output .= '<tr><td class="cf7rl_tabs_table_title_width"><label>' . __('Enable Material Theme:', 'cf7rl') . '</label></td>';
	$output .= '<td class="cf7rl_tabs_table_body_width"><input name="cf7rl_enable_material_theme" value="1" type="checkbox" ' . $checked . '></td>';
	$output .= '<td>' . __('Apply Google Material Design styling to this form', 'cf7rl') . '</td></tr>';
	
	$output .= '<tr><td colspan="3"><br /><b>' . __('Appearance Settings', 'cf7rl') . '</b></td></tr>';
	
	$output .= '<tr><td class="cf7rl_tabs_table_title_width"><label>' . __('Primary Color:', 'cf7rl') . '</label></td>';
	$output .= '<td class="cf7rl_tabs_table_body_width"><input type="color" name="cf7rl_material_primary_color" value="' . esc_attr($primary_color) . '"></td>';
	$output .= '<td>' . __('Main color for focused inputs and buttons', 'cf7rl') . '</td></tr>';
	
	$output .= '<tr><td class="cf7rl_tabs_table_title_width"><label>' . __('Background Color:', 'cf7rl') . '</label></td>';
	$output .= '<td class="cf7rl_tabs_table_body_width"><input type="color" name="cf7rl_material_background_color" value="' . esc_attr($background_color) . '"></td>';
	$output .= '<td>' . __('Background color for input fields', 'cf7rl') . '</td></tr>';
	
	$output .= '<tr><td class="cf7rl_tabs_table_title_width"><label>' . __('Vertical Spacing:', 'cf7rl') . '</label></td>';
	$output .= '<td class="cf7rl_tabs_table_body_width"><input type="number" name="cf7rl_material_vertical_spacing" value="' . esc_attr($vertical_spacing) . '" min="0" style="width: 80px;"> px</td>';
	$output .= '<td>' . __('Space between form elements (default: 24px)', 'cf7rl') . '</td></tr>';
	
	// Feature Settings section - commented out (no active features)
	// $output .= '<tr><td colspan="3"><br /><b>' . __('Feature Settings', 'cf7rl') . '</b></td></tr>';
	
	// Floating labels feature - commented out
	// $output .= '<tr><td class="cf7rl_tabs_table_title_width"><label>' . __('Enable Floating Labels:', 'cf7rl') . '</label></td>';
	// $output .= '<td class="cf7rl_tabs_table_body_width"><input name="cf7rl_material_floating_labels" value="1" type="checkbox" ' . $checked_floating . '></td>';
	// $output .= '<td>' . __('Animate labels to float above inputs when focused', 'cf7rl') . '</td></tr>';
	
	$output .= '<input type="hidden" name="cf7rl_material_post" value="' . esc_attr($post_id) . '">';
	
	$output .= '</tr></table>';
	
	echo $output;
}

/**
 * Save Material Theme settings when form is saved
 */
function cf7rl_save_material_theme_settings($cf7) {
	// Only save if the material theme module is enabled
	if (!cf7rl_is_module_enabled('material_theme')) {
		return;
	}
	
	if (empty($_POST['cf7rl_material_post'])) {
		return;
	}
	
	$post_id = absint($_POST['cf7rl_material_post']);
	
	// Enable/disable Material Theme
	if (!empty($_POST['cf7rl_enable_material_theme'])) {
		update_post_meta($post_id, '_cf7rl_enable_material_theme', '1');
	} else {
		update_post_meta($post_id, '_cf7rl_enable_material_theme', '0');
	}
	
	// Save colors - always save even if empty to ensure they're stored
	if (isset($_POST['cf7rl_material_primary_color'])) {
		$primary_color = sanitize_hex_color($_POST['cf7rl_material_primary_color']);
		if ($primary_color) {
			update_post_meta($post_id, '_cf7rl_material_primary_color', $primary_color);
		}
	}
	
	if (isset($_POST['cf7rl_material_background_color'])) {
		$background_color = sanitize_hex_color($_POST['cf7rl_material_background_color']);
		if ($background_color) {
			update_post_meta($post_id, '_cf7rl_material_background_color', $background_color);
		}
	}
	
	// Save vertical spacing
	if (isset($_POST['cf7rl_material_vertical_spacing'])) {
		$vertical_spacing = absint($_POST['cf7rl_material_vertical_spacing']);
		update_post_meta($post_id, '_cf7rl_material_vertical_spacing', $vertical_spacing);
	}
	
	// Save feature toggles
	// Floating labels feature - commented out, always set to disabled
	// if (!empty($_POST['cf7rl_material_floating_labels'])) {
	// 	update_post_meta($post_id, '_cf7rl_material_floating_labels', '1');
	// } else {
		update_post_meta($post_id, '_cf7rl_material_floating_labels', '0');
	// }
}
add_action('wpcf7_after_save', 'cf7rl_save_material_theme_settings');
