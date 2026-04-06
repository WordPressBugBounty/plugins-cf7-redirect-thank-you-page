<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Add Bookings tab to CF7 form editor
 */
add_filter('wpcf7_editor_panels', 'cf7rl_booking_editor_panel', 20);
function cf7rl_booking_editor_panel($panels) {
	if (!cf7rl_is_module_enabled('bookings')) {
		return $panels;
	}
	
	$new_page = array(
		'Bookings' => array(
			'title' => __('Bookings & Appointments', 'cf7rl'),
			'callback' => 'cf7rl_booking_admin_settings'
		)
	);
	
	return array_merge($panels, $new_page);
}

/**
 * Display booking settings on form edit page
 */
function cf7rl_booking_admin_settings($cf7) {
	$post_id = absint($_GET['post']);
	
	// Get current settings
	$slot_duration = get_post_meta($post_id, '_cf7rl_booking_slot_duration', true) ?: '30';
	$min_advance_raw = get_post_meta($post_id, '_cf7rl_booking_min_advance', true);
	$min_advance = ($min_advance_raw !== '' && $min_advance_raw !== false) ? $min_advance_raw : '24';
	$max_advance = get_post_meta($post_id, '_cf7rl_booking_max_advance', true) ?: '30';
	$availability = get_post_meta($post_id, '_cf7rl_booking_availability', true);
	$unavailable_dates = get_post_meta($post_id, '_cf7rl_booking_unavailable_dates', true) ?: '';
	
	// If no availability set, use defaults
	if (empty($availability)) {
		$availability = cf7rl_get_default_availability();
	}
	
	$days = array(
		0 => __('Sunday', 'cf7rl'),
		1 => __('Monday', 'cf7rl'),
		2 => __('Tuesday', 'cf7rl'),
		3 => __('Wednesday', 'cf7rl'),
		4 => __('Thursday', 'cf7rl'),
		5 => __('Friday', 'cf7rl'),
		6 => __('Saturday', 'cf7rl')
	);
	
	?>
	<h2><?php _e('Booking & Appointment Settings', 'cf7rl'); ?></h2>
	
	<div class="mail-field"></div>
	
	<table class="cf7rl-booking-settings">
		<tr>
			<td colspan="3">
				<h3 style="margin-top:0;"><?php _e('General Settings', 'cf7rl'); ?></h3>
			</td>
		</tr>
		
		<tr>
			<td style="width: 200px;">
				<label><?php _e('Time Slot Duration:', 'cf7rl'); ?></label>
			</td>
			<td>
				<select name="cf7rl_booking_slot_duration">
					<option value="15" <?php selected($slot_duration, '15'); ?>>15 <?php _e('minutes', 'cf7rl'); ?></option>
					<option value="30" <?php selected($slot_duration, '30'); ?>>30 <?php _e('minutes', 'cf7rl'); ?></option>
					<option value="45" <?php selected($slot_duration, '45'); ?>>45 <?php _e('minutes', 'cf7rl'); ?></option>
					<option value="60" <?php selected($slot_duration, '60'); ?>>1 <?php _e('hour', 'cf7rl'); ?></option>
					<option value="90" <?php selected($slot_duration, '90'); ?>>1.5 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="120" <?php selected($slot_duration, '120'); ?>>2 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="150" <?php selected($slot_duration, '150'); ?>>2.5 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="180" <?php selected($slot_duration, '180'); ?>>3 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="210" <?php selected($slot_duration, '210'); ?>>3.5 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="240" <?php selected($slot_duration, '240'); ?>>4 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="300" <?php selected($slot_duration, '300'); ?>>5 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="360" <?php selected($slot_duration, '360'); ?>>6 <?php _e('hours', 'cf7rl'); ?></option>
					<option value="480" <?php selected($slot_duration, '480'); ?>>8 <?php _e('hours', 'cf7rl'); ?></option>
				</select>
			</td>
			<td>
				<span class="description"><?php _e('Duration of each appointment slot', 'cf7rl'); ?></span>
			</td>
		</tr>
		
		<tr>
			<td>
				<label><?php _e('Minimum Advance Notice:', 'cf7rl'); ?></label>
			</td>
			<td>
				<?php 
				// Convert stored minutes to hours and minutes for display
				$total_minutes = intval($min_advance);
				$display_hours = floor($total_minutes / 60);
				$display_minutes = $total_minutes % 60;
				?>
				<input type="number" name="cf7rl_booking_min_advance_hours" value="<?php echo esc_attr($display_hours); ?>" min="0" step="1" style="width: 70px;" /> <?php _e('hours', 'cf7rl'); ?>
				<input type="number" name="cf7rl_booking_min_advance_minutes" value="<?php echo esc_attr($display_minutes); ?>" min="0" max="59" step="1" style="width: 70px;" /> <?php _e('minutes', 'cf7rl'); ?>
			</td>
			<td>
				<span class="description"><?php _e('Minimum time in advance required to book', 'cf7rl'); ?></span>
			</td>
		</tr>
		
		<tr>
			<td>
				<label><?php _e('Maximum Advance Booking:', 'cf7rl'); ?></label>
			</td>
			<td>
				<input type="number" name="cf7rl_booking_max_advance" value="<?php echo esc_attr($max_advance); ?>" min="1" step="1" /> <?php _e('days', 'cf7rl'); ?>
			</td>
			<td>
				<span class="description"><?php _e('Maximum days in advance users can book', 'cf7rl'); ?></span>
			</td>
		</tr>
		
		<tr>
			<td colspan="3">
				<br /><hr><br />
				<h3><?php _e('Weekly Availability', 'cf7rl'); ?></h3>
				<p class="description"><?php _e('Set your available hours for each day of the week', 'cf7rl'); ?></p>
			</td>
		</tr>
		
		<?php foreach ($days as $day_num => $day_name): 
			$enabled = isset($availability[$day_num]['enabled']) ? $availability[$day_num]['enabled'] : false;
			$start = isset($availability[$day_num]['start']) ? $availability[$day_num]['start'] : '09:00';
			$end = isset($availability[$day_num]['end']) ? $availability[$day_num]['end'] : '17:00';
		?>
		<tr class="cf7rl-availability-row">
			<td>
				<label>
					<input type="checkbox" name="cf7rl_booking_availability[<?php echo $day_num; ?>][enabled]" value="1" <?php checked($enabled, true); ?> />
					<strong><?php echo esc_html($day_name); ?></strong>
				</label>
			</td>
			<td>
				<input type="time" name="cf7rl_booking_availability[<?php echo $day_num; ?>][start]" value="<?php echo esc_attr($start); ?>" />
				<?php _e('to', 'cf7rl'); ?>
				<input type="time" name="cf7rl_booking_availability[<?php echo $day_num; ?>][end]" value="<?php echo esc_attr($end); ?>" />
			</td>
			<td></td>
		</tr>
		<?php endforeach; ?>
		
		<tr>
			<td colspan="3">
				<br /><hr><br />
				<h3><?php _e('Unavailable Dates', 'cf7rl'); ?></h3>
				<p class="description"><?php _e('Enter specific dates when appointments are not available (one per line, format: YYYY-MM-DD)', 'cf7rl'); ?></p>
			</td>
		</tr>
		
		<tr>
			<td colspan="3">
				<textarea name="cf7rl_booking_unavailable_dates" rows="5" cols="50" placeholder="2025-12-25&#10;2025-12-31&#10;2026-01-01"><?php 
					if (is_array($unavailable_dates)) {
						echo esc_textarea(implode("\n", $unavailable_dates));
					} else {
						echo esc_textarea($unavailable_dates);
					}
				?></textarea>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="cf7rl_booking_post" value="<?php echo esc_attr($post_id); ?>" />
	<?php
}

