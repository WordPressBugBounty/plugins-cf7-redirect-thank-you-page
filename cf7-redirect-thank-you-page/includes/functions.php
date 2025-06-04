<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// display activation notice
function cf7rl_my_plugin_admin_notices() {
	if (!get_option('cf7rl_my_plugin_notice_shown')) {
		echo "<div class='updated'><p><a href='admin.php?page=cf7rl_admin_table'>Click here to view the plugin settings</a>.</p></div>";
		update_option("cf7rl_my_plugin_notice_shown", "true");
	}
}
add_action('admin_notices', 'cf7rl_my_plugin_admin_notices');

// display review notice after 1 week
function cf7rl_review_admin_notice() {
	// Don't show on plugin pages or if already dismissed
	if (get_option('cf7rl_review_notice_dismissed') == 'true') {
		return;
	}
	
	$install_date = get_option('cf7rl_install_date');
	if (!$install_date) {
		return;
	}
	
	// Check if a week has passed (7 days = 604800 seconds)
	$week_in_seconds = 7 * 24 * 60 * 60;
	if (time() - $install_date < $week_in_seconds) {
		return;
	}
	
	// Get current screen to avoid showing on plugin's own pages
	$screen = get_current_screen();
	if (strpos($screen->id, 'cf7rl') !== false || strpos($screen->id, 'wpcf7') !== false) {
		return;
	}
	
	?>
	<div class="notice notice-info is-dismissible cf7rl-review-notice">
		<p>
			<strong>Contact Form 7 - Redirect & Thank You Page</strong><br>
			It's been a week since you installed our plugin. If it's been helpful, could you please leave us a 5-star review? It would mean a lot to us!
		</p>
		<p>
			<a href="https://wordpress.org/support/plugin/cf7-redirect-thank-you-page/reviews/?filter=5#new-post" class="button-primary" target="_blank">Leave a Review</a>
			<a href="?cf7rl_dismiss_review_notice=1" class="button-secondary">Maybe Later</a>
			<a href="?cf7rl_dismiss_review_notice=1&cf7rl_review_never=1" class="button-link">Don't Show Again</a>
		</p>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$(document).on('click', '.cf7rl-review-notice .notice-dismiss', function() {
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'cf7rl_dismiss_review_notice',
						nonce: '<?php echo wp_create_nonce('cf7rl_dismiss_review_nonce'); ?>'
					}
				});
			});
		});
	</script>
	<?php
}
add_action('admin_notices', 'cf7rl_review_admin_notice');

// handle review notice dismissal
function cf7rl_handle_review_notice_dismissal() {
	if (isset($_GET['cf7rl_dismiss_review_notice']) && $_GET['cf7rl_dismiss_review_notice'] == '1') {
		if (isset($_GET['cf7rl_review_never']) && $_GET['cf7rl_review_never'] == '1') {
			// Never show again
			update_option('cf7rl_review_notice_dismissed', 'true');
		} else {
			// Show again in another week
			update_option('cf7rl_install_date', time());
		}
		
		// Redirect to remove the URL parameters
		$redirect_url = remove_query_arg(array('cf7rl_dismiss_review_notice', 'cf7rl_review_never'));
		wp_redirect($redirect_url);
		exit;
	}
}
add_action('admin_init', 'cf7rl_handle_review_notice_dismissal');

// AJAX handler for dismiss button (X button)
function cf7rl_ajax_dismiss_review_notice() {
	if (!wp_verify_nonce($_POST['nonce'], 'cf7rl_dismiss_review_nonce')) {
		wp_die('Security check failed');
	}
	
	// When dismissed via X button, show again in another week
	update_option('cf7rl_install_date', time());
	wp_die();
}
add_action('wp_ajax_cf7rl_dismiss_review_notice', 'cf7rl_ajax_dismiss_review_notice');