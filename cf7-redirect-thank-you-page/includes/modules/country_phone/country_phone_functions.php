<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get country phone options
 */
function cf7rl_get_country_phone_options() {
	$options = get_option('cf7rl_options');
	if (!is_array($options)) {
		$options = array();
	}
	
	// Set defaults for country field
	if (empty($options['country_default'])) { 				$options['country_default'] = ''; }
	if (empty($options['country_include'])) { 				$options['country_include'] = array(); }
	if (empty($options['country_exclude'])) { 				$options['country_exclude'] = array(); }
	if (empty($options['country_preferred'])) { 			$options['country_preferred'] = array(); }
	if (!isset($options['country_show_flags'])) { 			$options['country_show_flags'] = '1'; }
	if (empty($options['country_label'])) { 				$options['country_label'] = ''; }
	
	// Set defaults for phone field
	if (empty($options['phone_default'])) { 				$options['phone_default'] = ''; }
	if (empty($options['phone_include'])) { 				$options['phone_include'] = array(); }
	if (empty($options['phone_exclude'])) { 				$options['phone_exclude'] = array(); }
	if (empty($options['phone_preferred'])) { 				$options['phone_preferred'] = array(); }
	if (!isset($options['phone_show_flags'])) { 			$options['phone_show_flags'] = '1'; }
	if (empty($options['phone_label'])) { 					$options['phone_label'] = ''; }
	
	// Set defaults for width customization
	if (empty($options['country_dropdown_width'])) { 		$options['country_dropdown_width'] = '200'; }
	if (empty($options['phone_dropdown_width'])) { 		$options['phone_dropdown_width'] = '100'; }
	
	return $options;
}

/**
 * Get list of all countries with their codes
 */
