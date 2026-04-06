<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register booking form tags
 */
add_action('wpcf7_init', 'cf7rl_add_booking_form_tags', 10);
function cf7rl_add_booking_form_tags() {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('bookings')) {
		return;
	}
	
	// Register booking date picker tag
	wpcf7_add_form_tag(
		array('bookingdate', 'bookingdate*'),
		'cf7rl_bookingdate_form_tag_handler',
		array('name-attr' => true)
	);
	
	// Register booking time selector tag
	wpcf7_add_form_tag(
		array('bookingtime', 'bookingtime*'),
		'cf7rl_bookingtime_form_tag_handler',
		array('name-attr' => true)
	);
}

/**
 * Handler for bookingdate form tag
 */
function cf7rl_bookingdate_form_tag_handler($tag) {
	if (empty($tag->name)) {
		return '';
	}
	
	// Get form ID from the contact form object
	$contact_form = WPCF7_ContactForm::get_current();
	$form_id = $contact_form ? $contact_form->id() : 0;
	
	$options = cf7rl_get_booking_options($form_id);
	$validation_error = wpcf7_get_validation_error($tag->name);
	
	$class = wpcf7_form_controls_class($tag->type, 'wpcf7-text wpcf7-bookingdate');
	if ($validation_error) {
		$class .= ' wpcf7-not-valid';
	}
	
	$atts = array(
		'class' => $tag->get_class_option($class),
		'id' => $tag->get_id_option(),
		'name' => $tag->name,
		'type' => 'text',
		'placeholder' => __('Select a date', 'cf7rl'),
		'data-max-advance' => $options['max_advance_days'],
		'data-form-id' => $form_id,
	);
	
	if ($tag->is_required()) {
		$atts['aria-required'] = 'true';
	}
	
	$atts = wpcf7_format_atts($atts);
	
	$html = sprintf('<span class="wpcf7-form-control-wrap" data-name="%1$s">', sanitize_html_class($tag->name));
	$html .= sprintf('<input %s />', $atts);
	$html .= $validation_error;
	$html .= '</span>';
	
	return $html;
}

/**
 * Handler for bookingtime form tag
 */
function cf7rl_bookingtime_form_tag_handler($tag) {
	if (empty($tag->name)) {
		return '';
	}
	
	$validation_error = wpcf7_get_validation_error($tag->name);
	
	$class = wpcf7_form_controls_class($tag->type, 'wpcf7-select wpcf7-bookingtime');
	if ($validation_error) {
		$class .= ' wpcf7-not-valid';
	}
	
	$atts = array(
		'class' => $tag->get_class_option($class),
		'id' => $tag->get_id_option(),
		'name' => $tag->name,
	);
	
	if ($tag->is_required()) {
		$atts['aria-required'] = 'true';
	}
	
	$atts = wpcf7_format_atts($atts);
	
	$html = sprintf('<span class="wpcf7-form-control-wrap" data-name="%1$s">', sanitize_html_class($tag->name));
	$html .= sprintf('<select %s>', $atts);
	$html .= '<option value="">' . __('Select a time', 'cf7rl') . '</option>';
	$html .= '</select>';
	$html .= $validation_error;
	$html .= '</span>';
	
	return $html;
}

/**
 * Add tag generator buttons to CF7 form editor
 */
add_action('wpcf7_admin_init', 'cf7rl_add_tag_generator_booking', 50);
function cf7rl_add_tag_generator_booking() {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('bookings')) {
		return;
	}
	
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add('bookingdate', __('Booking Date', 'cf7rl'), 'cf7rl_tag_generator_bookingdate', array('version' => '2'));
	$tag_generator->add('bookingtime', __('Booking Time', 'cf7rl'), 'cf7rl_tag_generator_bookingtime', array('version' => '2'));
}

/**
 * Tag generator panel for booking date
 */
function cf7rl_tag_generator_bookingdate($contact_form, $args = '') {
	$args = wp_parse_args($args, array());
	?>
<header class="description-box">
	<h3><?php echo esc_html(__('Booking Date form-tag generator', 'cf7rl')); ?></h3>
	<p><?php echo esc_html(__('Generates a form-tag for a date picker field that allows users to select an appointment date.', 'cf7rl')); ?></p>
</header>

<div class="control-box">
	<fieldset>
		<legend id="tag-generator-panel-bookingdate-type-legend"><?php echo esc_html(__('Field type', 'cf7rl')); ?></legend>
		<select data-tag-part="basetype" aria-labelledby="tag-generator-panel-bookingdate-type-legend">
			<option value="bookingdate" selected="selected"><?php echo esc_html(__('Booking date', 'cf7rl')); ?></option>
		</select>
		<br />
		<label><input type="checkbox" data-tag-part="type-suffix" value="*" /> <?php echo esc_html(__('This is a required field.', 'cf7rl')); ?></label>
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-bookingdate-name-legend"><?php echo esc_html(__('Field name', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="name" value="booking-date-1" pattern="[A-Za-z][A-Za-z0-9_\-]*" aria-labelledby="tag-generator-panel-bookingdate-name-legend" />
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-bookingdate-class-legend"><?php echo esc_html(__('Class attribute', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="option" data-tag-option="class:" pattern="[A-Za-z0-9_\-\s]*" aria-labelledby="tag-generator-panel-bookingdate-class-legend" />
	</fieldset>
</div>

<footer class="insert-box">
	<div class="flex-container">
		<input type="text" class="code selectable" readonly="readonly" data-tag-part="tag" aria-label="<?php echo esc_attr(__('The form-tag to be inserted into the form template', 'cf7rl')); ?>" />
		<button type="button" class="button button-primary" data-taggen="insert-tag"><?php echo esc_html(__('Insert Tag', 'cf7rl')); ?></button>
	</div>
	<p class="mail-tag-tip"><?php echo sprintf(esc_html(__('To use the user input in the email, insert the corresponding mail-tag %s into the email template.', 'cf7rl')), '<strong data-tag-part="mail-tag"></strong>'); ?></p>
</footer>
<?php
}

