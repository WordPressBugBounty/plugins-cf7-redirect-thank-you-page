<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load WP_List_Table if not already loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Forms List Table Class
 */
class CF7RL_Forms_List_Table extends WP_List_Table {
	
	public function __construct() {
		parent::__construct( array(
			'singular' => 'form',
			'plural'   => 'forms',
			'ajax'     => false
		) );
	}
	
	public function get_columns() {
		return array(
			'form_title'        => __( 'Form Name', 'cf7rl' ),
			'submission_count'  => __( 'Number of Submissions', 'cf7rl' )
		);
	}
	
	public function prepare_items() {
		$columns = $this->get_columns();
		$this->_column_headers = array( $columns, array(), array() );
		$this->items = cf7rl_db_get_forms_with_counts();
	}
	
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'form_title':
				$url = add_query_arg( 'form_id', $item->form_id );
				return '<a href="' . esc_url( $url ) . '"><strong>' . esc_html( $item->form_title ) . '</strong></a>';
			case 'submission_count':
				return esc_html( $item->submission_count );
			default:
				return '';
		}
	}
}

/**
 * Submissions List Table Class
 */
class CF7RL_Submissions_List_Table extends WP_List_Table {
	
	private $form_id;
	
	public function __construct( $form_id ) {
		$this->form_id = $form_id;
		parent::__construct( array(
			'singular' => 'submission',
			'plural'   => 'submissions',
			'ajax'     => false,
			'screen'   => 'cf7rl_submissions_' . $form_id
		) );
	}
	
	public function get_columns() {
		return array(
			'cb'                => '<input type="checkbox" />',
			'id'                => __( 'Submission ID', 'cf7rl' ),
			'submission_date'   => __( 'Date', 'cf7rl' )
		);
	}
	
	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'cf7rl' )
		);
	}
	
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="submission[]" value="%s" />', $item->id );
	}
	
	public function column_id( $item ) {
		$delete_url = wp_nonce_url(
			add_query_arg( array(
				'action' => 'delete',
				'submission' => $item->id
			) ),
			'cf7rl_delete_submission'
		);
		
		$actions = array(
			'view' => '<a href="#" class="cf7rl-db-expand-btn" data-details="details-' . esc_attr( $item->id ) . '">' . __( 'View Details', 'cf7rl' ) . '</a>',
			'delete' => '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete this submission?', 'cf7rl' ) ) . '\');">' . __( 'Delete', 'cf7rl' ) . '</a>'
		);
		
		return sprintf( '%1$s %2$s', $item->id, $this->row_actions( $actions ) );
	}
	
	public function column_submission_date( $item ) {
		return esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item->submission_date ) ) );
	}
	
	public function prepare_items() {
		// Get per_page from screen options, default to 10
		$per_page = $this->get_items_per_page( 'cf7rl_submissions_per_page', 10 );
		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		$this->process_bulk_action();
		
		$this->items = cf7rl_db_get_submissions( $this->form_id, $per_page, $offset );
		$total_items = cf7rl_db_get_submission_count( $this->form_id );
		
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}
	
	public function process_bulk_action() {
		// Bulk actions are now processed in cf7rl_db_admin_page() before any output
		// This method is kept for compatibility with WP_List_Table
		return;
	}
	
	public function single_row( $item ) {
		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';
		echo '<tr class="cf7rl-db-details-row" style="display:none;" id="details-' . esc_attr( $item->id ) . '">';
		echo '<td colspan="' . $this->get_column_count() . '">';
		echo '<div class="cf7rl-db-details">';
		
		if ( is_array( $item->submission_data ) && ! empty( $item->submission_data ) ) {
			echo '<table class="widefat">';
			foreach ( $item->submission_data as $key => $value ) {
				echo '<tr>';
				echo '<td style="width: 200px;"><strong>' . esc_html( $key ) . '</strong></td>';
				echo '<td>';
				if ( is_array( $value ) ) {
					echo esc_html( implode( ', ', $value ) );
				} else {
					echo esc_html( $value );
				}
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo '<p>' . __( 'No data available.', 'cf7rl' ) . '</p>';
		}
		
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
}

/**
 * Add screen options for the database page
 */
function cf7rl_db_screen_options() {
	// Only add screen options when viewing a specific form
	if ( isset( $_GET['form_id'] ) ) {
		add_screen_option( 'per_page', array(
			'label'   => __( 'Submissions per page', 'cf7rl' ),
			'default' => 10,
			'option'  => 'cf7rl_submissions_per_page'
		) );
	}
}

/**
 * Process admin actions early, before any output
 * This prevents "headers already sent" errors
 */
function cf7rl_db_process_admin_actions() {
	// Only run on our admin page
	if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'cf7rl_database' ) {
		return;
	}
	
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	
	// Process delete actions
	if ( isset( $_GET['form_id'] ) && isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'delete' && isset( $_REQUEST['submission'] ) ) {
		// Check for both bulk action nonce and single delete nonce
		$nonce_valid = false;
		
		// Check bulk action nonce
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-submissions' ) ) {
			$nonce_valid = true;
		}
		
		// Check single delete nonce
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'cf7rl_delete_submission' ) ) {
			$nonce_valid = true;
		}
		
		if ( ! $nonce_valid ) {
			wp_die( __( 'Security check failed.', 'cf7rl' ) );
		}
		
		$submissions = is_array( $_REQUEST['submission'] ) ? $_REQUEST['submission'] : array( $_REQUEST['submission'] );
		
		foreach ( $submissions as $submission_id ) {
			cf7rl_db_delete_submission( intval( $submission_id ) );
		}
		
		// Redirect to clean URL
		wp_redirect( remove_query_arg( array( 'action', 'submission', '_wpnonce' ) ) );
		exit;
	}
	
	// Handle CSV export
	if ( isset( $_GET['action'] ) && $_GET['action'] === 'export' && isset( $_GET['form_id'] ) ) {
		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) ) {
			wp_die( __( 'Security check failed: Missing security token. Please try again.', 'cf7rl' ) );
		}
		
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'cf7rl_export_csv' ) ) {
			wp_die( __( 'Security check failed: Invalid security token. Please refresh the page and try again.', 'cf7rl' ) );
		}
		
		cf7rl_db_export_csv( intval( $_GET['form_id'] ) );
		exit;
	}
}
add_action( 'admin_init', 'cf7rl_db_process_admin_actions' );

