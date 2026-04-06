<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// hook into contact form 7 form
function cf7rl_payment_editor_panels ( $panels ) {

	$new_page = array(
		'PayPal' => array(
			'title' => __( 'PayPal & Stripe', 'contact-form-7-paypal-add-on' ),
			'callback' => 'cf7rl_payment_admin_after_additional_settings'
		)
	);

	$panels = array_merge($panels, $new_page);

	return $panels;

}
add_filter( 'wpcf7_editor_panels', 'cf7rl_payment_editor_panels' );


function cf7rl_payment_admin_after_additional_settings( $cf7 ) {

	$post_id = absint($_GET['post']);

	$enable = 					get_post_meta($post_id, "_cf7rl_enable", true);
	$enable_stripe = 			get_post_meta($post_id, "_cf7rl_enable_stripe", true);
	$name = 					get_post_meta($post_id, "_cf7rl_name", true);
	$price = 					get_post_meta($post_id, "_cf7rl_price", true);
	$id = 						get_post_meta($post_id, "_cf7rl_id", true);
	$gateway = 					get_post_meta($post_id, "_cf7rl_gateway", true);
	$stripe_email = 			get_post_meta($post_id, "_cf7rl_stripe_email", true);
	
	// Check if redirect module is enabled
	$redirect_enable = 			get_post_meta($post_id, "_cf7rl_redirect_enable", true);
	$redirect_active = ($redirect_enable == "1");

	if ($enable == "1" && !$redirect_active) { $checked = "CHECKED"; } else { $checked = ""; }
	if ($enable_stripe == "1" && !$redirect_active) { $checked_stripe = "CHECKED"; } else { $checked_stripe = ""; }
	if ($redirect_active) { $disabled = "DISABLED"; } else { $disabled = ""; }

	$admin_table_output = "";
	$admin_table_output .= "<h2>" . __("PayPal & Stripe Settings", 'contact-form-7-paypal-add-on') . "</h2>";

	$admin_table_output .= "<div class='mail-field'></div>";
	
	// Container for dynamic notice (will be populated by JavaScript)
	$admin_table_output .= "<div id='cf7rl-payment-notice-container'>";
	
	// Show server-side notice if redirect is active
	if ($redirect_active) {
		$admin_table_output .= "<div class='notice notice-warning inline cf7rl-server-notice' style='margin: 10px 0; padding: 10px;'>";
		$admin_table_output .= "<p><strong>Note:</strong> The Redirect & Thank You Page module is enabled on this form. ";
		$admin_table_output .= "Payment processing has been disabled because it cannot be used with the redirect module.</p>";
		$admin_table_output .= "</div>";
	}
	
	$admin_table_output .= "</div>";
	
	$admin_table_output .= "<table><tr>";
	
	$admin_table_output .= "<td width='195px'><label>" . __("Enable PayPal on this form:", 'contact-form-7-paypal-add-on') . " </label></td>";
	$admin_table_output .= "<td width='250px'><input name='cf7rl_enable' value='1' type='checkbox' $checked $disabled></td></tr>";

	$admin_table_output .= "<td><label>" . __("Enable Stripe on this form", 'contact-form-7-paypal-add-on') . "</label></td>";
	$admin_table_output .= "<td><input name='cf7rl_enable_stripe' value='1' type='checkbox' $checked_stripe $disabled></td></tr>";

	$admin_table_output .= "<tr><td>" . __("Gateway Code:", 'contact-form-7-paypal-add-on') . " </td>";
	$admin_table_output .= "<td><input type='text' name='cf7rl_gateway' value='" . esc_attr($gateway) . "'> </td><td> (" . __("Required to use both Gateways at the same time. Documentation", 'contact-form-7-paypal-add-on') . " <a target='_blank' href='https://wpplugin.org/documentation/paypal-stripe-gateway-code/'>" . __("here", 'contact-form-7-paypal-add-on') . "</a>. " . __("Example: menu-231", 'contact-form-7-paypal-add-on') . ")</td></tr><tr><td>";

	$admin_table_output .= "<tr><td>" . __("Email Code:", 'contact-form-7-paypal-add-on') . " </td>";
	$admin_table_output .= "<td><input type='text' name='cf7rl_stripe_email' value='" . esc_attr($stripe_email) . "'> </td><td> (" . __("Optional. Pass email to Stripe. Example: text-105", 'contact-form-7-paypal-add-on') . ")</td></tr><tr><td colspan='3'><br />";


	$admin_table_output .= "<hr></td></tr>";

	$admin_table_output .= "<tr><td>" . __("Item Description:", 'contact-form-7-paypal-add-on') . " </td>";
	$admin_table_output .= "<td><input type='text' name='cf7rl_name' value='" . esc_attr($name) . "'> </td><td> (" . __("Optional", 'contact-form-7-paypal-add-on') . ")</td></tr>";

	$admin_table_output .= "<tr><td>" . __("Item Price:", 'contact-form-7-paypal-add-on') . " </td>";
	$admin_table_output .= "<td><input type='text' name='cf7rl_price' value='" . esc_attr($price) . "'> </td><td> (" . __("Format: for $2.99, enter 2.99", 'contact-form-7-paypal-add-on') . ")</td></tr>";

	$admin_table_output .= "<tr><td>" . __("Item ID / SKU:", 'contact-form-7-paypal-add-on') . " </td>";
	$admin_table_output .= "<td><input type='text' name='cf7rl_id' value='" . esc_attr($id) . "'> </td><td> (" . __("Optional", 'contact-form-7-paypal-add-on') . ")</td></tr>";
	
	$admin_table_output .= "<input type='hidden' name='cf7rl_payment_post' value='" . esc_attr($post_id) . "'>";

	$admin_table_output .= "</td></tr></table>";

	echo $admin_table_output;

}