function cf7rl_get_countries_list() {
	return array(
		'AF' => array('name' => 'Afghanistan', 'dial_code' => '+93'),
		'AL' => array('name' => 'Albania', 'dial_code' => '+355'),
		'DZ' => array('name' => 'Algeria', 'dial_code' => '+213'),
		'AS' => array('name' => 'American Samoa', 'dial_code' => '+1684'),
		'AD' => array('name' => 'Andorra', 'dial_code' => '+376'),
		'AO' => array('name' => 'Angola', 'dial_code' => '+244'),
		'AI' => array('name' => 'Anguilla', 'dial_code' => '+1264'),
		'AG' => array('name' => 'Antigua and Barbuda', 'dial_code' => '+1268'),
		'AR' => array('name' => 'Argentina', 'dial_code' => '+54'),
		'AM' => array('name' => 'Armenia', 'dial_code' => '+374'),
		'AW' => array('name' => 'Aruba', 'dial_code' => '+297'),
		'AU' => array('name' => 'Australia', 'dial_code' => '+61'),
		'AT' => array('name' => 'Austria', 'dial_code' => '+43'),
		'AZ' => array('name' => 'Azerbaijan', 'dial_code' => '+994'),
		'BS' => array('name' => 'Bahamas', 'dial_code' => '+1242'),
		'BH' => array('name' => 'Bahrain', 'dial_code' => '+973'),
		'BD' => array('name' => 'Bangladesh', 'dial_code' => '+880'),
		'BB' => array('name' => 'Barbados', 'dial_code' => '+1246'),
		'BY' => array('name' => 'Belarus', 'dial_code' => '+375'),
		'BE' => array('name' => 'Belgium', 'dial_code' => '+32'),
		'BZ' => array('name' => 'Belize', 'dial_code' => '+501'),
		'BJ' => array('name' => 'Benin', 'dial_code' => '+229'),
		'BM' => array('name' => 'Bermuda', 'dial_code' => '+1441'),
		'BT' => array('name' => 'Bhutan', 'dial_code' => '+975'),
		'BO' => array('name' => 'Bolivia', 'dial_code' => '+591'),
		'BA' => array('name' => 'Bosnia and Herzegovina', 'dial_code' => '+387'),
		'BW' => array('name' => 'Botswana', 'dial_code' => '+267'),
		'BR' => array('name' => 'Brazil', 'dial_code' => '+55'),
		'BN' => array('name' => 'Brunei', 'dial_code' => '+673'),
		'BG' => array('name' => 'Bulgaria', 'dial_code' => '+359'),
		'BF' => array('name' => 'Burkina Faso', 'dial_code' => '+226'),
		'BI' => array('name' => 'Burundi', 'dial_code' => '+257'),
		'KH' => array('name' => 'Cambodia', 'dial_code' => '+855'),
		'CM' => array('name' => 'Cameroon', 'dial_code' => '+237'),
		'CA' => array('name' => 'Canada', 'dial_code' => '+1'),
		'CV' => array('name' => 'Cape Verde', 'dial_code' => '+238'),
		'KY' => array('name' => 'Cayman Islands', 'dial_code' => '+1345'),
		'CF' => array('name' => 'Central African Republic', 'dial_code' => '+236'),
		'TD' => array('name' => 'Chad', 'dial_code' => '+235'),
		'CL' => array('name' => 'Chile', 'dial_code' => '+56'),
		'CN' => array('name' => 'China', 'dial_code' => '+86'),
		'CO' => array('name' => 'Colombia', 'dial_code' => '+57'),
		'KM' => array('name' => 'Comoros', 'dial_code' => '+269'),
		'CG' => array('name' => 'Congo', 'dial_code' => '+242'),
		'CD' => array('name' => 'Congo, Democratic Republic', 'dial_code' => '+243'),
		'CR' => array('name' => 'Costa Rica', 'dial_code' => '+506'),
		'HR' => array('name' => 'Croatia', 'dial_code' => '+385'),
		'CU' => array('name' => 'Cuba', 'dial_code' => '+53'),
		'CY' => array('name' => 'Cyprus', 'dial_code' => '+357'),
		'CZ' => array('name' => 'Czech Republic', 'dial_code' => '+420'),
		'DK' => array('name' => 'Denmark', 'dial_code' => '+45'),
		'DJ' => array('name' => 'Djibouti', 'dial_code' => '+253'),
		'DM' => array('name' => 'Dominica', 'dial_code' => '+1767'),
		'DO' => array('name' => 'Dominican Republic', 'dial_code' => '+1809'),
		'EC' => array('name' => 'Ecuador', 'dial_code' => '+593'),
		'EG' => array('name' => 'Egypt', 'dial_code' => '+20'),
		'SV' => array('name' => 'El Salvador', 'dial_code' => '+503'),
		'GQ' => array('name' => 'Equatorial Guinea', 'dial_code' => '+240'),
		'ER' => array('name' => 'Eritrea', 'dial_code' => '+291'),
		'EE' => array('name' => 'Estonia', 'dial_code' => '+372'),
		'ET' => array('name' => 'Ethiopia', 'dial_code' => '+251'),
		'FJ' => array('name' => 'Fiji', 'dial_code' => '+679'),
		'FI' => array('name' => 'Finland', 'dial_code' => '+358'),
		'FR' => array('name' => 'France', 'dial_code' => '+33'),
		'GA' => array('name' => 'Gabon', 'dial_code' => '+241'),
		'GM' => array('name' => 'Gambia', 'dial_code' => '+220'),
		'GE' => array('name' => 'Georgia', 'dial_code' => '+995'),
		'DE' => array('name' => 'Germany', 'dial_code' => '+49'),
		'GH' => array('name' => 'Ghana', 'dial_code' => '+233'),
		'GR' => array('name' => 'Greece', 'dial_code' => '+30'),
		'GD' => array('name' => 'Grenada', 'dial_code' => '+1473'),
		'GU' => array('name' => 'Guam', 'dial_code' => '+1671'),
		'GT' => array('name' => 'Guatemala', 'dial_code' => '+502'),
		'GN' => array('name' => 'Guinea', 'dial_code' => '+224'),
		'GW' => array('name' => 'Guinea-Bissau', 'dial_code' => '+245'),
		'GY' => array('name' => 'Guyana', 'dial_code' => '+592'),
		'HT' => array('name' => 'Haiti', 'dial_code' => '+509'),
		'HN' => array('name' => 'Honduras', 'dial_code' => '+504'),
		'HK' => array('name' => 'Hong Kong', 'dial_code' => '+852'),
		'HU' => array('name' => 'Hungary', 'dial_code' => '+36'),
		'IS' => array('name' => 'Iceland', 'dial_code' => '+354'),
		'IN' => array('name' => 'India', 'dial_code' => '+91'),
		'ID' => array('name' => 'Indonesia', 'dial_code' => '+62'),
		'IR' => array('name' => 'Iran', 'dial_code' => '+98'),
		'IQ' => array('name' => 'Iraq', 'dial_code' => '+964'),
		'IE' => array('name' => 'Ireland', 'dial_code' => '+353'),
		'IL' => array('name' => 'Israel', 'dial_code' => '+972'),
		'IT' => array('name' => 'Italy', 'dial_code' => '+39'),
		'JM' => array('name' => 'Jamaica', 'dial_code' => '+1876'),
		'JP' => array('name' => 'Japan', 'dial_code' => '+81'),
		'JO' => array('name' => 'Jordan', 'dial_code' => '+962'),
		'KZ' => array('name' => 'Kazakhstan', 'dial_code' => '+7'),
		'KE' => array('name' => 'Kenya', 'dial_code' => '+254'),
		'KI' => array('name' => 'Kiribati', 'dial_code' => '+686'),
		'KP' => array('name' => 'Korea, North', 'dial_code' => '+850'),
		'KR' => array('name' => 'Korea, South', 'dial_code' => '+82'),
		'KW' => array('name' => 'Kuwait', 'dial_code' => '+965'),
		'KG' => array('name' => 'Kyrgyzstan', 'dial_code' => '+996'),
		'LA' => array('name' => 'Laos', 'dial_code' => '+856'),
		'LV' => array('name' => 'Latvia', 'dial_code' => '+371'),
		'LB' => array('name' => 'Lebanon', 'dial_code' => '+961'),
		'LS' => array('name' => 'Lesotho', 'dial_code' => '+266'),
		'LR' => array('name' => 'Liberia', 'dial_code' => '+231'),
		'LY' => array('name' => 'Libya', 'dial_code' => '+218'),
		'LI' => array('name' => 'Liechtenstein', 'dial_code' => '+423'),
		'LT' => array('name' => 'Lithuania', 'dial_code' => '+370'),
		'LU' => array('name' => 'Luxembourg', 'dial_code' => '+352'),
		'MO' => array('name' => 'Macau', 'dial_code' => '+853'),
		'MK' => array('name' => 'Macedonia', 'dial_code' => '+389'),
		'MG' => array('name' => 'Madagascar', 'dial_code' => '+261'),
		'MW' => array('name' => 'Malawi', 'dial_code' => '+265'),
		'MY' => array('name' => 'Malaysia', 'dial_code' => '+60'),
		'MV' => array('name' => 'Maldives', 'dial_code' => '+960'),
		'ML' => array('name' => 'Mali', 'dial_code' => '+223'),
		'MT' => array('name' => 'Malta', 'dial_code' => '+356'),
		'MH' => array('name' => 'Marshall Islands', 'dial_code' => '+692'),
		'MR' => array('name' => 'Mauritania', 'dial_code' => '+222'),
		'MU' => array('name' => 'Mauritius', 'dial_code' => '+230'),
		'MX' => array('name' => 'Mexico', 'dial_code' => '+52'),
		'FM' => array('name' => 'Micronesia', 'dial_code' => '+691'),
		'MD' => array('name' => 'Moldova', 'dial_code' => '+373'),
		'MC' => array('name' => 'Monaco', 'dial_code' => '+377'),
		'MN' => array('name' => 'Mongolia', 'dial_code' => '+976'),
		'ME' => array('name' => 'Montenegro', 'dial_code' => '+382'),
		'MA' => array('name' => 'Morocco', 'dial_code' => '+212'),
		'MZ' => array('name' => 'Mozambique', 'dial_code' => '+258'),
		'MM' => array('name' => 'Myanmar', 'dial_code' => '+95'),
		'NA' => array('name' => 'Namibia', 'dial_code' => '+264'),
		'NR' => array('name' => 'Nauru', 'dial_code' => '+674'),
		'NP' => array('name' => 'Nepal', 'dial_code' => '+977'),
		'NL' => array('name' => 'Netherlands', 'dial_code' => '+31'),
		'NZ' => array('name' => 'New Zealand', 'dial_code' => '+64'),
		'NI' => array('name' => 'Nicaragua', 'dial_code' => '+505'),
		'NE' => array('name' => 'Niger', 'dial_code' => '+227'),
		'NG' => array('name' => 'Nigeria', 'dial_code' => '+234'),
		'NO' => array('name' => 'Norway', 'dial_code' => '+47'),
		'OM' => array('name' => 'Oman', 'dial_code' => '+968'),
		'PK' => array('name' => 'Pakistan', 'dial_code' => '+92'),
		'PW' => array('name' => 'Palau', 'dial_code' => '+680'),
		'PS' => array('name' => 'Palestine', 'dial_code' => '+970'),
		'PA' => array('name' => 'Panama', 'dial_code' => '+507'),
		'PG' => array('name' => 'Papua New Guinea', 'dial_code' => '+675'),
		'PY' => array('name' => 'Paraguay', 'dial_code' => '+595'),
		'PE' => array('name' => 'Peru', 'dial_code' => '+51'),
		'PH' => array('name' => 'Philippines', 'dial_code' => '+63'),
		'PL' => array('name' => 'Poland', 'dial_code' => '+48'),
		'PT' => array('name' => 'Portugal', 'dial_code' => '+351'),
		'PR' => array('name' => 'Puerto Rico', 'dial_code' => '+1787'),
		'QA' => array('name' => 'Qatar', 'dial_code' => '+974'),
		'RO' => array('name' => 'Romania', 'dial_code' => '+40'),
		'RU' => array('name' => 'Russia', 'dial_code' => '+7'),
		'RW' => array('name' => 'Rwanda', 'dial_code' => '+250'),
		'WS' => array('name' => 'Samoa', 'dial_code' => '+685'),
		'SM' => array('name' => 'San Marino', 'dial_code' => '+378'),
		'SA' => array('name' => 'Saudi Arabia', 'dial_code' => '+966'),
		'SN' => array('name' => 'Senegal', 'dial_code' => '+221'),
		'RS' => array('name' => 'Serbia', 'dial_code' => '+381'),
		'SC' => array('name' => 'Seychelles', 'dial_code' => '+248'),
		'SL' => array('name' => 'Sierra Leone', 'dial_code' => '+232'),
		'SG' => array('name' => 'Singapore', 'dial_code' => '+65'),
		'SK' => array('name' => 'Slovakia', 'dial_code' => '+421'),
		'SI' => array('name' => 'Slovenia', 'dial_code' => '+386'),
		'SB' => array('name' => 'Solomon Islands', 'dial_code' => '+677'),
		'SO' => array('name' => 'Somalia', 'dial_code' => '+252'),
		'ZA' => array('name' => 'South Africa', 'dial_code' => '+27'),
		'ES' => array('name' => 'Spain', 'dial_code' => '+34'),
		'LK' => array('name' => 'Sri Lanka', 'dial_code' => '+94'),
		'SD' => array('name' => 'Sudan', 'dial_code' => '+249'),
		'SR' => array('name' => 'Suriname', 'dial_code' => '+597'),
		'SZ' => array('name' => 'Swaziland', 'dial_code' => '+268'),
		'SE' => array('name' => 'Sweden', 'dial_code' => '+46'),
		'CH' => array('name' => 'Switzerland', 'dial_code' => '+41'),
		'SY' => array('name' => 'Syria', 'dial_code' => '+963'),
		'TW' => array('name' => 'Taiwan', 'dial_code' => '+886'),
		'TJ' => array('name' => 'Tajikistan', 'dial_code' => '+992'),
		'TZ' => array('name' => 'Tanzania', 'dial_code' => '+255'),
		'TH' => array('name' => 'Thailand', 'dial_code' => '+66'),
		'TG' => array('name' => 'Togo', 'dial_code' => '+228'),
		'TO' => array('name' => 'Tonga', 'dial_code' => '+676'),
		'TT' => array('name' => 'Trinidad and Tobago', 'dial_code' => '+1868'),
		'TN' => array('name' => 'Tunisia', 'dial_code' => '+216'),
		'TR' => array('name' => 'Turkey', 'dial_code' => '+90'),
		'TM' => array('name' => 'Turkmenistan', 'dial_code' => '+993'),
		'TV' => array('name' => 'Tuvalu', 'dial_code' => '+688'),
		'UG' => array('name' => 'Uganda', 'dial_code' => '+256'),
		'UA' => array('name' => 'Ukraine', 'dial_code' => '+380'),
		'AE' => array('name' => 'United Arab Emirates', 'dial_code' => '+971'),
		'GB' => array('name' => 'United Kingdom', 'dial_code' => '+44'),
		'US' => array('name' => 'United States', 'dial_code' => '+1'),
		'UY' => array('name' => 'Uruguay', 'dial_code' => '+598'),
		'UZ' => array('name' => 'Uzbekistan', 'dial_code' => '+998'),
		'VU' => array('name' => 'Vanuatu', 'dial_code' => '+678'),
		'VA' => array('name' => 'Vatican City', 'dial_code' => '+379'),
		'VE' => array('name' => 'Venezuela', 'dial_code' => '+58'),
		'VN' => array('name' => 'Vietnam', 'dial_code' => '+84'),
		'YE' => array('name' => 'Yemen', 'dial_code' => '+967'),
		'ZM' => array('name' => 'Zambia', 'dial_code' => '+260'),
		'ZW' => array('name' => 'Zimbabwe', 'dial_code' => '+263')
	);
}

