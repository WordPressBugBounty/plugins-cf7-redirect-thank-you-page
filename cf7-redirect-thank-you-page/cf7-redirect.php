<?php

/*
Plugin Name: Business Essentials for Contact Form 7
Plugin URI: https://wpplugin.org/
Description: Adds Business Modules for Contact Form 7 including: Payments with PayPal and Stripe, Redirect, Goolge reCAPTCHA, Country and Phone Fields, Database Storage for Submissions, Booking and Appointments, and Material Design Theme. 
Author: Scott Paterson
Author URI: https://wpplugin.org
License: GPL2
Version: 1.2.1
Requires Plugins: contact-form-7
Requires PHP: 5.6
Requires at least: 3.0
*/

/*  Copyright 2014-2026 Scott Paterson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/



// plugin variable: cf7rl

// empty function used by pro version to check if free version is installed
function cf7rl_free() {
}

// check if pro version is attempting to be activated - if so, then deactive that plugin
if (function_exists('cf7rl_pro')) {

	deactivate_plugins('contact-form-7-redirect-thank-you-page-pro/cf7-redirect.php');
	
} else {

	//  plugin functions
	register_activation_hook( 	__FILE__, "cf7rl_activate" );
	register_deactivation_hook( __FILE__, "cf7rl_deactivate" );
	register_uninstall_hook( 	__FILE__, "cf7rl_uninstall" );

	function cf7rl_activate() {
		
		// Migrate settings from version 1.1 to 1.2
		cf7rl_migrate_from_v1_1();
		
		// default options
		$cf7rl_options = array(
			'redirect'			=> '1',
			'modules'			=> array('redirect'),
			'request_method'	=> '1',
			'session'			=> '1',
			'recaptcha_site_key'	=> '',
			'recaptcha_secret_key'	=> '',
			'recaptcha_position'	=> 'below',
			'recaptcha_theme'		=> 'light',
			'recaptcha_error_message' => __('Please complete the reCAPTCHA verification to submit this form.', 'cf7rl'),
			'country_default'		=> '',
			'country_include'		=> array(),
			'country_exclude'		=> array(),
			'country_preferred'		=> array(),
			'country_show_flags'	=> '1',
			'phone_default'			=> '',
			'phone_include'			=> array(),
			'phone_exclude'			=> array(),
			'phone_preferred'		=> array(),
			'phone_show_flags'		=> '1',
			// Payment module defaults (PayPal & Stripe)
			'currency'			=> '25',
			'language'			=> '3',
			'mode'				=> '2',
			'mode_stripe'		=> '2',
			'cancel'			=> '',
			'return'			=> '',
			'redirect_payment'	=> '1',
			'success'			=> __('Payment Successful', 'cf7rl'),
			'failed'			=> __('Payment Failed', 'cf7rl'),
			'stripe_return'		=> '',
			'acct_id_test'		=> '',
			'acct_id_live'		=> '',
			'ppcp_onboarding'	=> array(
				'live' => array(),
				'sandbox' => array()
			),
			'ppcp_notice_dismissed' => 1,
			'stripe_connect_notice_dismissed' => 1
		);
		
		add_option("cf7rl_options", $cf7rl_options);
		
		// Store installation timestamp for review notice
		if (!get_option('cf7rl_install_date')) {
			add_option('cf7rl_install_date', time());
		}
		
		// Create database table for submissions
		global $wpdb;
		$table_name = $wpdb->prefix . 'cf7_submissions';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			form_title varchar(255) NOT NULL,
			submission_data longtext NOT NULL,
			submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY form_id (form_id),
			KEY submission_date (submission_date)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		// Create database table for bookings
		$bookings_table = $wpdb->prefix . 'cf7_bookings';
		
		$sql_bookings = "CREATE TABLE $bookings_table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			booking_date date NOT NULL,
			booking_time time NOT NULL,
			submission_data longtext NOT NULL,
			status varchar(20) DEFAULT 'confirmed' NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY form_id (form_id),
			KEY booking_date (booking_date),
			KEY status (status)
		) $charset_collate;";
		
		dbDelta( $sql_bookings );
		
	}

	function cf7rl_deactivate() {
		
		delete_option("cf7rl_my_plugin_notice_shown");
		delete_option("cf7rl_install_date");
		delete_option("cf7rl_review_notice_dismissed");
		
	}

	function cf7rl_uninstall() {
	}

	/**
	 * Migrate settings from version 1.1 to 1.2
	 * This handles the meta key change from _cf7rl_enable to _cf7rl_redirect_enable
	 */
	function cf7rl_migrate_from_v1_1() {
		// Check if migration has already been done
		if (get_option('cf7rl_migrated_v1_1_to_v1_2')) {
			return;
		}
		
		// Direct database query to copy old meta key to new meta key
		global $wpdb;
		
		// Copy _cf7rl_enable to _cf7rl_redirect_enable for all posts that have it set to "1"
		$wpdb->query(
			"INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
			SELECT post_id, '_cf7rl_redirect_enable', meta_value
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_cf7rl_enable'
			AND meta_value = '1'
			AND post_id NOT IN (
				SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_cf7rl_redirect_enable'
			)"
		);
		
		// Delete the old _cf7rl_enable keys so they don't conflict with payments module
		$wpdb->query(
			"DELETE FROM {$wpdb->postmeta}
			WHERE meta_key = '_cf7rl_enable'
			AND post_id IN (
				SELECT post_id FROM (
					SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_cf7rl_redirect_enable'
				) AS migrated
			)"
		);
		
		// Mark migration as complete
		update_option('cf7rl_migrated_v1_1_to_v1_2', true);
	}

	// Define plugin URL constant
	define('CF7RL_URL', plugin_dir_url(__FILE__));

	// Run migration on init to catch updates (activation hook doesn't run on updates)
	// Must run on init because wpcf7_contact_form post type is registered on init by CF7
	add_action('init', 'cf7rl_check_version_migration', 20);
	
	function cf7rl_check_version_migration() {
		cf7rl_migrate_from_v1_1();
	}

	// check to make sure contact form 7 is installed and active
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		
		// Load modules system first (needed by other files)
		include_once('includes/admin/modules.php');
		
		// public includes
		include_once('includes/functions.php');
		include_once('includes/redirect_methods.php');
		include_once('includes/enqueue.php');
		
		
		// Load reCAPTCHA module if enabled
		if (cf7rl_is_module_enabled('recaptcha')) {
			include_once('includes/modules/recaptcha/recaptcha_functions.php');
			include_once('includes/modules/recaptcha/enqueue.php');
			
			// Load admin functions only in admin
			if (is_admin()) {
				include_once('includes/modules/recaptcha/admin_functions.php');
			}
		}
		
		// Load Country & Phone Fields module
		if (cf7rl_is_module_enabled('country_phone')) {
			include_once('includes/modules/country_phone/country_phone_functions.php');
			include_once('includes/modules/country_phone/enqueue.php');
			include_once('includes/modules/country_phone/admin_functions.php');
		}
		
		// Load Database Submissions module
		if (cf7rl_is_module_enabled('database_submissions')) {
			include_once('includes/modules/database_submissions/database_functions.php');
			include_once('includes/modules/database_submissions/admin_page.php');
		}
		
		// Load Bookings module
		if (cf7rl_is_module_enabled('bookings')) {
			include_once('includes/modules/bookings/booking_functions.php');
			include_once('includes/modules/bookings/admin_functions.php');
			include_once('includes/modules/bookings/enqueue.php');
			include_once('includes/modules/bookings/booking_admin_tab.php');
		}
		
		// Load Material Theme module
		if (cf7rl_is_module_enabled('material_theme')) {
			include_once('includes/modules/material_theme/material_theme_functions.php');
			include_once('includes/modules/material_theme/admin_functions.php');
			include_once('includes/modules/material_theme/enqueue.php');
		}
		
		// Load Payments module (PayPal & Stripe)
		if (cf7rl_is_module_enabled('payments')) {
			// Load payment helper functions first
			include_once('includes/modules/payments/payment_functions.php');
			
			// Load Stripe library if not already loaded
			if (!class_exists('Stripe\Stripe')) {
				include_once('includes/modules/payments/stripe_library/init.php');
			}
			
			// Load payments functionality
			include_once('includes/modules/payments/payments.inc.php');
			
			// Load redirect methods for payments
			include_once('includes/modules/payments/redirect_methods.php');
			include_once('includes/modules/payments/redirect_paypal.php');
			include_once('includes/modules/payments/redirect_stripe.php');
			
			// Load Stripe Connect
			include_once('includes/modules/payments/stripe-connect.php');
			
			// Load PayPal Commerce Platform
			include_once('includes/modules/payments/ppcp.php');
			include_once('includes/modules/payments/ppcp_frontend.php');
			
			// Load enqueue for payments
			include_once('includes/modules/payments/enqueue.php');
			
			// Load admin tabs page for payments
			if (is_admin()) {
				include_once('includes/modules/payments/tabs_page.php');
				include_once('includes/modules/payments/settings_page.php');
			}
		}
		
		// admin includes
		if (is_admin()) {
			include_once('includes/admin/tabs_page.php');
			include_once('includes/admin/menu_links.php');
			include_once('includes/admin/settings_page.php');
			include_once('includes/admin/extensions.php');
		}
		
	} else {
			
		// give warning if contact form 7 is not active
		function cf7rl_my_admin_notice() {
			?>
			<div class="error">
				<p><?php _e( '<b>Contact Form 7 - Redirect Logic:</b> Contact Form 7 is not installed and / or active! Please install or activate: <a target="_blank" href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7</a>.', 'cf7rl' ); ?></p>
			</div>
			<?php
		}
		add_action( 'admin_notices', 'cf7rl_my_admin_notice' );
		
	}

	// Define plugin version constant - dynamically read from plugin header
	if (!defined('CF7RL_VERSION_NUM')) {
		$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
		define('CF7RL_VERSION_NUM', $plugin_data['Version']);
	}

	// Add deactivation survey
	function cf7rl_enqueue_deactivation_survey() {
		if (get_current_screen() && get_current_screen()->id === 'plugins') {
			wp_enqueue_script('cf7rl-deactivation-survey', plugins_url('assets/js/deactivation-survey.js', __FILE__), array('jquery'), CF7RL_VERSION_NUM, true);
			wp_localize_script('cf7rl-deactivation-survey', 'cf7rlDeactivationSurvey', array(
				'pluginVersion' => CF7RL_VERSION_NUM,
				'deactivationOptions' => array(
					// 'upgraded_to_pro' => __('I upgraded to the Pro version', 'cf7rl'),
					'no_longer_needed' => __('I no longer need the plugin', 'cf7rl'),
					'found_better' => __('I found a better plugin', 'cf7rl'),
					'not_working' => __('The plugin is not working', 'cf7rl'),
					'temporary' => __('It\'s a temporary deactivation', 'cf7rl'),
					'other' => __('Other', 'cf7rl')
				),
				'strings' => array(
					'title' => __('Business Essentials for Contact Form 7 Deactivation', 'cf7rl'),
					'description' => __('If you have a moment, please let us know why you are deactivating. All submissions are anonymous and we only use this feedback to improve this plugin.', 'cf7rl'),
					'otherPlaceholder' => __('Please tell us more...', 'cf7rl'),
					'skipButton' => __('Skip & Deactivate', 'cf7rl'),
					'submitButton' => __('Submit & Deactivate', 'cf7rl'),
					'cancelButton' => __('Cancel', 'cf7rl'),
					'betterPluginQuestion' => __('What is the name of the plugin?', 'cf7rl'),
					'notWorkingQuestion' => __('We\'re sorry to hear that. Can you describe the issue?', 'cf7rl'),
					'errorRequired' => __('Error: Please complete the required field.', 'cf7rl')
				)
			));
		}
	}
	add_action('admin_enqueue_scripts', 'cf7rl_enqueue_deactivation_survey');
}

?>