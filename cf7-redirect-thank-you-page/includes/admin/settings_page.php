<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// admin table
function cf7rl_admin_table() {



	if ( !current_user_can( "manage_options" ) )  {
	wp_die( __( "You do not have sufficient permissions to access this page." ) );
	}



	// save and update options
	if (isset($_POST['update'])) {
	
	if ( empty( $_POST['cf7rl_nonce_field'] ) || !wp_verify_nonce( $_POST['cf7rl_nonce_field'], 'cf7rl_save_settings') ) {
		
		wp_die( __( "You do not have sufficient permissions to access this page." ) );
		
	}
		
		// Get current options to preserve existing values
		$options = get_option('cf7rl_options');
		if (!is_array($options)) {
			$options = array();
		}
		
		// Check if modules changed
		$old_modules = isset($options['modules']) ? $options['modules'] : array();
		$new_modules = isset($_POST['cf7rl_modules']) ? $_POST['cf7rl_modules'] : array();
		$modules_changed = ($old_modules != $new_modules);
		
		if (isset($_POST['redirect'])) {
			$options['redirect'] = sanitize_text_field($_POST['redirect']);
			if (empty($options['redirect'])) { $options['redirect'] = '1'; }
		}
		
		$options['modules'] = $new_modules;
		
		// Validation warnings array
		$validation_warnings = array();
		
		// reCAPTCHA module settings
		if (isset($_POST['recaptcha_site_key'])) {
			$options['recaptcha_site_key'] = sanitize_text_field($_POST['recaptcha_site_key']);
		}
		if (isset($_POST['recaptcha_secret_key'])) {
			$options['recaptcha_secret_key'] = sanitize_text_field($_POST['recaptcha_secret_key']);
		}
		if (isset($_POST['recaptcha_position'])) {
			$options['recaptcha_position'] = sanitize_text_field($_POST['recaptcha_position']);
		}
		if (isset($_POST['recaptcha_theme'])) {
			$options['recaptcha_theme'] = sanitize_text_field($_POST['recaptcha_theme']);
		}
		if (isset($_POST['recaptcha_error_message'])) {
			$options['recaptcha_error_message'] = sanitize_text_field($_POST['recaptcha_error_message']);
		}
		
		// Country & Phone Fields module settings
		// Only process these if country_default is in POST (indicates we're on the Settings tab with country/phone fields)
		if (isset($_POST['country_default'])) {
			$options['country_default'] = sanitize_text_field($_POST['country_default']);
			
			// Process all country/phone settings since we're on that settings page
			if (isset($_POST['country_include'])) {
				$options['country_include'] = array_map('sanitize_text_field', $_POST['country_include']);
			} else {
				$options['country_include'] = array();
			}
			if (isset($_POST['country_exclude'])) {
				$options['country_exclude'] = array_map('sanitize_text_field', $_POST['country_exclude']);
			} else {
				$options['country_exclude'] = array();
			}
			if (isset($_POST['country_preferred'])) {
				$options['country_preferred'] = array_map('sanitize_text_field', $_POST['country_preferred']);
			} else {
				$options['country_preferred'] = array();
			}
			
			if (isset($_POST['phone_default'])) {
				$options['phone_default'] = sanitize_text_field($_POST['phone_default']);
			}
			if (isset($_POST['phone_include'])) {
				$options['phone_include'] = array_map('sanitize_text_field', $_POST['phone_include']);
			} else {
				$options['phone_include'] = array();
			}
			if (isset($_POST['phone_exclude'])) {
				$options['phone_exclude'] = array_map('sanitize_text_field', $_POST['phone_exclude']);
			} else {
				$options['phone_exclude'] = array();
			}
			if (isset($_POST['phone_preferred'])) {
				$options['phone_preferred'] = array_map('sanitize_text_field', $_POST['phone_preferred']);
			} else {
				$options['phone_preferred'] = array();
			}
			if (isset($_POST['country_show_flags'])) {
				$options['country_show_flags'] = sanitize_text_field($_POST['country_show_flags']);
			} else {
				$options['country_show_flags'] = '0';
			}
			if (isset($_POST['phone_show_flags'])) {
				$options['phone_show_flags'] = sanitize_text_field($_POST['phone_show_flags']);
			} else {
				$options['phone_show_flags'] = '0';
			}
			if (isset($_POST['country_label'])) {
				$options['country_label'] = sanitize_text_field($_POST['country_label']);
			}
			if (isset($_POST['phone_label'])) {
				$options['phone_label'] = sanitize_text_field($_POST['phone_label']);
			}
			if (isset($_POST['country_dropdown_width'])) {
				$options['country_dropdown_width'] = sanitize_text_field($_POST['country_dropdown_width']);
			}
			if (isset($_POST['phone_dropdown_width'])) {
				$options['phone_dropdown_width'] = sanitize_text_field($_POST['phone_dropdown_width']);
			}
		}
		
		// Bookings module settings
		if (isset($_POST['booking_time_format'])) {
			$options['booking_time_format'] = sanitize_text_field($_POST['booking_time_format']);
		}
		if (isset($_POST['booking_date_width'])) {
			$options['booking_date_width'] = sanitize_text_field($_POST['booking_date_width']);
		}
		if (isset($_POST['booking_time_width'])) {
			$options['booking_time_width'] = sanitize_text_field($_POST['booking_time_width']);
		}
		
		// Payment module settings (PayPal & Stripe tabs)
		if (isset($_POST['mode'])) {
			$options['mode'] = sanitize_text_field($_POST['mode']);
			if (empty($options['mode'])) { $options['mode'] = '2'; }
		}
		if (isset($_POST['mode_stripe'])) {
			$options['mode_stripe'] = sanitize_text_field($_POST['mode_stripe']);
			if (empty($options['mode_stripe'])) { $options['mode_stripe'] = '2'; }
		}
		if (isset($_POST['success'])) {
			$options['success'] = sanitize_text_field($_POST['success']);
			if (empty($options['success'])) { $options['success'] = __('Payment Successful', 'cf7rl'); }
		}
		if (isset($_POST['failed'])) {
			$options['failed'] = sanitize_text_field($_POST['failed']);
			if (empty($options['failed'])) { $options['failed'] = __('Payment Failed', 'cf7rl'); }
		}
		// Payment module settings from Settings tab accordion
		if (isset($_POST['redirect_payment'])) {
			$options['redirect_payment'] = sanitize_text_field($_POST['redirect_payment']);
			if (empty($options['redirect_payment'])) { $options['redirect_payment'] = '1'; }
		}
		if (isset($_POST['request_method'])) {
			$options['request_method'] = sanitize_text_field($_POST['request_method']);
			if (empty($options['request_method'])) { $options['request_method'] = '1'; }
		}
		if (isset($_POST['session'])) {
			$options['session'] = sanitize_text_field($_POST['session']);
			if (empty($options['session'])) { $options['session'] = '1'; }
		}
		if (isset($_POST['currency'])) {
			$options['currency'] = sanitize_text_field($_POST['currency']);
			if (empty($options['currency'])) { $options['currency'] = '25'; }
		}
		if (isset($_POST['language'])) {
			$options['language'] = sanitize_text_field($_POST['language']);
			if (empty($options['language'])) { $options['language'] = '3'; }
		}
		
		update_option("cf7rl_options", $options);
		
		// If modules changed, use JavaScript to reload the page with proper module files loaded
		if ($modules_changed) {
			$redirect_args = array(
				'page' => 'cf7rl_admin_table',
				'tab' => isset($_POST['hidden_tab_value']) ? sanitize_text_field($_POST['hidden_tab_value']) : '1',
				'settings-updated' => 'true'
			);
			// Preserve open accordions state
			if (isset($_POST['cf7rl_open_accordions']) && !empty($_POST['cf7rl_open_accordions'])) {
				$redirect_args['accordions'] = sanitize_text_field($_POST['cf7rl_open_accordions']);
			}
			$redirect_url = add_query_arg($redirect_args, admin_url('admin.php'));
			
			// Store validation warnings in transient for display after redirect
			if (!empty($validation_warnings)) {
				set_transient('cf7rl_validation_warnings', $validation_warnings, 30);
			}
			
			echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";
			
			// Display validation warnings
			if (!empty($validation_warnings)) {
				echo "<div class='notice notice-warning'><p><strong>" . __('Warning:', 'cf7rl') . "</strong></p><ul style='list-style: disc; margin-left: 20px;'>";
				foreach ($validation_warnings as $warning) {
					echo "<li>" . esc_html($warning) . "</li>";
				}
				echo "</ul></div>";
			}
			
			echo "<script type='text/javascript'>window.location.href = '" . esc_url($redirect_url) . "';</script>";
			echo "<noscript><meta http-equiv='refresh' content='0;url=" . esc_url($redirect_url) . "' /></noscript>";
			return; // Stop further execution
		}
		
		echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";
		
		// Output script to restore open accordions after non-redirect save
		if (isset($_POST['cf7rl_open_accordions']) && !empty($_POST['cf7rl_open_accordions'])) {
			$open_accordions = sanitize_text_field($_POST['cf7rl_open_accordions']);
			echo "<script type='text/javascript'>var cf7rl_open_accordions = '" . esc_js($open_accordions) . "';</script>";
		}
		
		// Display validation warnings
		if (!empty($validation_warnings)) {
			echo "<div class='notice notice-warning'><p><strong>" . __('Warning:', 'cf7rl') . "</strong></p><ul style='list-style: disc; margin-left: 20px;'>";
			foreach ($validation_warnings as $warning) {
				echo "<li>" . esc_html($warning) . "</li>";
			}
			echo "</ul></div>";
		}
	}
	
	// Show settings updated message after redirect
	if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
		echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";
		
		// Check for validation warnings stored in transient
		$stored_warnings = get_transient('cf7rl_validation_warnings');
		if (!empty($stored_warnings)) {
			echo "<div class='notice notice-warning'><p><strong>" . __('Warning:', 'cf7rl') . "</strong></p><ul style='list-style: disc; margin-left: 20px;'>";
			foreach ($stored_warnings as $warning) {
				echo "<li>" . esc_html($warning) . "</li>";
			}
			echo "</ul></div>";
			// Delete the transient after displaying
			delete_transient('cf7rl_validation_warnings');
		}
	}



	// get options
	$options = get_option('cf7rl_options');
	if (!is_array($options)) {
		$options = array();
	}
	
	// Set defaults for options
	if (empty($options['redirect'])) { 					$options['redirect'] = '1'; }
	if (empty($options['currency'])) { 					$options['currency'] = '25'; }
	if (empty($options['language'])) { 					$options['language'] = '3'; }
	if (empty($options['request_method'])) { 			$options['request_method'] = '1'; }
	if (empty($options['session'])) { 					$options['session'] = '1'; }
	if (!isset($options['redirect_payment'])) { 		$options['redirect_payment'] = '1'; }
	
	// Set defaults for country/phone field options
	if (empty($options['country_dropdown_width'])) { 	$options['country_dropdown_width'] = '200'; }
	if (empty($options['phone_dropdown_width'])) { 	$options['phone_dropdown_width'] = '100'; }
	
	
	// tabs
	if (isset($_POST['hidden_tab_value'])) {
		$active_tab =	sanitize_text_field($_POST['hidden_tab_value']);
		$active_tab =	esc_attr($active_tab); // Apply escaping for safe output
	} else {
		if (isset($_GET['tab'])) {
			$active_tab = sanitize_text_field($_GET[ 'tab' ]);
			$active_tab = esc_attr($active_tab); // Apply escaping for safe output
		} else {
			$active_tab = '1';
		}
	}

	


	// make page
	$settings_table_output = "";
	$settings_table_output .= "<form method='post'>";

	$settings_table_output .= "<table width='70%'><tr><td>";
	$settings_table_output .= "<div class='wrap'><h2>Business Essentials for Contact Form 7 Settings</h2></div><br /></td><td><br />";
	$settings_table_output .= "<input type='submit' name='btn2' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;float: right;' value='Save Settings'>";
	$settings_table_output .= "</td></tr></table>";



	$settings_table_output .= "<table width='100%'><tr><td width='70%' valign='top'>";

		// Check if any modules are enabled
		$has_enabled_modules = cf7rl_is_module_enabled('redirect') || cf7rl_is_module_enabled('payments') || cf7rl_is_module_enabled('recaptcha');
		
		// Build tab list dynamically
		$tab_list = '1,2';
		if ($has_enabled_modules) {
			$tab_list .= ',3';
		}
		// Add PayPal and Stripe tabs if payments module is enabled
		$payments_enabled = cf7rl_is_module_enabled('payments');
		if ($payments_enabled) {
			$tab_list .= ',4,5';
		}

		$settings_table_output .= "<h2 class='nav-tab-wrapper'>";
			$settings_table_output .= "<a onclick=\"cf7rl_closetabs('{$tab_list}');cf7rl_newtab('1');return false;\" href='#' id='id1' class=\"nav-tab "; if ($active_tab == '1') { $settings_table_output .= 'nav-tab-active'; } else { ''; } $settings_table_output .= " \">Getting Started</a>";
			$settings_table_output .= "<a onclick=\"cf7rl_closetabs('{$tab_list}');cf7rl_newtab('2');return false;\" href='#' id='id2' class=\"nav-tab "; if ($active_tab == '2') { $settings_table_output .= 'nav-tab-active'; } else { ''; } $settings_table_output .= " \">Modules</a>";
			if ($has_enabled_modules) {
				$settings_table_output .= "<a onclick=\"cf7rl_closetabs('{$tab_list}');cf7rl_newtab('3');return false;\" href='#' id='id3' class=\"nav-tab "; if ($active_tab == '3') { $settings_table_output .= 'nav-tab-active'; } else { ''; } $settings_table_output .= " \">Settings</a>";
			}
			// PayPal and Stripe tabs
			if ($payments_enabled) {
				$settings_table_output .= "<a onclick=\"cf7rl_closetabs('{$tab_list}');cf7rl_newtab('4');return false;\" href='#' id='id4' class=\"nav-tab "; if ($active_tab == '4') { $settings_table_output .= 'nav-tab-active'; } else { ''; } $settings_table_output .= " \">PayPal</a>";
				$settings_table_output .= "<a onclick=\"cf7rl_closetabs('{$tab_list}');cf7rl_newtab('5');return false;\" href='#' id='id5' class=\"nav-tab "; if ($active_tab == '5') { $settings_table_output .= 'nav-tab-active'; } else { ''; } $settings_table_output .= " \">Stripe</a>";
			}
		$settings_table_output .= "</h2>";
		$settings_table_output .= "<br />";
		
	
	$settings_table_output .= "</td><td colspan='3'></td></tr><tr><td valign='top'>";



	$settings_table_output .= "<div id='1' style='display:none;border: 1px solid #CCCCCC; "; if ($active_tab == '1') { $settings_table_output .= 'display:block;'; } $settings_table_output .= "'>";
		$settings_table_output .= "<div style='background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;'>";
			$settings_table_output .= "&nbsp; Getting Started";
		$settings_table_output .= "</div>";
		$settings_table_output .= "<div style='background-color:#fff;padding:8px;'>
			
			<br>
			
			<b>Welcome to Business Essentials for Contact Form 7!</b>
			
			<br /><br />
			
			This plugin extends Contact Form 7 with powerful business features. Enable the modules you need on the <a href='admin.php?page=cf7rl_admin_table&tab=2'>Modules</a> tab, then configure each one on your <a href='admin.php?page=wpcf7'>contact forms</a>.
			
			<br /><br />
			
			<b>Available Modules:</b>
			
			<br /><br />
			
			<ul style='margin-left: 20px; list-style-type: disc;'>
				<li><b>Redirect & Thank You Page</b> - Redirect users to a URL or show a custom thank you message after form submission.</li>
				<li><b>PayPal & Stripe Payments</b> - Accept payments through your forms using PayPal or Stripe.</li>
				<li><b>Google reCAPTCHA v2</b> - Protect your forms from spam with Google reCAPTCHA verification.</li>
				<li><b>Country & Phone Fields</b> - Add country dropdowns and international phone number fields with dial codes.</li>
				<li><b>Database Submissions</b> - Store all form submissions in your database and export to CSV.</li>
				<li><b>Bookings & Appointments</b> - Add date and time picker fields for appointment scheduling.</li>
				<li><b>Material Design</b> - Apply beautiful Material Design styling to your Contact Form 7 forms.</li>
			</ul>
			
			<br />

			If you have any questions or problems, please post a support question <a target='_blank' href='https://wordpress.org/support/plugin/cf7-redirect-thank-you-page/'>here</a>.
			
			<br /><br />
			
			";
			
		$settings_table_output .= "</div>";
	$settings_table_output .= "</div>";


	$settings_table_output .= "<div id='2' style='display:none;border: 1px solid #CCCCCC; "; if ($active_tab == '2') { $settings_table_output .= 'display:block;'; } $settings_table_output .= "'>";
		$settings_table_output .= "<div style='background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;'>";
			$settings_table_output .= "&nbsp; Modules";
		$settings_table_output .= "</div>";
		$settings_table_output .= "<div style='background-color:#fff;padding:8px;'>";
			
			$settings_table_output .= "<p style='margin-top: 0;'>Enable or disable modules to customize the functionality of this plugin. Disabled modules will not load their features.</p>";
			
			$settings_table_output .= cf7rl_modules_page();
			
		$settings_table_output .= "</div>";
	$settings_table_output .= "</div>";


	// Settings tab - only show if at least one module is enabled
	if ($has_enabled_modules) {
		// Count how many modules with settings are enabled
		$settings_modules_count = 0;
		if (cf7rl_is_module_enabled('redirect')) { $settings_modules_count++; }
		if (cf7rl_is_module_enabled('payments')) { $settings_modules_count++; }
		if (cf7rl_is_module_enabled('recaptcha')) { $settings_modules_count++; }
		if (cf7rl_is_module_enabled('country_phone')) { $settings_modules_count++; }
		if (cf7rl_is_module_enabled('bookings')) { $settings_modules_count++; }
		
		// If only one module is enabled, auto-expand its accordion
		$auto_expand_single = ($settings_modules_count == 1);
		
		$settings_table_output .= "<div id='3' style='display:none;border: 1px solid #CCCCCC; "; if ($active_tab == '3') { $settings_table_output .= 'display:block;'; } $settings_table_output .= "'>";
			$settings_table_output .= "<div style='background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;'>";
			$settings_table_output .= "&nbsp; Settings";
		$settings_table_output .= "</div>";
		$settings_table_output .= "<div style='background-color:#fff;padding:8px;'>";

				// Redirect Module Settings - only show if redirect module is enabled
				if (cf7rl_is_module_enabled('redirect')) {
					$settings_table_output .= "<div class='cf7rl-accordion'>";
					$settings_table_output .= "<div class='cf7rl-accordion-header" . ($auto_expand_single ? " active" : "") . "'>";
					$settings_table_output .= "<span>Redirect Module Settings</span>";
					$settings_table_output .= "<span class='cf7rl-accordion-icon'>▼</span>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "<div class='cf7rl-accordion-content'" . ($auto_expand_single ? " style='display:block;'" : "") . ">";
					$settings_table_output .= "<table style='width: 100%;'>";

					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Redirect Method: </b></td><td colspan='2'>";

					$settings_table_output .= "<input "; if ($options['redirect'] == "1") {  $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='redirect' value='1'>1 (Method 1) ";
					$settings_table_output .= "<input "; if ($options['redirect'] == "2") {  $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='redirect' value='2'>2 (Method 2)";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Method 1 is recommend unless your form has problems redirecting. <br /> Method 2 disables <a target='_blank' href='https://contactform7.com/loading-javascript-and-stylesheet-only-when-it-is-necessary/'>WPCF7_LOAD_JS</a> which can be necessary in some situations.</td></tr>";
					
					$settings_table_output .= "</table>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "</div>";
				}
				
				// Payment settings section - only show if payments module is enabled
				if (cf7rl_is_module_enabled('payments')) {
					$settings_table_output .= "<div class='cf7rl-accordion'>";
					$settings_table_output .= "<div class='cf7rl-accordion-header" . ($auto_expand_single ? " active" : "") . "'>";
					$settings_table_output .= "<span>Payment Module Settings</span>";
					$settings_table_output .= "<span class='cf7rl-accordion-icon'>▼</span>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "<div class='cf7rl-accordion-content'" . ($auto_expand_single ? " style='display:block;'" : "") . ">";
					$settings_table_output .= "<table style='width: 100%;'>";
					
					// Payment Redirect Method
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Payment Redirect Method:</b></td><td colspan='2'>";
					$settings_table_output .= "<input "; if (!isset($options['redirect_payment']) || $options['redirect_payment'] == "1") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='redirect_payment' value='1'>1 (DOM wpcf7mailsent event listener) ";
					$settings_table_output .= "<input "; if (isset($options['redirect_payment']) && $options['redirect_payment'] == "2") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='redirect_payment' value='2'>2 (Form sent class listener)";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Method 1 recommend unless the form has problems redirecting.</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Request Method
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Request Method:</b></td><td colspan='2'>";
					$settings_table_output .= "<input "; if ($options['request_method'] == "1") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='request_method' value='1'>1 (Admin Ajax) ";
					$settings_table_output .= "<input "; if ($options['request_method'] == "2") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='request_method' value='2'>2 (Rest API)";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Method 1 recommend unless the form has problems.</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Session Method
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Temporary Storage Method:</b></td><td colspan='2'>";
					$settings_table_output .= "<input "; if ($options['session'] == "1") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='session' value='1'>Cookies ";
					$settings_table_output .= "<input "; if ($options['session'] == "2") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='session' value='2'>Sessions";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Cookies are recommend unless the form has problems.</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Currency
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Currency:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='currency'>";
					$settings_table_output .= "<option "; if ($options['currency'] == "1") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='1'>Australian Dollar - AUD</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "2") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='2'>Brazilian Real - BRL</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "3") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='3'>Canadian Dollar - CAD</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "4") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='4'>Czech Koruna - CZK</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "5") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='5'>Danish Krone - DKK</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "6") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='6'>Euro - EUR</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "7") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='7'>Hong Kong Dollar - HKD</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "8") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='8'>Hungarian Forint - HUF</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "9") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='9'>Israeli New Sheqel - ILS</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "10") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='10'>Japanese Yen - JPY</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "11") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='11'>Malaysian Ringgit - MYR</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "12") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='12'>Mexican Peso - MXN</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "13") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='13'>Norwegian Krone - NOK</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "14") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='14'>New Zealand Dollar - NZD</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "15") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='15'>Philippine Peso - PHP</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "16") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='16'>Polish Zloty - PLN</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "17") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='17'>Pound Sterling - GBP</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "26") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='26'>Romanian Leu - RON</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "18") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='18'>Russian Ruble - RUB</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "19") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='19'>Singapore Dollar - SGD</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "20") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='20'>Swedish Krona - SEK</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "21") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='21'>Swiss Franc - CHF</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "22") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='22'>Taiwan New Dollar - TWD</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "23") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='23'>Thai Baht - THB</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "24") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='24'>Turkish Lira - TRY</option>";
					$settings_table_output .= "<option "; if ($options['currency'] == "25") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='25'>U.S. Dollar - USD</option>";
					$settings_table_output .= "</select></td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Language
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Language:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='language'>";
					$settings_table_output .= "<option "; if ($options['language'] == "1") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='1'>Danish</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "2") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='2'>Dutch</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "3") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='3'>English</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "20") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='20'>English - UK</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "4") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='4'>French</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "5") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='5'>German</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "6") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='6'>Hebrew</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "7") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='7'>Italian</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "8") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='8'>Japanese</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "9") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='9'>Norwegian</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "10") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='10'>Polish</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "11") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='11'>Portuguese</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "12") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='12'>Russian</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "13") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='13'>Spanish</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "14") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='14'>Swedish</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "15") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='15'>Simplified Chinese</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "16") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='16'>Traditional Chinese - Hong Kong</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "17") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='17'>Traditional Chinese - Taiwan</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "18") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='18'>Turkish</option>";
					$settings_table_output .= "<option "; if ($options['language'] == "19") { $settings_table_output .= "SELECTED"; } $settings_table_output .= " value='19'>Thai</option>";
					$settings_table_output .= "</select></td></tr>";
					
					$settings_table_output .= "</table>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "</div>";
				}
				
				// reCAPTCHA settings section - only show if reCAPTCHA module is enabled
				if (cf7rl_is_module_enabled('recaptcha')) {
					$settings_table_output .= "<div class='cf7rl-accordion'>";
					$settings_table_output .= "<div class='cf7rl-accordion-header" . ($auto_expand_single ? " active" : "") . "'>";
					$settings_table_output .= "<span>Google reCAPTCHA v2 Settings</span>";
					$settings_table_output .= "<span class='cf7rl-accordion-icon'>▼</span>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "<div class='cf7rl-accordion-content'" . ($auto_expand_single ? " style='display:block;'" : "") . ">";
					$settings_table_output .= "<table style='width: 100%;'>";
					
					// Set defaults for reCAPTCHA options
					if (empty($options['recaptcha_site_key'])) { 		$options['recaptcha_site_key'] = ''; }
					if (empty($options['recaptcha_secret_key'])) { 	$options['recaptcha_secret_key'] = ''; }
					if (empty($options['recaptcha_position'])) { 		$options['recaptcha_position'] = 'below'; }
					if (empty($options['recaptcha_theme'])) { 			$options['recaptcha_theme'] = 'light'; }
					if (empty($options['recaptcha_error_message'])) { 	$options['recaptcha_error_message'] = __('Please complete the reCAPTCHA verification to submit this form.', 'cf7rl'); }
					
					$settings_table_output .= "<tr><td colspan='3'><p style='margin: 0 0 15px 0;'>Configure Google reCAPTCHA v2 to protect your forms from spam. Get your API keys from <a href='https://www.google.com/recaptcha/admin' target='_blank'>Google reCAPTCHA Admin Console</a>.</p></td></tr>";
					$settings_table_output .= "<tr><td colspan='3'><p style='margin: 0 0 15px 0; padding: 10px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;'><strong>Important:</strong> When creating your reCAPTCHA keys, you must select the <strong>\"I'm not a robot\" Checkbox (reCAPTCHA v2)</strong> option. Invisible reCAPTCHA and reCAPTCHA v3 are <strong>not supported</strong>. Through our testing, we found this option works best with Contact Form 7.</p></td></tr>";
					$settings_table_output .= "<tr><td colspan='3'><p style='margin: 0 0 15px 0; padding: 10px; background-color: #d1ecf1; border: 1px solid #17a2b8; border-radius: 4px;'><strong>Note:</strong> After you've entered your Site Key and Secret Key, you will need to enable Google reCAPTCHA via the checkbox on your individual contact form.</p></td></tr>";
					
					// Site Key
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Site Key:</b></td><td colspan='2'><input type='text' name='recaptcha_site_key' value='" . esc_attr($options['recaptcha_site_key']) . "' style='width: 400px;'></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Your reCAPTCHA site key (public key)</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Secret Key
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Secret Key:</b></td><td colspan='2'><input type='text' name='recaptcha_secret_key' value='" . esc_attr($options['recaptcha_secret_key']) . "' style='width: 400px;'></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Your reCAPTCHA secret key (private key)</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Position
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>reCAPTCHA Position:</b></td><td colspan='2'>";
					$settings_table_output .= "<input "; if ($options['recaptcha_position'] == "above") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='recaptcha_position' value='above'>Above Submit Button ";
					$settings_table_output .= "<input "; if ($options['recaptcha_position'] == "below") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='recaptcha_position' value='below'>Below Submit Button";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Choose where the reCAPTCHA widget appears relative to the submit button</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Theme
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>reCAPTCHA Theme:</b></td><td colspan='2'>";
					$settings_table_output .= "<input "; if ($options['recaptcha_theme'] == "light") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='recaptcha_theme' value='light'>Light ";
					$settings_table_output .= "<input "; if ($options['recaptcha_theme'] == "dark") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='recaptcha_theme' value='dark'>Dark";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Choose the color theme for the reCAPTCHA widget</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Error Message
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Error Message:</b></td><td colspan='2'><input type='text' name='recaptcha_error_message' value='" . esc_attr($options['recaptcha_error_message']) . "' style='width: 400px;'></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Custom error message shown when reCAPTCHA verification fails</td></tr>";
					
					$settings_table_output .= "</table>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "</div>";
				}
				
				// Country & Phone Fields settings section - only show if module is enabled
				if (cf7rl_is_module_enabled('country_phone')) {
					$settings_table_output .= "<div class='cf7rl-accordion'>";
					$settings_table_output .= "<div class='cf7rl-accordion-header" . ($auto_expand_single ? " active" : "") . "'>";
					$settings_table_output .= "<span>Country & Phone Fields Settings</span>";
					$settings_table_output .= "<span class='cf7rl-accordion-icon'>▼</span>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "<div class='cf7rl-accordion-content'" . ($auto_expand_single ? " style='display:block;'" : "") . ">";
					$settings_table_output .= "<table style='width: 100%;'>";
					
					// Set defaults for country & phone options
					if (empty($options['country_default'])) { 				$options['country_default'] = ''; }
					if (empty($options['country_include'])) { 				$options['country_include'] = array(); }
					if (empty($options['country_exclude'])) { 				$options['country_exclude'] = array(); }
					if (empty($options['country_preferred'])) { 			$options['country_preferred'] = array(); }
					if (empty($options['phone_default'])) { 				$options['phone_default'] = ''; }
					if (empty($options['phone_include'])) { 				$options['phone_include'] = array(); }
					if (empty($options['phone_exclude'])) { 				$options['phone_exclude'] = array(); }
					if (empty($options['phone_preferred'])) { 				$options['phone_preferred'] = array(); }
					
					// Get countries list
					$all_countries = cf7rl_get_countries_list();
					
					// Country Dropdown Field Settings
					$settings_table_output .= "<tr><td colspan='3'><h4 style='margin-bottom: 10px;'>Country Dropdown Field Settings</h4></td></tr>";
					
					// Default Country
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Default Country:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='country_default' style='width: 300px;'>";
					$settings_table_output .= "<option value=''>None</option>";
					foreach ($all_countries as $code => $country) {
						$selected = ($options['country_default'] == $code) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . "</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Select a default country to be pre-selected in the dropdown</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Include Countries
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Include Countries:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='country_include[]' multiple class='cf7rl-country-multiselect'>";
					foreach ($all_countries as $code => $country) {
						$selected = in_array($code, $options['country_include']) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . "</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>If specified, only these countries will be available. Leave empty to include all countries.</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Exclude Countries
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Exclude Countries:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='country_exclude[]' multiple class='cf7rl-country-multiselect'>";
					foreach ($all_countries as $code => $country) {
						$selected = in_array($code, $options['country_exclude']) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . "</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Countries to exclude from the dropdown</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Preferred Countries
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Preferred Countries:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='country_preferred[]' multiple class='cf7rl-country-multiselect'>";
					foreach ($all_countries as $code => $country) {
						$selected = in_array($code, $options['country_preferred']) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . "</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>These countries will appear at the top of the dropdown list</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /><br /></td></tr>";
					
					// Phone Field Settings
					$settings_table_output .= "<tr><td colspan='3'><h4 style='margin-bottom: 10px;'>Phone Field Settings</h4></td></tr>";
					
					// Default Country for Phone
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Default Country:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='phone_default' style='width: 300px;'>";
					$settings_table_output .= "<option value=''>None</option>";
					foreach ($all_countries as $code => $country) {
						$selected = ($options['phone_default'] == $code) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . " (" . esc_html($country['dial_code']) . ")</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Select a default country for the phone dial code dropdown</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Include Countries for Phone
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Include Countries:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='phone_include[]' multiple class='cf7rl-country-multiselect'>";
					foreach ($all_countries as $code => $country) {
						$selected = in_array($code, $options['phone_include']) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . " (" . esc_html($country['dial_code']) . ")</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>If specified, only these countries will be available in dial code dropdown. Leave empty to include all.</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Exclude Countries for Phone
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Exclude Countries:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='phone_exclude[]' multiple class='cf7rl-country-multiselect'>";
					foreach ($all_countries as $code => $country) {
						$selected = in_array($code, $options['phone_exclude']) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . " (" . esc_html($country['dial_code']) . ")</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Countries to exclude from the dial code dropdown</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Preferred Countries for Phone
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Preferred Countries:</b></td><td colspan='2'>";
					$settings_table_output .= "<select name='phone_preferred[]' multiple class='cf7rl-country-multiselect'>";
					foreach ($all_countries as $code => $country) {
						$selected = in_array($code, $options['phone_preferred']) ? ' selected' : '';
						$settings_table_output .= "<option value='" . esc_attr($code) . "'" . $selected . ">" . esc_html($country['name']) . " (" . esc_html($country['dial_code']) . ")</option>";
					}
					$settings_table_output .= "</select></td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>These countries will appear at the top of the dial code dropdown</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /><br /></td></tr>";
					
					// Display Options
					$settings_table_output .= "<tr><td colspan='3'><h4 style='margin-bottom: 10px;'>Display Options</h4></td></tr>";
					
					// Show Flags for Country Field
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Show Flags in Country Dropdown:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='checkbox' name='country_show_flags' value='1' "; if (!isset($options['country_show_flags']) || $options['country_show_flags'] == '1') { $settings_table_output .= "checked='checked'"; } $settings_table_output .= "> Yes";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Display country flag emojis in the country dropdown (frontend and admin)</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Show Flags for Phone Field
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Show Flags in Phone Dial Code:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='checkbox' name='phone_show_flags' value='1' "; if (!isset($options['phone_show_flags']) || $options['phone_show_flags'] == '1') { $settings_table_output .= "checked='checked'"; } $settings_table_output .= "> Yes";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Display country flag emojis in the phone dial code dropdown (frontend and admin)</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /><br /></td></tr>";
					
					// Label Customization
					$settings_table_output .= "<tr><td colspan='3'><h4 style='margin-bottom: 10px;'>Label Customization</h4></td></tr>";
					
					// Country Field Label
					if (empty($options['country_label'])) { $options['country_label'] = ''; }
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Country Field Label:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='text' name='country_label' value='" . esc_attr($options['country_label']) . "' style='width: 300px;' placeholder='Country'>";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Customize the placeholder text for the country dropdown (default: \"Country\")</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Phone Field Label
					if (empty($options['phone_label'])) { $options['phone_label'] = ''; }
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Phone Dial Code Label:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='text' name='phone_label' value='" . esc_attr($options['phone_label']) . "' style='width: 300px;' placeholder='Code'>";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Customize the placeholder text for the phone dial code dropdown (default: \"Code\")</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /><br /></td></tr>";
					
					// Width Customization
					$settings_table_output .= "<tr><td colspan='3'><h4 style='margin-bottom: 10px;'>Width Customization</h4></td></tr>";
					
					// Country Dropdown Width
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Country Dropdown Width:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='number' name='country_dropdown_width' value='" . esc_attr($options['country_dropdown_width']) . "' style='width: 100px;' min='50' max='1000'> px";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Set the width of the country dropdown field on the frontend (default: 200px)</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Phone Dropdown Width
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Phone Dial Code Dropdown Width:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='number' name='phone_dropdown_width' value='" . esc_attr($options['phone_dropdown_width']) . "' style='width: 100px;' min='50' max='1000'> px";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Set the width of the phone dial code dropdown field on the frontend (default: 100px)</td></tr>";
					
					$settings_table_output .= "</table>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "</div>";
				}
				
				// Bookings & Appointments settings section - only show if module is enabled
				if (cf7rl_is_module_enabled('bookings')) {
					$settings_table_output .= "<div class='cf7rl-accordion'>";
					$settings_table_output .= "<div class='cf7rl-accordion-header" . ($auto_expand_single ? " active" : "") . "'>";
					$settings_table_output .= "<span>Bookings & Appointments Settings</span>";
					$settings_table_output .= "<span class='cf7rl-accordion-icon'>▼</span>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "<div class='cf7rl-accordion-content'" . ($auto_expand_single ? " style='display:block;'" : "") . ">";
					$settings_table_output .= "<table style='width: 100%;'>";
					
					// Set defaults for booking options
					if (empty($options['booking_time_format'])) { 		$options['booking_time_format'] = '24'; }
					if (empty($options['booking_date_width'])) { 		$options['booking_date_width'] = '200'; }
					if (empty($options['booking_time_width'])) { 		$options['booking_time_width'] = '200'; }
					
					// Time Format
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Time Format:</b></td><td colspan='2'>";
					$settings_table_output .= "<input "; if ($options['booking_time_format'] == "24") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='booking_time_format' value='24'>24-hour (e.g., 14:30) ";
					$settings_table_output .= "<input "; if ($options['booking_time_format'] == "12") { $settings_table_output .= "checked='checked'"; } $settings_table_output .= " type='radio' name='booking_time_format' value='12'>12-hour (e.g., 2:30 PM)";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Choose how time slots are displayed to users</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Date Field Width
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Date Field Width:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='number' name='booking_date_width' value='" . esc_attr($options['booking_date_width']) . "' style='width: 100px;' min='50' max='1000'> px";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Set the width of the booking date field on the frontend (default: 200px)</td></tr>";
					
					$settings_table_output .= "<tr><td colspan='3'><br /></td></tr>";
					
					// Time Field Width
					$settings_table_output .= "<tr><td class='cf7rl_width'><b>Time Field Width:</b></td><td colspan='2'>";
					$settings_table_output .= "<input type='number' name='booking_time_width' value='" . esc_attr($options['booking_time_width']) . "' style='width: 100px;' min='50' max='1000'> px";
					$settings_table_output .= "</td></tr>";
					$settings_table_output .= "<tr><td class='cf7rl_width'></td><td colspan='2'>Set the width of the booking time dropdown field on the frontend (default: 200px)</td></tr>";
					
					$settings_table_output .= "</table>";
					$settings_table_output .= "</div>";
					$settings_table_output .= "</div>";
				}

		$settings_table_output .= "</div>";
	$settings_table_output .= "</div>";
	} // End if ($has_enabled_modules)
	
	// PayPal tab (tab 4) - only show if payments module is enabled
	if (cf7rl_is_module_enabled('payments') && function_exists('cf7rl_paypal_settings_tab')) {
		$paypal_tab_content = cf7rl_paypal_settings_tab();
		// Update display style based on active tab
		if ($active_tab == '4') {
			$paypal_tab_content = str_replace('style="display:none;', 'style="display:block;', $paypal_tab_content);
		}
		$settings_table_output .= $paypal_tab_content;
	}
	
	// Stripe tab (tab 5) - only show if payments module is enabled
	if (cf7rl_is_module_enabled('payments') && function_exists('cf7rl_stripe_settings_tab')) {
		$stripe_tab_content = cf7rl_stripe_settings_tab();
		// Update display style based on active tab
		if ($active_tab == '5') {
			$stripe_tab_content = str_replace('style="display:none;', 'style="display:block;', $stripe_tab_content);
		}
		$settings_table_output .= $stripe_tab_content;
	}
	
	
	// Extensions tab hidden
	// $settings_table_output .= "<div id='3' style='display:none;border: 1px solid #CCCCCC; "; if ($active_tab == '3') { $settings_table_output .= 'display:block;'; } $settings_table_output .= "'>";
	// 	$settings_table_output .= "<div style='background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;'>";
	// 		$settings_table_output .= "&nbsp; Extensions";
	// 	$settings_table_output .= "</div>";
	// 	$settings_table_output .= "<div style='background-color:#fff;padding:8px;'>";
	// 		
	// 		$settings_table_output .= "<table style='width: 100%;'>";
	// 			
	// 			$settings_table_output .= cf7rl_extensions_page();
	// 			
	// 		$settings_table_output .= "</table>";
	// 		
	// 	$settings_table_output .= "</div>";
	// $settings_table_output .= "</div>";




	$settings_table_output .= "<input type='hidden' name='update' value='1'>";
	$settings_table_output .= "<input type='hidden' name='hidden_tab_value' id='hidden_tab_value' value='$active_tab'>";
	
	$settings_table_output .= wp_nonce_field('cf7rl_save_settings', 'cf7rl_nonce_field');

$settings_table_output .= "</form>";













	$settings_table_output .= "</td><td width='3%' valign='top'>";

	$settings_table_output .= "</td><td width='24%' valign='top'>";

	
	// Review Box
	$settings_table_output .= "<div style='border: 2px solid #0073aa; border-radius: 4px; margin-bottom: 15px;'>";
		
		$settings_table_output .= "<div style='background-color:#0073aa;padding:10px;color:#fff;font-size:15px;font-weight: 700;'>";
		$settings_table_output .= "&nbsp; ⭐ Love this plugin?";
		$settings_table_output .= "</div>";
		
		$settings_table_output .= "<div style='background-color:#fff;padding:12px;text-align:center;'>";	
			$settings_table_output .= "<p style='margin-top:0;'>A lot of work went into building this plugin. A quick review helps us keep it free and growing!</p>";
			$settings_table_output .= "<a target='_blank' href='https://wordpress.org/support/plugin/cf7-redirect-thank-you-page/reviews/?filter=5#new-post' class='button-primary' style='font-size: 14px;'>Leave a Review</a>";
		$settings_table_output .= "</div>";
	$settings_table_output .= "</div>";
	
	// Pro Version Box - COMMENTED OUT
	/*
	$settings_table_output .= "<div style='border: 2px solid #1e7e34; border-radius: 4px; background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);'>";
		
		$settings_table_output .= "<div style='padding:10px;color:#fff;font-size:15px;font-weight: 700;'>";
		$settings_table_output .= "&nbsp; 🚀 Pro Version";
		$settings_table_output .= "</div>";
		
		$settings_table_output .= "<div style='background-color:#fff;padding:12px;border-radius: 0 0 2px 2px;'>";	
			
			$settings_table_output .= "<center><label style='font-size:14pt;'><b>Pro Features: </b></label></center><br />";
			
			$settings_table_output .= "<div class='dashicons dashicons-yes' style='margin-bottom: 6px; color: #1e7e34;'></div> Use mail tags on Thank You Page <br />";
			$settings_table_output .= "<div class='dashicons dashicons-yes' style='margin-bottom: 6px; color: #1e7e34;'></div> Form items like dropdown menus can be used to redirect to different URL's <br />";
			$settings_table_output .= "<div class='dashicons dashicons-yes' style='margin-bottom: 6px; color: #1e7e34;'></div> Support the development of more features <br /><br />";
			
			$settings_table_output .= "<center><a target='_blank' href='https://wpplugin.org/downloads/contact-form-7-redirect-thank-you-page-pro/?utm_source=plugin&utm_medium=cf7rl&utm_campaign=settings_page' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px; background: #1e7e34; border-color: #155724;'>Learn More</a></center><br />";
			
		$settings_table_output .= "</div>";
	$settings_table_output .= "</div>";
	*/

	
	$settings_table_output = apply_filters('cf7rl_settings_page_license_section',$settings_table_output);
	
	
	$settings_table_output .= "</td><td width='2%' valign='top'>";



	$settings_table_output .= "</td></tr></table>";
	
	echo $settings_table_output;

}