/**
 * Get user's country by IP address
 */
function cf7rl_get_country_by_ip() {
	$ip = $_SERVER['REMOTE_ADDR'];
	
	// Use a free IP geolocation service
	$response = wp_remote_get("http://ip-api.com/json/{$ip}");
	
	if (is_wp_error($response)) {
		return '';
	}
	
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);
	
	if (isset($data['countryCode'])) {
		return $data['countryCode'];
	}
	
	return '';
}

/**
 * Filter countries based on settings
 */
function cf7rl_filter_countries($all_countries, $include = array(), $exclude = array(), $preferred = array()) {
	$filtered = array();
	
	// If include list is specified, only use those countries
	if (!empty($include)) {
		foreach ($include as $code) {
			if (isset($all_countries[$code])) {
				$filtered[$code] = $all_countries[$code];
			}
		}
	} else {
		$filtered = $all_countries;
	}
	
	// Remove excluded countries
	if (!empty($exclude)) {
		foreach ($exclude as $code) {
			unset($filtered[$code]);
		}
	}
	
	// Add preferred countries at the top
	if (!empty($preferred)) {
		$preferred_countries = array();
		foreach ($preferred as $code) {
			if (isset($all_countries[$code])) {
				$preferred_countries[$code] = $all_countries[$code];
			}
		}
		
		// Merge preferred at the top
		if (!empty($preferred_countries)) {
			$filtered = $preferred_countries + $filtered;
		}
	}
	
	return $filtered;
}

