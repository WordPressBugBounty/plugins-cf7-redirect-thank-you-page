<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// add redirect menu under contact form 7 menu
add_action( 'admin_menu', 'cf7rl_admin_menu', 20 );
function cf7rl_admin_menu() {
	// Add Database menu item if module is enabled
	if ( cf7rl_is_module_enabled( 'database_submissions' ) ) {
		$hook = add_submenu_page('wpcf7',__( 'Database', 'contact-form-7' ),__( 'Database', 'contact-form-7' ),'manage_options', 'cf7rl_database','cf7rl_db_admin_page');
		
		// Add screen options on page load
		add_action( "load-{$hook}", 'cf7rl_db_screen_options' );
	}
}

// Add Business Essentials menu last with higher priority
add_action( 'admin_menu', 'cf7rl_business_essentials_menu', 30 );
function cf7rl_business_essentials_menu() {
	add_submenu_page('wpcf7',__( 'Business Essentials', 'contact-form-7' ),__( 'Business Essentials', 'contact-form-7' ),'wpcf7_edit_contact_forms', 'cf7rl_admin_table','cf7rl_admin_table');
}

// plugin page links
add_filter('plugin_action_links', 'cf7rl_plugin_settings_link', 10, 2 );
function cf7rl_plugin_settings_link($links,$file) {

	if ($file == 'cf7-redirect-thank-you-page/cf7-redirect.php') {
		
		$settings_link = 	'<a href="admin.php?page=cf7rl_admin_table">' . __('Settings', 'PTP_LOC') . '</a>';
		
		array_unshift($links, $settings_link);
	}

	return $links;
}
