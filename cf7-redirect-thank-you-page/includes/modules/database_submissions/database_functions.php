<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Create database table for form submissions
 */
function cf7rl_db_create_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		form_id bigint(20) NOT NULL,
		form_title varchar(255) NOT NULL,
		submission_data longtext NOT NULL,
		submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (id),
		KEY form_id (form_id),
		KEY submission_date (submission_date)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

/**
 * Initialize the database table when module is enabled
 */
function cf7rl_db_init() {
	// Check if module is enabled
	if ( ! cf7rl_is_module_enabled( 'database_submissions' ) ) {
		return;
	}
	
	// Check if table exists, if not create it
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		cf7rl_db_create_table();
	}
}
add_action( 'admin_init', 'cf7rl_db_init' );

/**
 * Hook into CF7 submission to save data
 */
function cf7rl_db_save_submission( $contact_form ) {
	// Check if module is enabled
	if ( ! cf7rl_is_module_enabled( 'database_submissions' ) ) {
		return;
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	$submission = WPCF7_Submission::get_instance();
	
	if ( ! $submission ) {
		return;
	}
	
	$posted_data = $submission->get_posted_data();
	$form_id = $contact_form->id();
	$form_title = $contact_form->title();
	
	// Remove fields we don't want to save
	$exclude_fields = array( 'g-recaptcha-response', '_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag', '_wpcf7_container_post' );
	foreach ( $exclude_fields as $field ) {
		unset( $posted_data[$field] );
	}
	
	// Serialize the submission data
	$submission_data = maybe_serialize( $posted_data );
	
	// Insert into database
	$wpdb->insert(
		$table_name,
		array(
			'form_id' => $form_id,
			'form_title' => $form_title,
			'submission_data' => $submission_data,
			'submission_date' => current_time( 'mysql' )
		),
		array( '%d', '%s', '%s', '%s' )
	);
}
add_action( 'wpcf7_before_send_mail', 'cf7rl_db_save_submission' );

/**
 * Get all forms with submission counts
 */
function cf7rl_db_get_forms_with_counts() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	$results = $wpdb->get_results( "
		SELECT form_id, form_title, COUNT(*) as submission_count
		FROM $table_name
		GROUP BY form_id, form_title
		ORDER BY form_title ASC
	" );
	
	return $results;
}

/**
 * Get submissions for a specific form
 */
function cf7rl_db_get_submissions( $form_id, $limit = 10, $offset = 0 ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	$results = $wpdb->get_results( $wpdb->prepare( "
		SELECT id, submission_data, submission_date
		FROM $table_name
		WHERE form_id = %d
		ORDER BY id DESC
		LIMIT %d OFFSET %d
	", $form_id, $limit, $offset ) );
	
	// Unserialize the data
	foreach ( $results as $result ) {
		$result->submission_data = maybe_unserialize( $result->submission_data );
	}
	
	return $results;
}

/**
 * Delete a submission
 */
function cf7rl_db_delete_submission( $submission_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	return $wpdb->delete(
		$table_name,
		array( 'id' => $submission_id ),
		array( '%d' )
	);
}

/**
 * Get total submission count for a form
 */
function cf7rl_db_get_submission_count( $form_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	$count = $wpdb->get_var( $wpdb->prepare( "
		SELECT COUNT(*)
		FROM $table_name
		WHERE form_id = %d
	", $form_id ) );
	
	return intval( $count );
}

/**
 * Export submissions to CSV
 */
function cf7rl_db_export_csv( $form_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	// Determine export type (page or all)
	$export_type = isset( $_GET['export_type'] ) ? sanitize_text_field( $_GET['export_type'] ) : 'all';
	
	// Build query based on export type
	if ( $export_type === 'page' ) {
		// Get per_page from user's screen options or default
		$user_id = get_current_user_id();
		$per_page = get_user_meta( $user_id, 'cf7rl_submissions_per_page', true );
		if ( empty( $per_page ) ) {
			$per_page = 10;
		}
		
		$current_page = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
		$offset = ( $current_page - 1 ) * $per_page;
		
		// Get only current page results
		$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT id, submission_data, submission_date
			FROM $table_name
			WHERE form_id = %d
			ORDER BY submission_date DESC
			LIMIT %d OFFSET %d
		", $form_id, $per_page, $offset ) );
		
		$filename_suffix = '-page-' . $current_page;
	} else {
		// Get all results
		$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT id, submission_data, submission_date
			FROM $table_name
			WHERE form_id = %d
			ORDER BY submission_date DESC
		", $form_id ) );
		
		$filename_suffix = '-all';
	}
	
	if ( empty( $results ) ) {
		return false;
	}
	
	// Get all unique field names
	$all_fields = array();
	foreach ( $results as $result ) {
		$data = maybe_unserialize( $result->submission_data );
		if ( is_array( $data ) ) {
			$all_fields = array_merge( $all_fields, array_keys( $data ) );
		}
	}
	$all_fields = array_unique( $all_fields );
	
	// Clean all output buffers to prevent HTML/errors from being included
	while ( ob_get_level() > 0 ) {
		ob_end_clean();
	}
	
	// Set headers for CSV download
	$form_title = $wpdb->get_var( $wpdb->prepare( "SELECT form_title FROM $table_name WHERE form_id = %d LIMIT 1", $form_id ) );
	$filename = sanitize_file_name( $form_title . $filename_suffix . '-' . date( 'Y-m-d' ) . '.csv' );
	
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );
	
	$output = fopen( 'php://output', 'w' );
	
	// Write header row
	$header = array_merge( array( 'Submission ID', 'Date' ), $all_fields );
	fputcsv( $output, $header );
	
	// Write data rows
	foreach ( $results as $result ) {
		$data = maybe_unserialize( $result->submission_data );
		$row = array( $result->id, $result->submission_date );
		
		foreach ( $all_fields as $field ) {
			if ( isset( $data[$field] ) ) {
				$value = $data[$field];
				
				// Handle array values - convert to comma-separated string
				if ( is_array( $value ) ) {
					$value = implode( ', ', $value );
				}
				
				// Strip any HTML tags
				$value = strip_tags( $value );
				
				$row[] = $value;
			} else {
				$row[] = '';
			}
		}
		
		fputcsv( $output, $row );
	}
	
	fclose( $output );
	exit;
}
