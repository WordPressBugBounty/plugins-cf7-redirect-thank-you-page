<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Add reCAPTCHA tab to Contact Form 7 editor
 */
function cf7rl_recaptcha_editor_panel($panels) {
	$new_page = array(
		'ReCaptcha' => array(
			'title' => __('reCAPTCHA', 'cf7rl'),
			'callback' => 'cf7rl_recaptcha_admin_settings'
		)
	);
	$panels = array_merge($panels, $new_page);
	return $panels;
}
add_filter('wpcf7_editor_panels', 'cf7rl_recaptcha_editor_panel', 20);

/**
 * Display reCAPTCHA settings on form edit page
 */
function cf7rl_recaptcha_admin_settings($cf7) {
	$post_id = absint($_GET['post']);
	
	$enable_recaptcha = get_post_meta($post_id, "_cf7rl_enable_recaptcha", true);
	
	if ($enable_recaptcha == "1") {
		$checked = "CHECKED";
	} else {
		$checked = "";
	}
	
	$admin_table_output = "";
	$admin_table_output .= "<h2>" . __("Google reCAPTCHA v2 Settings", 'cf7rl') . "</h2>";
	$admin_table_output .= "<div class='mail-field'></div>";
	
	// Check if reCAPTCHA is configured in global settings
	$options = cf7rl_get_recaptcha_options();
	$is_configured = !empty($options['recaptcha_site_key']) && !empty($options['recaptcha_secret_key']);
	
	if (!$is_configured) {
		$admin_table_output .= "<div class='notice notice-warning inline' style='margin: 10px 0;'>";
		$admin_table_output .= "<p><strong>" . __('reCAPTCHA is not configured yet. Please configure your reCAPTCHA credentials in the', 'cf7rl') . " ";
		$admin_table_output .= "<a href='" . admin_url('admin.php?page=cf7rl_admin_table&tab=3') . "'>" . __('Settings page', 'cf7rl') . "</a>.</strong></p>";
		$admin_table_output .= "</div>";
	}
	
	$admin_table_output .= "<table><tr>";
	$admin_table_output .= "<td width='250px'><label>" . __("Enable reCAPTCHA on this form:", 'cf7rl') . " </label></td>";
	$admin_table_output .= "<td width='250px'><input id='cf7rl_enable_recaptcha' name='cf7rl_enable_recaptcha' value='1' type='checkbox' $checked";
	
	if (!$is_configured) {
		$admin_table_output .= " disabled";
	}
	
	$admin_table_output .= "></td></tr>";
	
	if (!$is_configured) {
		$admin_table_output .= "<tr><td colspan='2'><p style='color: #666;'>" . __('You must configure reCAPTCHA credentials in the plugin settings before enabling it on forms.', 'cf7rl') . "</p></td></tr>";
	} else {
		$admin_table_output .= "<tr><td colspan='2'><p style='color: #666;'>" . __('When enabled, Google reCAPTCHA v2 will be displayed on this form to prevent spam submissions.', 'cf7rl') . "</p></td></tr>";
		$admin_table_output .= "<tr><td colspan='2'><p style='color: #666;'>" . __('The reCAPTCHA position and theme can be configured in the', 'cf7rl') . " ";
		$admin_table_output .= "<a href='" . admin_url('admin.php?page=cf7rl_admin_table&tab=3') . "'>" . __('Settings page', 'cf7rl') . "</a>.</p></td></tr>";
	}
	
	$admin_table_output .= "<input type='hidden' name='cf7rl_recaptcha_post' value='" . esc_attr($post_id) . "'>";
	$admin_table_output .= "</td></tr></table>";
	
	echo $admin_table_output;
}

/**
 * Save reCAPTCHA settings when form is saved
 */
function cf7rl_save_recaptcha_settings($cf7) {
	// Only save if the reCAPTCHA module is enabled
	if (!cf7rl_is_module_enabled('recaptcha')) {
		return;
	}
	
	if (empty($_POST['cf7rl_recaptcha_post'])) {
		return;
	}
	
	$post_id = absint($_POST['cf7rl_recaptcha_post']);
	
	if (!empty($_POST['cf7rl_enable_recaptcha'])) {
		$enable_recaptcha = sanitize_text_field($_POST['cf7rl_enable_recaptcha']);
		update_post_meta($post_id, "_cf7rl_enable_recaptcha", $enable_recaptcha);
	} else {
		update_post_meta($post_id, "_cf7rl_enable_recaptcha", 0);
	}
}
add_action('wpcf7_after_save', 'cf7rl_save_recaptcha_settings');