// hook into contact form 7 admin form save
add_action('wpcf7_after_save', 'cf7rl_payment_save_contact_form');

function cf7rl_payment_save_contact_form( $cf7 ) {
		
		if (!isset($_POST['cf7rl_payment_post'])) {
			return;
		}
		
		$post_id = absint($_POST['cf7rl_payment_post']);
		
		// Check if redirect module is being enabled in this save
		// If cf7rl_post exists in POST, the redirect tab data was submitted
		if (isset($_POST['cf7rl_post'])) {
			// Redirect form was submitted - check if checkbox is checked
			$redirect_active = !empty($_POST['cf7rl_redirect_enable']);
		} else {
			// Redirect form not submitted, check database
			$redirect_enable = get_post_meta($post_id, "_cf7rl_redirect_enable", true);
			$redirect_active = ($redirect_enable == "1");
		}
		
		// Only allow payment to be enabled if redirect is not active
		if (!empty($_POST['cf7rl_enable']) && !$redirect_active) {
			$enable = sanitize_text_field($_POST['cf7rl_enable']);
			update_post_meta($post_id, "_cf7rl_enable", $enable);
		} else {
			update_post_meta($post_id, "_cf7rl_enable", 0);
		}
		
		if (!empty($_POST['cf7rl_enable_stripe']) && !$redirect_active) {
			$enable_stripe = sanitize_text_field($_POST['cf7rl_enable_stripe']);
			update_post_meta($post_id, "_cf7rl_enable_stripe", $enable_stripe);
		} else {
			update_post_meta($post_id, "_cf7rl_enable_stripe", 0);
		}
		
		$name = sanitize_text_field($_POST['cf7rl_name']);
		update_post_meta($post_id, "_cf7rl_name", $name);
		
		$price = sanitize_text_field($_POST['cf7rl_price']);
		$price = cf7rl_format_currency($price);
		update_post_meta($post_id, "_cf7rl_price", $price);
		
		$id = sanitize_text_field($_POST['cf7rl_id']);
		update_post_meta($post_id, "_cf7rl_id", $id);
		
		$gateway = sanitize_text_field($_POST['cf7rl_gateway']);
		update_post_meta($post_id, "_cf7rl_gateway", $gateway);
		
		$stripe_email = sanitize_text_field($_POST['cf7rl_stripe_email']);
		update_post_meta($post_id, "_cf7rl_stripe_email", $stripe_email);

}
