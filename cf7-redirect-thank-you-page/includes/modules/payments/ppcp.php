<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function cf7rl_free_ppcp_status() {
	global $cf7rlPpcpStatus;

	if ( !isset( $cf7rlPpcpStatus ) ) {
		$cf7rlPpcpStatus = false;

		$options = cf7rl_free_options();
		$mode = intval( $options['mode'] );
		$env = $mode === 1 ? 'sandbox' : 'live';
		$onboarding = isset( $options['ppcp_onboarding'][$env] ) ? $options['ppcp_onboarding'][$env] : [];

		if ( !empty( $onboarding['seller_id'] ) ) {
			$args = [
				'env' => $env,
				'onboarding' => $onboarding
			];
			$transient = md5( json_encode( $args ) );
			$cf7rlPpcpStatus = get_transient( $transient );
			if ( $cf7rlPpcpStatus === false ) {
				$response = wp_remote_get( cf7rl_FREE_PPCP_API . 'get-status?' . http_build_query( $args ) );
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );
				if ( is_array( $data ) && !empty( $data['env'] ) ) {
					set_transient( $transient, $data, HOUR_IN_SECONDS );
					$cf7rlPpcpStatus = $data;
				}
			}
		} elseif ( !empty( $onboarding ) ) {
			$response = wp_remote_get( cf7rl_FREE_PPCP_API . 'find-seller-id?' . http_build_query( [
				'env' => $env,
				'onboarding' => $onboarding
			] ) );
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
			if ( is_array( $data ) && !empty( $data['env'] ) ) {
				cf7rl_free_ppcp_onboarding_save( sanitize_text_field( $data['env'] ), sanitize_text_field( $data['seller_id'] ) );
				$cf7rlPpcpStatus = $data;
			} elseif ( $onboarding['timestamp'] + 3600 < time() ) {
				unset( $options['ppcp_onboarding'][$env] );
				cf7rl_free_options_update( $options );
			}
		}
	}

	return $cf7rlPpcpStatus;
}

add_action( 'wp_ajax_cf7rl-ppcp-onboarding-start', 'cf7rl_free_ppcp_onboarding_start_ajax' );
function cf7rl_free_ppcp_onboarding_start_ajax() {
	if ( !wp_verify_nonce( $_GET['nonce'], 'cf7rl-ppcp-onboarding-start' ) ) {
		header( 'Location: ' . add_query_arg( ['error' => 'security'], cf7rl_free_ppcp_connect_tab_url() ) );
		die();
	}

    $env = !empty( $_GET['sandbox'] ) ? 'sandbox' : 'live';

	$response = wp_remote_post(
		cf7rl_FREE_PPCP_API . 'signup',
        [
	        'timeout' => 60,
	        'body' => [
                'env' => $env,
		        'return_url' => cf7rl_free_ppcp_connect_tab_url(),
		        'email' => get_bloginfo( 'admin_email' )
            ]
        ]
    );

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( empty( $data['action_url'] ) || empty( $data['tracking_id'] ) ) {
		header( 'Location: ' . add_query_arg( ['error' => 'api'], cf7rl_free_ppcp_connect_tab_url() ) );
        die();
	}

	$options = cf7rl_free_options();
	$options['ppcp_onboarding'][$env] = [
		'timestamp' => time(),
		'tracking_id' => $data['tracking_id'],
		'seller_id' => ''
	];
	$options['mode'] = $env === 'sandbox' ? 1 : 2;
	cf7rl_free_options_update( $options );

    header( "Location: {$data['action_url']}" );
    die();
}

function cf7rl_free_ppcp_connect_tab_url() {
    return add_query_arg(
	    [
		    'page' => 'cf7rl_admin_table',
		    'tab' => '4'
	    ],
        admin_url('admin.php')
    );
}

function cf7rl_free_ppcp_onboarding_save( $env, $seller_id ) {
    $options = cf7rl_free_options();

    if ( $env === 'sandbox' && isset( $options['sandboxaccount'] ) ) {
        unset( $options['sandboxaccount'] );
    } elseif ( $env === 'live' && isset( $options['liveaccount'] ) ) {
	    unset( $options['liveaccount'] );
    }

	$options['ppcp_onboarding'][$env]['seller_id'] = $seller_id;
	cf7rl_free_options_update( $options );
}

add_action( 'wp_ajax_cf7rl-ppcp-disconnect', 'cf7rl_free_ppcp_disconnect_ajax' );
function cf7rl_free_ppcp_disconnect_ajax() {
	if ( !wp_verify_nonce( $_POST['nonce'], 'cf7rl-free-request' ) ) {
		wp_send_json_error( [
			'message' => __( 'The request has not been authenticated. Please reload the page and try again.', 'contact-form-7-paypal-add-on' )
		] );
	}

	$options = cf7rl_free_options();
	$mode = intval( $options['mode'] );
	$env = $mode === 1 ? 'sandbox' : 'live';
	$onboarding = isset( $options['ppcp_onboarding'][$env] ) ? $options['ppcp_onboarding'][$env] : [];

	if ( empty( $onboarding ) ) {
		wp_send_json_error( [
			'message' => __( 'An error occurred while processing your account disconnection request. Please contact our support service.', 'contact-form-7-paypal-add-on' )
		] );
	}

	$args = [
		'env' => $env,
		'onboarding' => $onboarding
	];

	$response = wp_remote_post(
		cf7rl_FREE_PPCP_API . 'disconnect',
		[
			'timeout' => 60,
			'body' => $args
		]
	);

	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body, true );

	if ( empty( $data['success'] ) ) {
		wp_send_json_error( [
			'message' => __( 'An error occurred while processing your account disconnection request. Please contact our support service.', 'contact-form-7-paypal-add-on' )
		] );
	}

	unset( $options['ppcp_onboarding'][$env] );
	cf7rl_free_options_update( $options );

	$transient = md5( json_encode( $args ) );
	delete_transient( $transient );

	wp_send_json_success( [
        'statusHtml' => cf7rl_free_ppcp_status_markup()
    ] );
}
