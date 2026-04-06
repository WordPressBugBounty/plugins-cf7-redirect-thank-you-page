<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



// admin enqueue for payments
function cf7rl_payments_admin_enqueue() {

	// admin css for payments
	wp_register_style('cf7rl-payments-admin-css', CF7RL_URL . 'assets/css/payments_admin.css', array(), cf7rl_VERSION_NUM);
	wp_enqueue_style('cf7rl-payments-admin-css');

	// admin js
	wp_enqueue_script('cf7rl-payments-admin', CF7RL_URL . 'assets/js/payments_admin.js', array('jquery'), cf7rl_VERSION_NUM);
	wp_localize_script( 'cf7rl-payments-admin', 'cf7rl', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce( 'cf7rl-free-request' )
	] );
}
add_action('admin_enqueue_scripts','cf7rl_payments_admin_enqueue');












$options = cf7rl_free_options();
$request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set

// Register REST API endpoints only if the request method is set to 2
if ($request_method == 2) {
    add_action('rest_api_init', function () {
        // Register endpoint for form post
        register_rest_route('cf7rl/v1', '/cf7rl_get_form_post', array(
            'methods' => 'GET',
            'callback' => 'cf7rl_get_form_post_callback',
            'permission_callback' => '__return_true', // Adjust as needed
        ));

        // Register endpoint for stripe success message
        register_rest_route('cf7rl/v1', '/cf7rl_get_form_stripe_success', array(
            'methods' => 'GET',
            'callback' => 'cf7rl_get_form_stripe_success_callback',
            'permission_callback' => '__return_true', // Adjust as needed
        ));
    });
}

// Public enqueue for payments
function cf7rl_payments_public_enqueue() {
    $site_url = get_home_url();
    $path_paypal = $site_url . '/?cf7rl_paypal_redirect=';
    $path_stripe = $site_url . '/?cf7rl_stripe_redirect=';

    $options = cf7rl_free_options();
    $request_method = isset($options['request_method']) ? $options['request_method'] : 1; // Default to 1 if not set

	// redirect method js for payments
	wp_enqueue_script('cf7rl-payments-redirect_method', CF7RL_URL . 'assets/js/payments_redirect_method.js', array('jquery'), cf7rl_VERSION_NUM);
	wp_localize_script('cf7rl-payments-redirect_method', 'ajax_object_cf7rl',
		array (
			'ajax_url' 			=> admin_url('admin-ajax.php'),
			'rest_url'          => rest_url('cf7rl/v1/'),
			'request_method'    => $request_method,
			'forms' 			=> cf7rl_payment_forms_enabled(),
			'path_paypal'		=> $path_paypal,
			'path_stripe'		=> $path_stripe,
			'method'			=> $options['redirect_payment'],
			'nonce'				=> wp_create_nonce('cf7rl_payment_nonce'),
		)
	);

}
add_action('wp_enqueue_scripts', 'cf7rl_payments_public_enqueue', 10);