/**
 * Get country flag emoji by country code
 */
function cf7rl_get_country_flag($country_code) {
	$country_code = strtoupper($country_code);
	
	// Convert country code to flag emoji
	// Each letter is converted to its regional indicator symbol
	$flag = '';
	for ($i = 0; $i < strlen($country_code); $i++) {
		// Regional Indicator Symbol Letter A starts at U+1F1E6 (127462 in decimal)
		// A = 65, so we add 127462 - 65 = 127397
		$codepoint = 127397 + ord($country_code[$i]);
		
		// Use mb_chr if available, otherwise use html_entity_decode
		if (function_exists('mb_chr')) {
			$flag .= mb_chr($codepoint, 'UTF-8');
		} else {
			$flag .= html_entity_decode('&#' . $codepoint . ';', ENT_NOQUOTES, 'UTF-8');
		}
	}
	
	return $flag;
}

/**
 * Get country code by country name
 */
function cf7rl_get_country_code_by_name($country_name) {
	$all_countries = cf7rl_get_countries_list();
	
	foreach ($all_countries as $code => $country) {
		if ($country['name'] === $country_name) {
			return $code;
		}
	}
	
	return '';
}

/**
 * Get dial code by country name
 */
function cf7rl_get_dial_code_by_name($country_name) {
	$all_countries = cf7rl_get_countries_list();
	
	foreach ($all_countries as $code => $country) {
		if ($country['name'] === $country_name) {
			return $country['dial_code'];
		}
	}
	
	return '';
}

