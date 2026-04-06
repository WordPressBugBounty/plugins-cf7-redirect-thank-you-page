<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get booking options for a specific form
 */
function cf7rl_get_booking_options($form_id = null) {
	if ($form_id) {
		// Get form-specific settings
		$min_advance_raw = get_post_meta($form_id, '_cf7rl_booking_min_advance', true);
		$options = array(
			'slot_duration' => get_post_meta($form_id, '_cf7rl_booking_slot_duration', true) ?: '30',
			'min_advance_minutes' => ($min_advance_raw !== '' && $min_advance_raw !== false) ? intval($min_advance_raw) : 1440, // Default 24 hours = 1440 minutes
			'max_advance_days' => get_post_meta($form_id, '_cf7rl_booking_max_advance', true) ?: '30',
			'availability' => get_post_meta($form_id, '_cf7rl_booking_availability', true) ?: array(),
			'unavailable_dates' => get_post_meta($form_id, '_cf7rl_booking_unavailable_dates', true) ?: array(),
			'date_format' => get_post_meta($form_id, '_cf7rl_booking_date_format', true) ?: 'Y-m-d',
			'time_format' => get_post_meta($form_id, '_cf7rl_booking_time_format', true) ?: 'H:i',
		);
	} else {
		// Get global default settings
		$global_options = get_option('cf7rl_options');
		$min_advance_global = isset($global_options['booking_min_advance']) ? $global_options['booking_min_advance'] : null;
		$options = array(
			'slot_duration' => isset($global_options['booking_slot_duration']) ? $global_options['booking_slot_duration'] : '30',
			'min_advance_minutes' => ($min_advance_global !== '' && $min_advance_global !== null) ? intval($min_advance_global) : 1440,
			'max_advance_days' => isset($global_options['booking_max_advance']) ? $global_options['booking_max_advance'] : '30',
			'date_format' => isset($global_options['booking_date_format']) ? $global_options['booking_date_format'] : 'Y-m-d',
			'time_format' => isset($global_options['booking_time_format']) ? $global_options['booking_time_format'] : 'H:i',
		);
	}
	
	return $options;
}

/**
 * Get available time slots for a specific date and form
 */
function cf7rl_get_available_slots($date, $form_id) {
	$options = cf7rl_get_booking_options($form_id);
	$availability = isset($options['availability']) ? $options['availability'] : array();
	$unavailable_dates = isset($options['unavailable_dates']) ? $options['unavailable_dates'] : array();
	
	// If no availability is set, use defaults
	if (empty($availability)) {
		$availability = cf7rl_get_default_availability();
	}
	
	// Check if date is in unavailable dates
	if (!empty($unavailable_dates) && in_array($date, $unavailable_dates)) {
		return array();
	}
	
	// Get day of week (0 = Sunday, 6 = Saturday)
	$day_of_week = date('w', strtotime($date));
	
	// Check if this day has availability set
	if (empty($availability[$day_of_week])) {
		return array();
	}
	
	// Check if day is enabled
	if (!isset($availability[$day_of_week]['enabled']) || !$availability[$day_of_week]['enabled']) {
		return array();
	}
	
	$day_availability = $availability[$day_of_week];
	$slots = array();
	$slot_duration = intval($options['slot_duration']);
	
	// Default to 30 minutes if not set
	if (empty($slot_duration)) {
		$slot_duration = 30;
	}
	
	// Generate time slots
	$start_time = strtotime($day_availability['start']);
	$end_time = strtotime($day_availability['end']);
	$slot_duration_seconds = $slot_duration * 60;
	
	$current_time = $start_time;
	while (($current_time + $slot_duration_seconds) <= $end_time) {
		$slot_time = date('H:i', $current_time);
		
		// Check if slot is already booked
		if (!cf7rl_is_slot_booked($date, $slot_time, $form_id)) {
			$slots[] = $slot_time;
		}
		
		$current_time += $slot_duration_seconds;
	}
	
	return $slots;
}

/**
 * Check if a specific time slot is already booked
 */
function cf7rl_is_slot_booked($date, $time, $form_id) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_bookings';
	
	$booking = $wpdb->get_row($wpdb->prepare(
		"SELECT id FROM $table_name WHERE form_id = %d AND booking_date = %s AND booking_time = %s AND status = 'confirmed'",
		$form_id,
		$date,
		$time
	));
	
	return !empty($booking);
}

/**
 * Save a booking
 */
function cf7rl_save_booking($form_id, $date, $time, $submission_data) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_bookings';
	
	$wpdb->insert(
		$table_name,
		array(
			'form_id' => $form_id,
			'booking_date' => $date,
			'booking_time' => $time,
			'submission_data' => maybe_serialize($submission_data),
			'status' => 'confirmed',
			'created_at' => current_time('mysql')
		),
		array('%d', '%s', '%s', '%s', '%s', '%s')
	);
	
	return $wpdb->insert_id;
}

/**
 * Get default availability structure
 */
function cf7rl_get_default_availability() {
	$days = array(
		0 => 'Sunday',
		1 => 'Monday',
		2 => 'Tuesday',
		3 => 'Wednesday',
		4 => 'Thursday',
		5 => 'Friday',
		6 => 'Saturday'
	);
	
	$availability = array();
	foreach ($days as $day_num => $day_name) {
		$availability[$day_num] = array(
			'enabled' => ($day_num >= 1 && $day_num <= 5), // Monday-Friday enabled by default
			'start' => '09:00',
			'end' => '17:00'
		);
	}
	
	return $availability;
}

/**
 * Get time format setting (12 or 24 hour)
 */
function cf7rl_get_booking_time_format() {
	$options = get_option('cf7rl_options');
	return isset($options['booking_time_format']) ? $options['booking_time_format'] : '24';
}

/**
 * Get booking field widths
 */
function cf7rl_get_booking_field_widths() {
	$options = get_option('cf7rl_options');
	return array(
		'date' => isset($options['booking_date_width']) ? $options['booking_date_width'] : '200',
		'time' => isset($options['booking_time_width']) ? $options['booking_time_width'] : '200'
	);
}
