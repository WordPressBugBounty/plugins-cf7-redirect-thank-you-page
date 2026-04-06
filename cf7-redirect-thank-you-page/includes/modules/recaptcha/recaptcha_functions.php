<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get reCAPTCHA options
 */
function cf7rl_get_recaptcha_options() {
	$options = get_option('cf7rl_options');
	if (!is_array($options)) {
		$options = array();
	}
	
	// Set defaults
	if (empty($options['recaptcha_site_key'])) { 		$options['recaptcha_site_key'] = ''; }
	if (empty($options['recaptcha_secret_key'])) { 	$options['recaptcha_secret_key'] = ''; }
	if (empty($options['recaptcha_position'])) { 		$options['recaptcha_position'] = 'below'; }
	if (empty($options['recaptcha_theme'])) { 			$options['recaptcha_theme'] = 'light'; }
	if (empty($options['recaptcha_error_message'])) { 	$options['recaptcha_error_message'] = __('Please complete the reCAPTCHA verification to submit this form.', 'cf7rl'); }
	
	return $options;
}

/**
 * Verify reCAPTCHA response
 */
function cf7rl_verify_recaptcha($response) {
	$options = cf7rl_get_recaptcha_options();
	$secret_key = $options['recaptcha_secret_key'];
	
	if (empty($secret_key) || empty($response)) {
		return false;
	}
	
	$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
	
	$data = array(
		'secret' => $secret_key,
		'response' => $response,
		'remoteip' => $_SERVER['REMOTE_ADDR']
	);
	
	$response = wp_remote_post($verify_url, array(
		'body' => $data
	));
	
	if (is_wp_error($response)) {
		return false;
	}
	
	$response_body = wp_remote_retrieve_body($response);
	$result = json_decode($response_body, true);
	
	return isset($result['success']) && $result['success'] === true;
}

/**
 * Check if reCAPTCHA is enabled for a specific form
 */
function cf7rl_is_recaptcha_enabled_for_form($form_id) {
	$enabled = get_post_meta($form_id, "_cf7rl_enable_recaptcha", true);
	return $enabled == "1";
}

/**
 * Validate reCAPTCHA on form submission
 * This uses the spam filter hook which is perfect for reCAPTCHA
 */
function cf7rl_recaptcha_spam_filter($spam) {
	// If already marked as spam, return
	if ($spam) {
		return $spam;
	}
	
	// Get the submission
	$submission = WPCF7_Submission::get_instance();
	if (!$submission) {
		return $spam;
	}
	
	$contact_form = $submission->get_contact_form();
	$form_id = $contact_form->id();
	
	// Check if reCAPTCHA is enabled for this form
	if (!cf7rl_is_recaptcha_enabled_for_form($form_id)) {
		return $spam;
	}
	
	// Get reCAPTCHA response
	$recaptcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
	
	// Verify reCAPTCHA
	if (!cf7rl_verify_recaptcha($recaptcha_response)) {
		// Mark as spam if reCAPTCHA fails
		$spam = true;
		
		// Add a custom error message
		$submission->add_spam_log(array(
			'agent' => 'cf7rl-recaptcha',
			'reason' => __('reCAPTCHA verification failed', 'cf7rl')
		));
		
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CF7RL reCAPTCHA: Validation failed for form ' . $form_id);
		}
	} else {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CF7RL reCAPTCHA: Validation passed for form ' . $form_id);
		}
	}
	
	return $spam;
}
add_filter('wpcf7_spam', 'cf7rl_recaptcha_spam_filter', 10, 1);

/**
 * Customize the spam/validation message when reCAPTCHA fails
 */
function cf7rl_recaptcha_custom_message($message, $status, $contact_form = null) {
	// Only modify the message if it's a spam status
	if ($status !== 'spam') {
		return $message;
	}
	
	// Try to get the contact form if not provided
	if (!$contact_form) {
		$contact_form = wpcf7_get_current_contact_form();
	}
	
	if (!$contact_form) {
		return $message;
	}
	
	// Check if this form has reCAPTCHA enabled
	$form_id = $contact_form->id();
	if (!cf7rl_is_recaptcha_enabled_for_form($form_id)) {
		return $message;
	}
	
	// Check if the spam was triggered by our reCAPTCHA validation
	$submission = WPCF7_Submission::get_instance();
	if ($submission) {
		$spam_log = $submission->get_spam_log();
		foreach ($spam_log as $log) {
			if (isset($log['agent']) && $log['agent'] === 'cf7rl-recaptcha') {
				// Get the custom error message from settings
				$options = cf7rl_get_recaptcha_options();
				$custom_message = isset($options['recaptcha_error_message']) ? $options['recaptcha_error_message'] : '';
				
				// Return custom message or default
				return !empty($custom_message) ? $custom_message : __('Please complete the reCAPTCHA verification to submit this form.', 'cf7rl');
			}
		}
	}
	
	return $message;
}
add_filter('wpcf7_display_message', 'cf7rl_recaptcha_custom_message', 10, 2);

