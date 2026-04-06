<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Convert PHP date format to Flatpickr format
 */
function cf7rl_php_to_flatpickr_format($php_format) {
	$replacements = array(
		'd' => 'd',    // Day of month, 2 digits with leading zeros
		'D' => 'D',    // Day of week, short (Mon)
		'j' => 'j',    // Day of month without leading zeros
		'l' => 'l',    // Day of week, full (Monday)
		'N' => 'N',    // ISO day of week (1=Mon, 7=Sun)
		'S' => '',     // Ordinal suffix (st, nd, rd, th) - not supported
		'w' => 'w',    // Day of week (0=Sun, 6=Sat)
		'F' => 'F',    // Month name, full (January)
		'M' => 'M',    // Month name, short (Jan)
		'm' => 'm',    // Month, 2 digits with leading zeros
		'n' => 'n',    // Month without leading zeros
		'Y' => 'Y',    // Year, 4 digits
		'y' => 'y',    // Year, 2 digits
	);
	
	$flatpickr_format = '';
	$length = strlen($php_format);
	
	for ($i = 0; $i < $length; $i++) {
		$char = $php_format[$i];
		if (isset($replacements[$char])) {
			$flatpickr_format .= $replacements[$char];
		} else {
			$flatpickr_format .= $char;
		}
	}
	
	return $flatpickr_format;
}

/**
 * Enqueue booking module styles and scripts on frontend
 */
add_action('wp_enqueue_scripts', 'cf7rl_booking_enqueue_frontend');
function cf7rl_booking_enqueue_frontend() {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('bookings')) {
		return;
	}
	
	// Enqueue Flatpickr CSS (date picker library)
	wp_enqueue_style(
		'flatpickr',
		'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
		array(),
		'4.6.13'
	);
	
	// Enqueue custom booking CSS
	wp_enqueue_style(
		'cf7rl-booking',
		CF7RL_URL . 'assets/css/booking.css',
		array('flatpickr'),
		'1.0.0'
	);
	
	// Enqueue Flatpickr JS
	wp_enqueue_script(
		'flatpickr',
		'https://cdn.jsdelivr.net/npm/flatpickr',
		array(),
		'4.6.13',
		true
	);
	
	// Enqueue custom booking JS
	wp_enqueue_script(
		'cf7rl-booking',
		CF7RL_URL . 'assets/js/booking.js',
		array('jquery', 'flatpickr'),
		'1.0.0',
		true
	);
	
	// Localize script with AJAX URL and nonce
	$wp_date_format = get_option('date_format');
	$wp_time_format = get_option('time_format');
	$flatpickr_date_format = cf7rl_php_to_flatpickr_format($wp_date_format);
	
	wp_localize_script('cf7rl-booking', 'cf7rlBooking', array(
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('cf7rl_booking_nonce'),
		'timeFormat' => $wp_time_format,
		'dateFormat' => $flatpickr_date_format,
		'strings' => array(
			'selectDate' => __('Please select a date first', 'cf7rl'),
			'noSlots' => __('No available time slots for this date', 'cf7rl'),
			'loading' => __('Loading...', 'cf7rl'),
		)
	));
}

/**
 * Enqueue admin styles and scripts
 */
add_action('admin_enqueue_scripts', 'cf7rl_booking_admin_enqueue');
function cf7rl_booking_admin_enqueue($hook) {
	// Only load on CF7 form edit pages
	if (strpos($hook, 'wpcf7') === false) {
		return;
	}
	
	if (!cf7rl_is_module_enabled('bookings')) {
		return;
	}
	
	wp_enqueue_style(
		'cf7rl-booking-admin',
		CF7RL_URL . 'assets/css/booking_admin.css',
		array(),
		'1.0.0'
	);
	
	wp_enqueue_script(
		'cf7rl-booking-admin',
		CF7RL_URL . 'assets/js/booking_admin.js',
		array('jquery'),
		'1.0.0',
		true
	);
}

/**
 * AJAX handler to get disabled dates for calendar
 */
