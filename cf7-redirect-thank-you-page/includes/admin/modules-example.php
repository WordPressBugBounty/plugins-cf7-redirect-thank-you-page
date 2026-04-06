<?php
/**
 * Example: How to add custom modules to CF7 Redirect
 * 
 * This file demonstrates how developers can add their own modules
 * to the CF7 Redirect plugin using the modular system.
 * 
 * DO NOT include this file in your plugin - it's just an example!
 */

// Example 1: Add a custom module
add_filter( 'cf7rl_available_modules', 'my_custom_cf7rl_module' );

function my_custom_cf7rl_module( $modules ) {
	
	// Add your custom module to the modules array
	$modules['my_custom_feature'] = array(
		'name' => __( 'My Custom Feature', 'my-textdomain' ),
		'description' => __( 'This is a custom feature that extends CF7 Redirect functionality.', 'my-textdomain' ),
		'default' => false, // Set to true if you want it enabled by default
		'enabled' => false
	);
	
	return $modules;
}


// Example 2: Check if your module is enabled before loading features
function my_custom_feature_init() {
	
	// Only load your feature if the module is enabled
	if ( cf7rl_is_module_enabled( 'my_custom_feature' ) ) {
		
		// Load your custom feature code here
		// include_once( 'path/to/your/feature.php' );
		
		// Or add hooks/filters
		add_action( 'wpcf7_before_send_mail', 'my_custom_feature_function' );
		
	}
}
add_action( 'plugins_loaded', 'my_custom_feature_init' );


// Example 3: Multiple modules from one plugin
add_filter( 'cf7rl_available_modules', 'my_plugin_add_modules' );

function my_plugin_add_modules( $modules ) {
	
	$modules['feature_one'] = array(
		'name' => __( 'Feature One', 'my-textdomain' ),
		'description' => __( 'Description of feature one.', 'my-textdomain' ),
		'default' => true
	);
	
	$modules['feature_two'] = array(
		'name' => __( 'Feature Two', 'my-textdomain' ),
		'description' => __( 'Description of feature two.', 'my-textdomain' ),
		'default' => false
	);
	
	return $modules;
}