/**
 * Tag generator panel for booking time
 */
function cf7rl_tag_generator_bookingtime($contact_form, $args = '') {
	$args = wp_parse_args($args, array());
	?>
<header class="description-box">
	<h3><?php echo esc_html(__('Booking Time form-tag generator', 'cf7rl')); ?></h3>
	<p><?php echo esc_html(__('Generates a form-tag for a time selector field that displays available appointment times based on the selected date.', 'cf7rl')); ?></p>
</header>

<div class="control-box">
	<fieldset>
		<legend id="tag-generator-panel-bookingtime-type-legend"><?php echo esc_html(__('Field type', 'cf7rl')); ?></legend>
		<select data-tag-part="basetype" aria-labelledby="tag-generator-panel-bookingtime-type-legend">
			<option value="bookingtime" selected="selected"><?php echo esc_html(__('Booking time', 'cf7rl')); ?></option>
		</select>
		<br />
		<label><input type="checkbox" data-tag-part="type-suffix" value="*" /> <?php echo esc_html(__('This is a required field.', 'cf7rl')); ?></label>
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-bookingtime-name-legend"><?php echo esc_html(__('Field name', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="name" value="booking-time-1" pattern="[A-Za-z][A-Za-z0-9_\-]*" aria-labelledby="tag-generator-panel-bookingtime-name-legend" />
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-bookingtime-class-legend"><?php echo esc_html(__('Class attribute', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="option" data-tag-option="class:" pattern="[A-Za-z0-9_\-\s]*" aria-labelledby="tag-generator-panel-bookingtime-class-legend" />
	</fieldset>
</div>

<footer class="insert-box">
	<div class="flex-container">
		<input type="text" class="code selectable" readonly="readonly" data-tag-part="tag" aria-label="<?php echo esc_attr(__('The form-tag to be inserted into the form template', 'cf7rl')); ?>" />
		<button type="button" class="button button-primary" data-taggen="insert-tag"><?php echo esc_html(__('Insert Tag', 'cf7rl')); ?></button>
	</div>
	<p class="mail-tag-tip"><?php echo sprintf(esc_html(__('To use the user input in the email, insert the corresponding mail-tag %s into the email template.', 'cf7rl')), '<strong data-tag-part="mail-tag"></strong>'); ?></p>
</footer>
<?php
}

/**
 * Validation for booking date field
 */
add_filter('wpcf7_validate_bookingdate', 'cf7rl_bookingdate_validation_filter', 10, 2);
add_filter('wpcf7_validate_bookingdate*', 'cf7rl_bookingdate_validation_filter', 10, 2);
function cf7rl_bookingdate_validation_filter($result, $tag) {
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
	
	if ($tag->is_required() && empty($value)) {
		$result->invalidate($tag, wpcf7_get_message('invalid_required'));
	} elseif (!empty($value)) {
		// Use WordPress date format (same as frontend display)
		$date_format = get_option('date_format');
		
		// Validate date format using the WordPress format
		$date = DateTime::createFromFormat($date_format, $value);
		if (!$date || $date->format($date_format) !== $value) {
			$result->invalidate($tag, __('Please select a valid date.', 'cf7rl'));
		}
	}
	
	return $result;
}

/**
 * Validation for booking time field
 */
add_filter('wpcf7_validate_bookingtime', 'cf7rl_bookingtime_validation_filter', 10, 2);
add_filter('wpcf7_validate_bookingtime*', 'cf7rl_bookingtime_validation_filter', 10, 2);
function cf7rl_bookingtime_validation_filter($result, $tag) {
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
	
	if ($tag->is_required() && empty($value)) {
		$result->invalidate($tag, wpcf7_get_message('invalid_required'));
	}
	
	return $result;
}

/**
 * Save booking when form is submitted
 */
add_action('wpcf7_before_send_mail', 'cf7rl_save_booking_on_submit');
function cf7rl_save_booking_on_submit($contact_form) {
	if (!cf7rl_is_module_enabled('bookings')) {
		return;
	}
	
	$submission = WPCF7_Submission::get_instance();
	if (!$submission) {
		return;
	}
	
	$posted_data = $submission->get_posted_data();
	$form_id = $contact_form->id();
	
	// Look for booking date and time fields
	$booking_date = null;
	$booking_time = null;
	
	foreach ($posted_data as $key => $value) {
		if (strpos($key, 'booking-date') === 0 || strpos($key, 'bookingdate') === 0) {
			$booking_date = $value;
		}
		if (strpos($key, 'booking-time') === 0 || strpos($key, 'bookingtime') === 0) {
			$booking_time = $value;
		}
	}
	
	// Save booking if both date and time are present
	if ($booking_date && $booking_time) {
		// Convert date from WordPress display format to Y-m-d for database storage
		$wp_date_format = get_option('date_format');
		$date_obj = DateTime::createFromFormat($wp_date_format, $booking_date);
		if ($date_obj) {
			$booking_date = $date_obj->format('Y-m-d');
		}
		
		cf7rl_save_booking($form_id, $booking_date, $booking_time, $posted_data);
	}
}