/**
 * Format phone field data to include country name and dial code
 * Hooks into form submission to modify the phone field value
 */
add_filter('wpcf7_posted_data', 'cf7rl_format_phone_field_data');
function cf7rl_format_phone_field_data($posted_data) {
	// Check if module is enabled
	if (!cf7rl_is_module_enabled('country_phone')) {
		return $posted_data;
	}
	
	// Loop through posted data to find phone fields with dialcode
	foreach ($posted_data as $key => $value) {
		// Check if this is a dialcode field
		if (strpos($key, '_dialcode') !== false) {
			// Get the base field name (remove _dialcode suffix)
			$base_field = str_replace('_dialcode', '', $key);
			
			// Check if the corresponding phone number field exists
			if (isset($posted_data[$base_field])) {
				$country_name = $value;
				$phone_number = $posted_data[$base_field];
				
				// Get the dial code for this country
				$dial_code = cf7rl_get_dial_code_by_name($country_name);
				
				// Format as "Country Name (Dial Code) Phone Number"
				if (!empty($country_name) && !empty($dial_code)) {
					$posted_data[$base_field] = $country_name . ' (' . $dial_code . ') ' . $phone_number;
				}
			}
			
			// Remove the dialcode field from posted data so it doesn't appear separately
			unset($posted_data[$key]);
		}
	}
	
	return $posted_data;
}
