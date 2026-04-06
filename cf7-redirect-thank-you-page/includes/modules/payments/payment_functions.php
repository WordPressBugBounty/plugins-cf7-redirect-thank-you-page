<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Define constants for payments module - use main plugin version if available
if (!defined('cf7rl_VERSION_NUM')) {
	if (defined('CF7RL_VERSION_NUM')) {
		define('cf7rl_VERSION_NUM', CF7RL_VERSION_NUM);
	} else {
		define('cf7rl_VERSION_NUM', '1.2');
	}
}

define( 'cf7rl_STRIPE_CONNECT_ENDPOINT', 'https://wpplugin.org/stripe/connect.php' );
define( 'cf7rl_FREE_PPCP_API', 'https://wpplugin.org/ppcp-cf7pp/' );
define( 'cf7rl_FREE_URL', CF7RL_URL );

/**
 * Get plugin options
 */
function cf7rl_free_options() {
	$default = [
		'currency' => '25',
		'language' => '3',
		'mode' => '2',
		'cancel' => '',
		'return' => '',
		'redirect' => '1',
		'redirect_payment' => '1',
		'request_method' => '1',
		'session' => '1',
		'success' => __('Payment Successful', 'contact-form-7-paypal-add-on'),
		'failed' => __('Payment Failed', 'contact-form-7-paypal-add-on'),
		'stripe_return' => '',
		'mode_stripe' => '2',
		'acct_id_test' => '',
		'acct_id_live' => '',
		'ppcp_onboarding' => [
			'live' => [],
			'sandbox' => []
		],
		'ppcp_notice_dismissed' => 1,
		'stripe_connect_notice_dismissed' => 1
	];
	$options = (array) get_option( 'cf7rl_options' );

	return array_merge( $default, $options );
}

/**
 * Update plugin options
 */
function cf7rl_free_options_update( $options ) {
	update_option( 'cf7rl_options', $options );
}

// format currency
function cf7rl_format_currency($price) {
	$price = floatval(preg_replace('/[^\d\.]/', '', $price));
	$price =number_format((float)$price, 2, '.', '');
	return $price;
}

/**
 * Convert numeric currency code to ISO 4217
 * @since 1.9.4
 * @return string
 */
function cf7rl_free_currency_code_to_iso( $code ) {
	$currencies = [
		'1' => 'AUD',
		'2' => 'BRL',
		'3' => 'CAD',
		'4' => 'CZK',
		'5' => 'DKK',
		'6' => 'EUR',
		'7' => 'HKD',
		'8' => 'HUF',
		'9' => 'ILS',
		'10' => 'JPY',
		'11' => 'MYR',
		'12' => 'MXN',
		'13' => 'NOK',
		'14' => 'NZD',
		'15' => 'PHP',
		'16' => 'PLN',
		'17' => 'GBP',
		'18' => 'RUB',
		'19' => 'SGD',
		'20' => 'SEK',
		'21' => 'CHF',
		'22' => 'TWD',
		'23' => 'THB',
		'24' => 'TRY',
		'25' => 'USD'
	];

	return !empty( $currencies[$code] ) ? $currencies[$code] : 'USD';
}

/**
 * Convert numeric language code to locale code
 * @since 1.9.4
 * @return string
 */
function cf7rl_free_language_code_to_locale( $code ) {
	$languages = [
		'1' => 'da_DK',
		'2' => 'nl_BE',
		'3' => 'en_US',
		'4' => 'fr_CA',
		'5' => 'de_DE',
		'6' => 'he_IL',
		'7' => 'it_IT',
		'8' => 'ja_JP',
		'9' => 'no_NO',
		'10' => 'pl_PL',
		'11' => 'pt_BR',
		'12' => 'ru_RU',
		'13' => 'es_ES',
		'14' => 'sv_SE',
		'15' => 'zh_CN',
		'16' => 'zh_HK',
		'17' => 'zh_TW',
		'18' => 'tr_TR',
		'19' => 'th_TH',
		'20' => 'en_GB'
	];

	return !empty( $languages[$code] ) ? $languages[$code] : 'default';
}

// Enable CF7 AJAX for payments
function cf7rl_payments_enable_cf7_ajax() {
	if ( function_exists( 'wpcf7' ) ) {  // Check if Contact Form 7 is active
		add_filter( 'wpcf7_load_js', '__return_true' ); // This is required to be enabled for redirection to work
	}
}
add_action( 'init', 'cf7rl_payments_enable_cf7_ajax' );

// start session if not already started and session support is enabled
$options = cf7rl_free_options();

if (empty($options['session'])) {
	$session = '1';
} else {
	$session = $options['session'];
}

if ($session == '2') {
	function cf7rl_payments_session() {
		if(!session_id()) {
			session_start();
			session_write_close();
		}
	}
	add_action('init', 'cf7rl_payments_session', 1);
	
}
