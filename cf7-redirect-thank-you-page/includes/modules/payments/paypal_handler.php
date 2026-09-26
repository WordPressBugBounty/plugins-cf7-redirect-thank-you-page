<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


/**
 * Used for testing to make sure the IPN can listen to URL calls.
 * @since 1.8
 * @return string
 */
add_action('template_redirect','cf7rl_ipn_test');
function cf7rl_ipn_test() {

	if (isset($_REQUEST['cf7rl_test'])) {
		echo __("Contact Form 7 - PayPal Add-on - Test Successful", 'contact-form-7-paypal-add-on');
		exit;
	}
}


/**
 * PayPal notify url.
 * @since 1.8
 * @return string or array
 */
function cf7rl_get_paypal_notify_url($return = 'str') {
	$options = cf7rl_free_options();
	$mode_paypal = $options['mode'] == '1' ? 'sandbox' : 'production';

	$namespace = 'paypalipn/v1';
	$route = '/cf7rl_' . $mode_paypal;

	if ($return == 'str') {
		$result = add_query_arg('rest_route', '/' . $namespace . $route, get_site_url());
	} else {
		$result = array(
			'namespace'	=> $namespace,
			'route'		=> $route
		);
	}

	return $result;
}


/**
 * Register PayPal IPN listener.
 * @since 1.8
 */
add_action('rest_api_init', 'cf7rl_paypal_ipn_listener');
function cf7rl_paypal_ipn_listener() {
	$notify_url = cf7rl_get_paypal_notify_url('arr');
    register_rest_route($notify_url['namespace'], $notify_url['route'], array(
        'methods' 				=> 'POST',
        'callback' 				=> 'cf7rl_paypal_ipn_handler',
        'permission_callback'	=> 'cf7rl_paypal_ipn_auth'
    ));
}


/**
 * PayPal IPN permission callback.
 * @since 1.8
 * @return bool
 */
function cf7rl_paypal_ipn_auth() {
	return true; // security done in the handler
}


/**
 * PayPal IPN handler.
 * @since 1.8
 */
function cf7rl_paypal_ipn_handler() {
	$payload = file_get_contents('php://input');
	parse_str($payload, $data);

	// fields used below - $data itself is posted back to PayPal unchanged
	$ipn = array();
	foreach (array('payment_status', 'invoice', 'txn_id', 'receiver_email', 'business', 'receiver_id', 'mc_gross', 'mc_currency') as $key) {
		$ipn[$key] = isset($data[$key]) && is_string($data[$key]) ? trim($data[$key]) : '';
	}

	if (strtolower($ipn['payment_status']) != 'completed') {
		return;
	}

	// invoice is the payment id this site sent to PayPal
	$payment_id = (int) $ipn['invoice'];

	if (empty($payment_id) || get_post_type($payment_id) !== 'cf7rl_payments') {
		cf7rl_paypal_ipn_log('invoice ' . $payment_id . ' is not a payment, ignored');
		return;
	}

	$options = cf7rl_free_options();
	$paypal_post_url = 'https://www.' . ($options['mode'] == '1' ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr';

	$data['cmd'] = '_notify-validate';
	$args = array(
		'method'           => 'POST',
		'timeout'          => 45,
		'redirection'      => 5,
		'httpversion'      => '1.1',
		'blocking'         => true,
		'headers'          => array(
			'connection'   => 'close',
			'content-type' => 'application/x-www-form-urlencoded',
		),
		'body'             => $data
	);

	// Get response
	$response = wp_remote_post($paypal_post_url, $args);

	// could not ask PayPal - an error response makes PayPal resend the IPN later
	if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
		cf7rl_paypal_ipn_log('payment #' . $payment_id . ': could not reach PayPal to verify the IPN, PayPal will resend it');
		return new WP_REST_Response(null, 500);
	}

	// anyone can post to this url, so only act on messages PayPal confirms it sent
	if (strtolower(trim(wp_remote_retrieve_body($response))) != 'verified') {
		cf7rl_paypal_ipn_log('payment #' . $payment_id . ': PayPal did not verify the IPN, ignored');
		return;
	}

	$error = cf7rl_paypal_ipn_payment_error($ipn, $payment_id, $options);

	if (!empty($error)) {
		cf7rl_paypal_ipn_log('payment #' . $payment_id . ': ' . $error . ', not completed');
		return;
	}

	cf7rl_complete_payment($payment_id, 'completed', $ipn['txn_id']);
}


/**
 * Check a verified PayPal IPN paid this site's PayPal account the full payment amount.
 * Verification only proves PayPal sent the IPN - buyers can edit the PayPal link, or pay their own account.
 * @since 1.2.2
 * @return string Why the IPN does not pay for the payment, or an empty string if it does
 */
function cf7rl_paypal_ipn_payment_error($ipn, $payment_id, $options) {
	// the account buyers are sent to pay - a merchant account ID or an email address
	$account_key = $options['mode'] == '1' ? 'sandboxaccount' : 'liveaccount';
	$account = isset($options[$account_key]) ? strtolower(trim($options[$account_key])) : '';
	$receivers = array_map('strtolower', array($ipn['receiver_email'], $ipn['business'], $ipn['receiver_id']));

	if ($account === '') {
		return 'no ' . $account_key . ' setting is saved to check the payment against';
	}

	if (!in_array($account, $receivers, true)) {
		return 'paid to ' . $ipn['receiver_email'] . ', which does not match the ' . $account_key . ' setting';
	}

	$currency = cf7rl_free_currency_code_to_iso($options['currency']);

	if (strtoupper($ipn['mc_currency']) !== $currency) {
		return 'paid in ' . $ipn['mc_currency'] . ' instead of ' . $currency;
	}

	// PayPal can add tax or shipping, so the buyer must pay at least the payment amount
	$amount = (float) get_post_meta($payment_id, 'amount', true);

	if (round((float) $ipn['mc_gross'], 2) < round($amount, 2)) {
		return 'paid ' . $ipn['mc_gross'] . ' instead of ' . number_format($amount, 2, '.', '');
	}

	return '';
}


/**
 * Log why a PayPal IPN did not complete a payment, when WP_DEBUG is on.
 * @since 1.2.2
 */
function cf7rl_paypal_ipn_log($message) {
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('CF7RL PayPal IPN: ' . $message);
	}
}
