<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// returns the form id of the forms that have redirect enabled - used for redirect method 1 and method 2
function cf7rl_forms_enabled() {

	// Check if redirect module is enabled
	if ( ! cf7rl_is_module_enabled( 'redirect' ) ) {
		return json_encode( array() );
	}

	// array that will contain which forms redirect is enabled on
	$enabled = array();
	
	$args = array(
		'posts_per_page'   => 999,
		'post_type'        => 'wpcf7_contact_form',
		'post_status'      => 'publish',
	);
	$posts_array = get_posts($args);
	
	
	// loop through them and find out which ones have redirect enabled
	foreach($posts_array as $post) {
		
		$post_id = $post->ID;
		
		// Check if redirect is enabled (using new meta key)
		$enable = get_post_meta( $post_id, "_cf7rl_redirect_enable", true);
		
		if ($enable == "1") {
			
			$cf7rl_redirect_type = get_post_meta( $post_id, "_cf7rl_redirect_type", true);
			$cf7rl_url = get_post_meta( $post_id, "_cf7rl_url", true);
			$cf7rl_tab = get_post_meta( $post_id, "_cf7rl_tab", true);
			
			$enabled[] = '|'.$post_id.'|'.$cf7rl_redirect_type.'|'.$cf7rl_url.'|'.$cf7rl_tab.'|';
			
		}
		
	}

	return json_encode($enabled);

}


// for redirect method 2 - this must be loaded for redirect method 2 regardless of if the form has redirect enabled
$options = get_option('cf7rl_options');

if (isset($options['redirect'])) {
	if ($options['redirect'] == "2"  || $options['redirect'] == '') {
		
		if (!defined('WPCF7_LOAD_JS')) {
			define('WPCF7_LOAD_JS', false);
		}
		
	}
}



// return thank you page form
add_action('wp_ajax_cf7rl_get_form_thank', 'cf7rl_get_form_thank_callback');
add_action('wp_ajax_nopriv_cf7rl_get_form_thank', 'cf7rl_get_form_thank_callback');
function cf7rl_get_form_thank_callback() {

	// Check if redirect module is enabled
	if ( ! cf7rl_is_module_enabled( 'redirect' ) ) {
		wp_die();
	}

	// Verify nonce for security
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'cf7rl_thank_you_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed' ) );
		wp_die();
	}

	$formid = absint($_POST['formid']);
	
	// Verify this is a valid CF7 form
	if ( get_post_type( $formid ) !== 'wpcf7_contact_form' ) {
		wp_send_json_error( array( 'message' => 'Invalid form ID' ) );
		wp_die();
	}
	
	$cf7rl_thank_you_page = get_post_meta($formid, "_cf7rl_thank_you_page", true);	
	
	$result = '';
	
	// thank you page - use wp_kses_post to allow safe HTML
	$result .= "<div class='cf7rl_thank'>";
	$result .= wp_kses_post($cf7rl_thank_you_page);
	$result .= "</div>";


	$response = array(
		'html'         		=> $result,
	);

	echo json_encode($response);
	wp_die();
}