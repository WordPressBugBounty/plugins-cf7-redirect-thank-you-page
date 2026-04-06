<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Add cronjob.
 * @since 1.8
 */
add_action('wp', 'cf7rl_payment_check_status_cronjob');
function cf7rl_payment_check_status_cronjob() {
    if ( !wp_next_scheduled('cf7rl_payment_check_status') ) {
        wp_schedule_event(time(), 'hourly', 'cf7rl_payment_check_status');
    }
}

/**
 * Add cronjob action.
 * @since 1.8
 */
add_action( 'cf7rl_payment_check_status', 'cf7rl_payment_check_status_func' );
function cf7rl_payment_check_status_func() {
	global $wpdb;

	$wpdb->query( 
		"UPDATE {$wpdb->posts}
		 SET post_status = 'cf7rl-abandoned'
		 WHERE post_type = 'cf7rl_payments'
		   AND post_status = 'cf7rl-pending'
		   AND post_date < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)"
	);
}
