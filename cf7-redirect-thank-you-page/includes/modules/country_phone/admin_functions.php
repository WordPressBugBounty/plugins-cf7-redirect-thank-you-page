<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register Country and Phone Fields form tags
 */
add_action('wpcf7_init', 'cf7rl_add_country_phone_form_tags', 10);
function cf7rl_add_country_phone_form_tags() {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('country_phone')) {
		return;
	}
	
	// Register country select tag (with required variant)
	wpcf7_add_form_tag(
		array('countryselect', 'countryselect*'),
		'cf7rl_countryselect_form_tag_handler',
		array('name-attr' => true)
	);
	
	// Register tel text tag (with required variant)
	wpcf7_add_form_tag(
		array('teltext', 'teltext*'),
		'cf7rl_teltext_form_tag_handler',
		array('name-attr' => true)
	);
}

/**
 * Handler for countryselect form tag
 */
function cf7rl_countryselect_form_tag_handler($tag) {
	if (empty($tag->name)) {
		return '';
	}
	
	$options = cf7rl_get_country_phone_options();
	$all_countries = cf7rl_get_countries_list();
	
	// Filter countries based on settings
	$countries = cf7rl_filter_countries(
		$all_countries,
		$options['country_include'],
		$options['country_exclude'],
		$options['country_preferred']
	);
	
	// Get default country
	$default_country = $options['country_default'];
	
	// Check if flags should be shown
	$show_flags = (!isset($options['country_show_flags']) || $options['country_show_flags'] == '1');
	
	$validation_error = wpcf7_get_validation_error($tag->name);
	
	$class = wpcf7_form_controls_class($tag->type, 'wpcf7-countryselect');
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
	
	if ($show_flags) {
		$atts['data-show-flags'] = 'true';
	}
	
	$atts = wpcf7_format_atts($atts);
	
	$html = sprintf('<span class="wpcf7-form-control-wrap" data-name="%1$s">', sanitize_html_class($tag->name));
	$html .= sprintf('<select %s>', $atts);
	
	// Get custom label or use default
	$country_label = !empty($options['country_label']) ? esc_html($options['country_label']) : __('Country', 'cf7rl');
	$html .= sprintf('<option value="">%s</option>', $country_label);
	
	foreach ($countries as $code => $country) {
		$selected = ($code === $default_country) ? ' selected' : '';
		if ($show_flags) {
			$flag = cf7rl_get_country_flag($code);
			$display_name = $flag . ' ' . esc_html($country['name']);
		} else {
			$display_name = esc_html($country['name']);
		}
		$html .= sprintf(
			'<option value="%s"%s>%s</option>',
			esc_attr($country['name']),
			$selected,
			$display_name
		);
	}
	
	$html .= '</select>';
	$html .= $validation_error;
	$html .= '</span>';
	
	return $html;
}

/**
 * Handler for teltext form tag
 */
function cf7rl_teltext_form_tag_handler($tag) {
	if (empty($tag->name)) {
		return '';
	}
	
	$options = cf7rl_get_country_phone_options();
	$all_countries = cf7rl_get_countries_list();
	
	// Filter countries based on settings
	$countries = cf7rl_filter_countries(
		$all_countries,
		$options['phone_include'],
		$options['phone_exclude'],
		$options['phone_preferred']
	);
	
	// Get default country
	$default_country = $options['phone_default'];
	
	// Check if flags should be shown
	$show_flags = (!isset($options['phone_show_flags']) || $options['phone_show_flags'] == '1');
	
	$validation_error = wpcf7_get_validation_error($tag->name);
	
	$class = wpcf7_form_controls_class($tag->type, 'wpcf7-phonetext');
	if ($validation_error) {
		$class .= ' wpcf7-not-valid';
	}
	
	$atts = array(
		'class' => $tag->get_class_option($class),
		'id' => $tag->get_id_option(),
		'name' => $tag->name,
		'type' => 'tel',
		'size' => $tag->get_size_option('40'),
	);
	
	if ($tag->is_required()) {
		$atts['aria-required'] = 'true';
	}
	
	if ($validation_error) {
		$atts['aria-invalid'] = 'true';
	}
	
	if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
		$atts['placeholder'] = $tag->get_option('placeholder', 'watermark', true);
	}
	
	if ($show_flags) {
		$atts['data-show-flags'] = 'true';
	}
	
	$value = (string) reset($tag->values);
	if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
		$value = '';
	}
	
	$atts['value'] = $value;
	
	$atts = wpcf7_format_atts($atts);
	
	// Build dial code dropdown
	$dial_atts = array(
		'class' => 'wpcf7-form-control wpcf7-tel-dialcode',
		'name' => $tag->name . '_dialcode',
		'id' => $tag->get_id_option() . '_dialcode',
	);
	
	if ($show_flags) {
		$dial_atts['data-show-flags'] = 'true';
	}
	
	$dial_atts = wpcf7_format_atts($dial_atts);
	
	// Get custom label or use default
	$phone_label = !empty($options['phone_label']) ? esc_html($options['phone_label']) : __('Code', 'cf7rl');
	
	$dial_options = sprintf('<option value="">%s</option>', $phone_label);
	foreach ($countries as $code => $country) {
		$selected = ($code === $default_country) ? ' selected' : '';
		if ($show_flags) {
			$flag = cf7rl_get_country_flag($code);
			$display_name = $flag . ' ' . esc_html($country['dial_code']) . ' ' . esc_html($country['name']);
		} else {
			$display_name = esc_html($country['dial_code']) . ' ' . esc_html($country['name']);
		}
		$dial_options .= sprintf(
			'<option value="%s"%s>%s</option>',
			esc_attr($country['name']),
			$selected,
			$display_name
		);
	}
	
	// Build HTML - phone group wrapper contains everything including validation error
	$html = '<span class="wpcf7-form-control-wrap cf7rl-phone-wrap" data-name="' . sanitize_html_class($tag->name) . '">';
	$html .= '<span class="cf7rl-phone-group">';
	$html .= '<select ' . $dial_atts . '>' . $dial_options . '</select>';
	$html .= '<input ' . $atts . ' />';
	$html .= '</span>';
	$html .= $validation_error;
	$html .= '</span>';
	
	return $html;
}