/**
 * Save booking settings when form is saved
 */
add_action('wpcf7_after_save', 'cf7rl_save_booking_settings');
function cf7rl_save_booking_settings($cf7) {
	if (!cf7rl_is_module_enabled('bookings')) {
		return;
	}
	
	if (empty($_POST['cf7rl_booking_post'])) {
		return;
	}
	
	$post_id = absint($_POST['cf7rl_booking_post']);
	
	// Save slot duration
	if (isset($_POST['cf7rl_booking_slot_duration'])) {
		$slot_duration = sanitize_text_field($_POST['cf7rl_booking_slot_duration']);
		update_post_meta($post_id, '_cf7rl_booking_slot_duration', $slot_duration);
	}
	
	// Save minimum advance notice (convert hours + minutes to total minutes)
	if (isset($_POST['cf7rl_booking_min_advance_hours']) || isset($_POST['cf7rl_booking_min_advance_minutes'])) {
		$hours = isset($_POST['cf7rl_booking_min_advance_hours']) ? absint($_POST['cf7rl_booking_min_advance_hours']) : 0;
		$minutes = isset($_POST['cf7rl_booking_min_advance_minutes']) ? absint($_POST['cf7rl_booking_min_advance_minutes']) : 0;
		$total_minutes = ($hours * 60) + $minutes;
		update_post_meta($post_id, '_cf7rl_booking_min_advance', $total_minutes);
	}
	
	// Save maximum advance booking
	if (isset($_POST['cf7rl_booking_max_advance'])) {
		$max_advance = absint($_POST['cf7rl_booking_max_advance']);
		update_post_meta($post_id, '_cf7rl_booking_max_advance', $max_advance);
	}
	
	// Save availability
	if (isset($_POST['cf7rl_booking_availability'])) {
		$availability = array();
		foreach ($_POST['cf7rl_booking_availability'] as $day => $settings) {
			$availability[$day] = array(
				'enabled' => isset($settings['enabled']) && $settings['enabled'] == '1',
				'start' => sanitize_text_field($settings['start']),
				'end' => sanitize_text_field($settings['end'])
			);
		}
		update_post_meta($post_id, '_cf7rl_booking_availability', $availability);
	}
	
	// Save unavailable dates
	if (isset($_POST['cf7rl_booking_unavailable_dates'])) {
		$unavailable_text = sanitize_textarea_field($_POST['cf7rl_booking_unavailable_dates']);
		$unavailable_dates = array_filter(array_map('trim', explode("\n", $unavailable_text)));
		update_post_meta($post_id, '_cf7rl_booking_unavailable_dates', $unavailable_dates);
	}
}