/**
 * Add reCAPTCHA to form output
 * This filter receives the form HTML
 */
function cf7rl_add_recaptcha_to_form($form) {
	// Get the current form ID
	$contact_form = wpcf7_get_current_contact_form();
	if (!$contact_form) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CF7RL reCAPTCHA: Could not get current contact form');
		}
		return $form;
	}
	
	$form_id = $contact_form->id();
	
	// Check if reCAPTCHA is enabled for this form
	if (!cf7rl_is_recaptcha_enabled_for_form($form_id)) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CF7RL reCAPTCHA: reCAPTCHA not enabled for form ' . $form_id);
		}
		return $form;
	}
	
	$options = cf7rl_get_recaptcha_options();
	$site_key = isset($options['recaptcha_site_key']) ? $options['recaptcha_site_key'] : '';
	$position = isset($options['recaptcha_position']) ? $options['recaptcha_position'] : 'below';
	$theme = isset($options['recaptcha_theme']) ? $options['recaptcha_theme'] : 'light';
	
	if (empty($site_key)) {
		// Debug: Site key is empty
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CF7RL reCAPTCHA: Site key is empty for form ' . $form_id);
		}
		return $form;
	}
	
	// Create reCAPTCHA HTML
	// Note: We use explicit rendering in JavaScript to handle expiration callbacks
	$recaptcha_html = '<div class="cf7rl-recaptcha-wrapper" style="margin: 15px 0;">';
	$recaptcha_html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '" data-theme="' . esc_attr($theme) . '" data-size="normal"></div>';
	$recaptcha_html .= '</div>';
	
	// Try multiple patterns to find the submit button
	$patterns = array(
		// Standard input submit button
		'/(<input[^>]*type=["\']submit["\'][^>]*>)/i',
		// Button element
		'/(<button[^>]*type=["\']submit["\'][^>]*>.*?<\/button>)/is',
		// CF7 submit wrapper
		'/(<p[^>]*class=["\'][^"\']*submit[^"\']*["\'][^>]*>.*?<\/p>)/is',
	);
	
	$replaced = false;
	foreach ($patterns as $pattern) {
		if (preg_match($pattern, $form)) {
			if ($position === 'above') {
				// Add reCAPTCHA above the submit button
				$form = preg_replace($pattern, $recaptcha_html . '$1', $form, 1);
			} else {
				// Add reCAPTCHA below the submit button (default)
				$form = preg_replace($pattern, '$1' . $recaptcha_html, $form, 1);
			}
			$replaced = true;
			
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('CF7RL reCAPTCHA: Successfully added to form ' . $form_id . ' using pattern match');
			}
			break;
		}
	}
	
	// If no pattern matched, append to the end of the form
	if (!$replaced) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('CF7RL reCAPTCHA: No submit button found for form ' . $form_id . ', appending to end');
		}
		$form .= $recaptcha_html;
	}
	
	return $form;
}
add_filter('wpcf7_form_elements', 'cf7rl_add_recaptcha_to_form', 10, 1);

/**
 * Debug function - Add this temporarily to see if the filter is being called
 * To enable: Add define('CF7RL_RECAPTCHA_DEBUG', true); to wp-config.php
 */
if (defined('CF7RL_RECAPTCHA_DEBUG') && CF7RL_RECAPTCHA_DEBUG) {
	add_action('wp_footer', function() {
		if (!is_admin()) {
			$contact_form = wpcf7_get_current_contact_form();
			if ($contact_form) {
				$form_id = $contact_form->id();
				$enabled = cf7rl_is_recaptcha_enabled_for_form($form_id);
				$options = cf7rl_get_recaptcha_options();
				
				echo '<!-- CF7RL reCAPTCHA Debug -->';
				echo '<script>console.log("CF7RL reCAPTCHA Debug:");</script>';
				echo '<script>console.log("Form ID: ' . $form_id . '");</script>';
				echo '<script>console.log("reCAPTCHA Enabled: ' . ($enabled ? 'Yes' : 'No') . '");</script>';
				echo '<script>console.log("Site Key: ' . (!empty($options['recaptcha_site_key']) ? 'Set' : 'Not Set') . '");</script>';
				echo '<script>console.log("Module Enabled: ' . (cf7rl_is_module_enabled('recaptcha') ? 'Yes' : 'No') . '");</script>';
				echo '<!-- End CF7RL reCAPTCHA Debug -->';
			}
		}
	}, 999);
}