/**
 * Save screen options for submissions per page
 */
function cf7rl_db_set_screen_option( $status, $option, $value ) {
	if ( 'cf7rl_submissions_per_page' === $option ) {
		return $value;
	}
	return $status;
}
add_filter( 'set-screen-option', 'cf7rl_db_set_screen_option', 10, 3 );

/**
 * Display the database submissions admin page
 */
function cf7rl_db_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	?>
	<div class="wrap">
		<h1><?php _e( 'Form Submissions Database', 'cf7rl' ); ?></h1>
		
		<?php
		// Check if viewing a specific form
		if ( isset( $_GET['form_id'] ) ) {
			cf7rl_db_display_submissions( intval( $_GET['form_id'] ) );
		} else {
			cf7rl_db_display_forms_list();
		}
		?>
	</div>
	
	<style>
		.cf7rl-db-details {
			padding: 15px;
			background: #f9f9f9;
		}
		.cf7rl-db-details table {
			margin: 0;
		}
		.cf7rl-db-details-row {
			background: #f9f9f9 !important;
		}
		.cf7rl-db-action-buttons {
			margin-bottom: 15px;
		}
		.cf7rl-db-action-buttons .button {
			margin-right: 5px;
		}
		.cf7rl-db-no-data {
			padding: 40px;
			text-align: center;
			color: #666;
			background: #fff;
			border: 1px solid #ddd;
			margin-top: 20px;
		}
	</style>
	
	<script>
	jQuery(document).ready(function($) {
		$('.cf7rl-db-expand-btn').on('click', function(e) {
			e.preventDefault();
			var detailsId = $(this).data('details');
			$('#' + detailsId).toggle();
			$(this).text(function(i, text) {
				return text === '<?php _e( 'View Details', 'cf7rl' ); ?>' ? '<?php _e( 'Hide Details', 'cf7rl' ); ?>' : '<?php _e( 'View Details', 'cf7rl' ); ?>';
			});
		});
	});
	</script>
	<?php
}

/**
 * Display list of forms with submission counts
 */
function cf7rl_db_display_forms_list() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	
	// Check if table exists
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		echo '<div class="cf7rl-db-no-data">';
		echo '<p>' . __( 'Database table is being created. Please refresh this page.', 'cf7rl' ) . '</p>';
		echo '</div>';
		return;
	}
	
	$forms = cf7rl_db_get_forms_with_counts();
	
	if ( empty( $forms ) ) {
		echo '<div class="cf7rl-db-no-data">';
		echo '<p>' . __( 'No form submissions found.', 'cf7rl' ) . '</p>';
		echo '<p>' . __( 'Submissions will appear here once forms are submitted.', 'cf7rl' ) . '</p>';
		echo '</div>';
		return;
	}
	
	$table = new CF7RL_Forms_List_Table();
	$table->prepare_items();
	$table->display();
}

/**
 * Display submissions for a specific form
 */
function cf7rl_db_display_submissions( $form_id ) {
	// Get form title
	global $wpdb;
	$table_name = $wpdb->prefix . 'cf7_submissions';
	$form_title = $wpdb->get_var( $wpdb->prepare( "SELECT form_title FROM $table_name WHERE form_id = %d LIMIT 1", $form_id ) );
	
	?>
	<div class="cf7rl-db-action-buttons">
		<a href="<?php echo esc_url( remove_query_arg( array( 'form_id', 'paged', 'action', 'submission', '_wpnonce' ) ) ); ?>" class="button">
			← <?php _e( 'Back to Forms List', 'cf7rl' ); ?>
		</a>
		
		<?php if ( $form_title ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'export', 'form_id' => $form_id, 'export_type' => 'page' ) ), 'cf7rl_export_csv' ) ); ?>" class="button">
				<?php _e( 'Export Page Results to CSV', 'cf7rl' ); ?>
			</a>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'export', 'form_id' => $form_id, 'export_type' => 'all' ) ), 'cf7rl_export_csv' ) ); ?>" class="button">
				<?php _e( 'Export All to CSV', 'cf7rl' ); ?>
			</a>
		<?php endif; ?>
	</div>
	
	<h2><?php echo esc_html( $form_title ); ?> - <?php _e( 'Submissions', 'cf7rl' ); ?></h2>
	
	<?php
	$total_count = cf7rl_db_get_submission_count( $form_id );
	
	if ( $total_count == 0 ) {
		echo '<div class="cf7rl-db-no-data">';
		echo '<p>' . __( 'No submissions found for this form.', 'cf7rl' ) . '</p>';
		echo '</div>';
		return;
	}
	
	$table = new CF7RL_Submissions_List_Table( $form_id );
	$table->prepare_items();
	?>
	
	<form method="post">
		<?php
		$table->display();
		?>
	</form>
	<?php
}