/**
 * Add tag generator buttons to CF7 form editor
 */
add_action('wpcf7_admin_init', 'cf7rl_add_tag_generator_country_phone', 50);
function cf7rl_add_tag_generator_country_phone() {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('country_phone')) {
		return;
	}
	
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add('countryselect', __('Country Select', 'cf7rl'), 'cf7rl_tag_generator_countryselect', array('version' => '2'));
	$tag_generator->add('teltext', __('Phone Field', 'cf7rl'), 'cf7rl_tag_generator_teltext', array('version' => '2'));
}

/**
 * Tag generator panel for country select
 */
function cf7rl_tag_generator_countryselect($contact_form, $args = '') {
	$args = wp_parse_args($args, array());
	?>
<header class="description-box">
	<h3><?php echo esc_html(__('Country Select form-tag generator', 'cf7rl')); ?></h3>
	<p><?php echo esc_html(__('Generates a form-tag for a country dropdown field with searchable country selection.', 'cf7rl')); ?></p>
</header>

<div class="control-box">
	<fieldset>
		<legend id="tag-generator-panel-countryselect-type-legend"><?php echo esc_html(__('Field type', 'cf7rl')); ?></legend>
		<select data-tag-part="basetype" aria-labelledby="tag-generator-panel-countryselect-type-legend">
			<option value="countryselect" selected="selected"><?php echo esc_html(__('Country dropdown', 'cf7rl')); ?></option>
		</select>
		<br />
		<label><input type="checkbox" data-tag-part="type-suffix" value="*" /> <?php echo esc_html(__('This is a required field.', 'cf7rl')); ?></label>
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-countryselect-name-legend"><?php echo esc_html(__('Field name', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="name" value="country-1" pattern="[A-Za-z][A-Za-z0-9_\-]*" aria-labelledby="tag-generator-panel-countryselect-name-legend" />
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-countryselect-class-legend"><?php echo esc_html(__('Class attribute', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="option" data-tag-option="class:" pattern="[A-Za-z0-9_\-\s]*" aria-labelledby="tag-generator-panel-countryselect-class-legend" />
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
 * Tag generator panel for phone field
 */
function cf7rl_tag_generator_teltext($contact_form, $args = '') {
	$args = wp_parse_args($args, array());
	?>
<header class="description-box">
	<h3><?php echo esc_html(__('Phone Field form-tag generator', 'cf7rl')); ?></h3>
	<p><?php echo esc_html(__('Generates a form-tag for an international phone number field with country dial code selector.', 'cf7rl')); ?></p>
</header>

<div class="control-box">
	<fieldset>
		<legend id="tag-generator-panel-teltext-type-legend"><?php echo esc_html(__('Field type', 'cf7rl')); ?></legend>
		<select data-tag-part="basetype" aria-labelledby="tag-generator-panel-teltext-type-legend">
			<option value="teltext" selected="selected"><?php echo esc_html(__('Phone field', 'cf7rl')); ?></option>
		</select>
		<br />
		<label><input type="checkbox" data-tag-part="type-suffix" value="*" /> <?php echo esc_html(__('This is a required field.', 'cf7rl')); ?></label>
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-teltext-name-legend"><?php echo esc_html(__('Field name', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="name" value="phone-1" pattern="[A-Za-z][A-Za-z0-9_\-]*" aria-labelledby="tag-generator-panel-teltext-name-legend" />
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-teltext-class-legend"><?php echo esc_html(__('Class attribute', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="option" data-tag-option="class:" pattern="[A-Za-z0-9_\-\s]*" aria-labelledby="tag-generator-panel-teltext-class-legend" />
	</fieldset>

	<fieldset>
		<legend id="tag-generator-panel-teltext-value-legend"><?php echo esc_html(__('Default value', 'cf7rl')); ?></legend>
		<input type="text" data-tag-part="content" aria-labelledby="tag-generator-panel-teltext-value-legend" /><br />
		<label><input type="checkbox" data-tag-part="option" data-tag-option="placeholder" /> <?php echo esc_html(__('Use this text as the placeholder.', 'cf7rl')); ?></label>
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
 * Validation for country select field
 */
add_filter('wpcf7_validate_countryselect', 'cf7rl_countryselect_validation_filter', 10, 2);
add_filter('wpcf7_validate_countryselect*', 'cf7rl_countryselect_validation_filter', 10, 2);
function cf7rl_countryselect_validation_filter($result, $tag) {
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
	
	if ($tag->is_required() && empty($value)) {
		$result->invalidate($tag, wpcf7_get_message('invalid_required'));
	}
	
	return $result;
}

/**
 * Validation for phone field
 */
add_filter('wpcf7_validate_teltext', 'cf7rl_teltext_validation_filter', 10, 2);
add_filter('wpcf7_validate_teltext*', 'cf7rl_teltext_validation_filter', 10, 2);
function cf7rl_teltext_validation_filter($result, $tag) {
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
	
	if ($tag->is_required() && empty($value)) {
		$result->invalidate($tag, wpcf7_get_message('invalid_required'));
	}
	
	return $result;
}