add_action('wp_ajax_cf7rl_get_disabled_dates', 'cf7rl_ajax_get_disabled_dates');
add_action('wp_ajax_nopriv_cf7rl_get_disabled_dates', 'cf7rl_ajax_get_disabled_dates');
function cf7rl_ajax_get_disabled_dates() {
	check_ajax_referer('cf7rl_booking_nonce', 'nonce');
	
	$form_id = isset($_POST['form_id']) ? absint($_POST['form_id']) : 0;
	if (empty($form_id)) {
		wp_send_json_error();
	}
	
	$options = cf7rl_get_booking_options($form_id);
	$availability = !empty($options['availability']) ? $options['availability'] : cf7rl_get_default_availability();
	$unavailable_dates = !empty($options['unavailable_dates']) ? $options['unavailable_dates'] : array();
	$min_advance_minutes = intval($options['min_advance_minutes']);
	
	// Get disabled days of week (0=Sunday, 6=Saturday)
	// Check all 7 days - if day doesn't exist in array or enabled is false, it's disabled
	$disabled_days = array();
	for ($day_num = 0; $day_num <= 6; $day_num++) {
		if (!isset($availability[$day_num]) || empty($availability[$day_num]['enabled'])) {
			$disabled_days[] = $day_num;
		}
	}
	
	// Check if today should be disabled by actually checking available slots
	$today = current_time('Y-m-d');
	$today_day = intval(current_time('w'));
	$disable_today = false;
	
	// Only check today if it's not already disabled by day of week or unavailable dates
	if (!in_array($today_day, $disabled_days) && !in_array($today, $unavailable_dates)) {
		// Get actual available slots for today using the same logic as time slots endpoint
		$slots = cf7rl_get_available_slots($today, $form_id);
		
		if (!empty($slots)) {
			// Filter slots by minimum advance notice (same logic as time slots endpoint)
			$earliest_bookable = strtotime(current_time('Y-m-d H:i')) + ($min_advance_minutes * 60);
			$selected_date = strtotime($today);
			
			$slots = array_filter($slots, function($slot) use ($selected_date, $earliest_bookable) {
				$slot_timestamp = $selected_date + strtotime($slot, 0);
				return $slot_timestamp >= $earliest_bookable;
			});
		}
		
		// If no slots remain after filtering, disable today
		if (empty($slots)) {
			$disable_today = true;
		}
	}
	
	wp_send_json_success(array(
		'disabledDays' => $disabled_days,
		'unavailableDates' => array_values($unavailable_dates),
		'disableToday' => $disable_today,
		'minAdvanceMinutes' => $min_advance_minutes
	));
}

/**
 * AJAX handler to get available time slots
 */
add_action('wp_ajax_cf7rl_get_time_slots', 'cf7rl_ajax_get_time_slots');
add_action('wp_ajax_nopriv_cf7rl_get_time_slots', 'cf7rl_ajax_get_time_slots');
function cf7rl_ajax_get_time_slots() {
	check_ajax_referer('cf7rl_booking_nonce', 'nonce');
	
	$date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : '';
	$form_id = isset($_POST['form_id']) ? absint($_POST['form_id']) : 0;
	
	if (empty($date) || empty($form_id)) {
		wp_send_json_error(array('message' => __('Invalid request', 'cf7rl')));
	}
	
	$slots = cf7rl_get_available_slots($date, $form_id);
	
	// Get minimum advance notice in minutes
	$options = cf7rl_get_booking_options($form_id);
	$min_advance_minutes = intval($options['min_advance_minutes']);
	
	// Calculate the earliest bookable time based on minimum advance notice
	$earliest_bookable = strtotime(current_time('Y-m-d H:i')) + ($min_advance_minutes * 60);
	$selected_date = strtotime($date);
	
	if (!empty($slots)) {
		$slots = array_values(array_filter($slots, function($slot) use ($selected_date, $earliest_bookable) {
			$slot_timestamp = $selected_date + strtotime($slot, 0);
			return $slot_timestamp >= $earliest_bookable;
		}));
	}
	
	wp_send_json_success(array('slots' => $slots));
}
