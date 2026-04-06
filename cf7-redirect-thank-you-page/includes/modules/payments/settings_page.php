<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Add PayPal settings tab content
 */
function cf7rl_paypal_settings_tab() {
	$options = cf7rl_free_options();
	
	ob_start();
	?>
	<div id="4" style="display:none;border: 1px solid #CCCCCC;">
		<div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
		&nbsp; <?php _e('PayPal Account', 'contact-form-7-paypal-add-on'); ?>
		</div>
		<div style="background-color:#fff;padding:8px;">

            <?php echo cf7rl_free_ppcp_status_markup(); ?>

			<table width='100%'>
                <tr><td colspan='2'><br /></td></tr>

                <?php if ( !empty( $options['liveaccount'] ) ) { ?>
				<tr><td class='cf7rl_width'>
				<b><?php _e('Live Account: ', 'contact-form-7-paypal-add-on'); ?></b></td><td><input type='text' size=40 name='liveaccount' value='<?php echo $options['liveaccount']; ?>' readonly />
				</td></tr>

				<tr><td class='cf7rl_width'></td><td>
				<br /><?php _e('Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.', 'contact-form-7-paypal-add-on'); ?>
				<br /><br /><?php _e('You can find your Merchant account ID in your PayPal account under Profile -> My business info -> Merchant account ID', 'contact-form-7-paypal-add-on'); ?>

				<br /><br /><?php _e('If you don\'t have a PayPal account, you can sign up for free at', 'contact-form-7-paypal-add-on'); ?> <a target='_blank' href='https://paypal.com'><?php _e('PayPal', 'contact-form-7-paypal-add-on'); ?></a>. <br /><br />
				</td></tr>
                <?php } ?>

	            <?php if ( !empty( $options['sandboxaccount'] ) ) { ?>
				<tr><td class='cf7rl_width'>
				<b><?php _e('Sandbox Account: ', 'contact-form-7-paypal-add-on'); ?></b></td><td><input type='text' size=40 name='sandboxaccount' value='<?php echo $options['sandboxaccount']; ?>' readonly />
				</td></tr>

				<tr><td class='cf7rl_width'></td><td>
				<?php _e('Enter a valid sandbox PayPal account email address. A Sandbox account is a PayPal accont with fake money used for testing. This is useful to make sure your PayPal account and settings are working properly being going live.', 'contact-form-7-paypal-add-on'); ?>
				<br /><br /><?php _e('To create a Sandbox account, you first need a Developer Account. You can sign up for free at the', 'contact-form-7-paypal-add-on'); ?> <a target='_blank' href='https://www.paypal.com/webapps/merchantboarding/webflow/unifiedflow?execution=e1s2'><?php _e('PayPal Developer', 'contact-form-7-paypal-add-on'); ?></a> <?php _e('site.', 'contact-form-7-paypal-add-on'); ?> <br /><br />

				<?php _e('Once you have made an account, create a Sandbox Business and Personal Account', 'contact-form-7-paypal-add-on'); ?> <a target='_blank' href='https://developer.paypal.com/webapps/developer/applications/accounts'><?php _e('here', 'contact-form-7-paypal-add-on'); ?></a>. <?php _e('Enter the Business acount email on this page and use the Personal account username and password to buy something on your site as a customer.', 'contact-form-7-paypal-add-on'); ?>
				<br /><br />
				</td></tr>
	            <?php } ?>

				<tr><td class='cf7rl_width'>
				<b><?php _e('Sandbox Mode:', 'contact-form-7-paypal-add-on'); ?></b></td><td>
				<input <?php if ($options['mode'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode' value='1'><?php _e('On (Sandbox mode)', 'contact-form-7-paypal-add-on'); ?>
				<input <?php if ($options['mode'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode' value='2'><?php _e('Off (Live mode)', 'contact-form-7-paypal-add-on'); ?>
				</td></tr>

			</table>

		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Add Stripe settings tab content
 */
function cf7rl_stripe_settings_tab() {
	$options = cf7rl_free_options();
	
	ob_start();
	?>
	<div id="5" style="display:none;border: 1px solid #CCCCCC;">
		<div style="background-color:#E4E4E4;padding:8px;color:#000;font-size:15px;color:#464646;font-weight: 700;border-bottom: 1px solid #CCCCCC;">
		&nbsp; <?php _e('Stripe Account', 'contact-form-7-paypal-add-on'); ?>
		</div>
		<div style="background-color:#fff;padding:8px;">

			<table width='100%'>
				<tr><td class='cf7rl_width'><b><?php _e('Connection status:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><?php cf7rl_stripe_connection_status_html(); ?></td></tr>

				<tr><td colspan="2"><br /></td></tr>

				<?php if ( !empty($options['pub_key_live']) && !empty($options['sec_key_live']) ) { ?>
				<tr><td class='cf7rl_width'><b><?php _e('Live Publishable Key:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><input type='text' size=40 name='pub_key_live' value='<?php echo $options['pub_key_live']; ?>' disabled="disabled"></td></tr>
				<tr><td class='cf7rl_width'><b><?php _e('Live Secret Key:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><input type='text' size=40 name='sec_key_live' value='<?php echo $options['sec_key_live']; ?>' disabled="disabled"></td></tr>
				<tr><td colspan="2"><br /></td></tr>
				<?php } ?>

				<?php if ( !empty($options['pub_key_test']) && !empty($options['sec_key_test']) ) { ?>
				<tr><td class='cf7rl_width'><b><?php _e('Test Publishable Key:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><input type='text' size=40 name='pub_key_test' value='<?php echo $options['pub_key_test']; ?>' disabled="disabled"></td></tr>
				<tr><td class='cf7rl_width'><b><?php _e('Test Secret Key:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><input type='text' size=40 name='sec_key_test' value='<?php echo $options['sec_key_test']; ?>' disabled="disabled"></td></tr>
				<tr><td colspan="2"><br /></td></tr>
				<?php } ?>

				<tr><td class='cf7rl_width'><b><?php _e('Sandbox Mode:', 'contact-form-7-paypal-add-on'); ?></b></td><td>

				<input <?php if ($options['mode_stripe'] == "1") { echo "checked='checked'"; } ?> type='radio' name='mode_stripe' value='1'><?php _e('On (Sandbox mode)', 'contact-form-7-paypal-add-on'); ?>
				<input <?php if ($options['mode_stripe'] == "2") { echo "checked='checked'"; } ?> type='radio' name='mode_stripe' value='2'><?php _e('Off (Live mode)', 'contact-form-7-paypal-add-on'); ?></td></tr>


				<tr><td>
				<br />
				</td></tr>

				<tr><td class='cf7rl_width'><b><?php _e('Default Text:', 'contact-form-7-paypal-add-on'); ?> </b></td><td></td></tr>
				<tr><td class='cf7rl_width'><b><?php _e('Payment Successful:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><input type='text' size='40' name='success' value='<?php echo esc_attr($options['success']); ?>'></td></tr>
				<tr><td class='cf7rl_width'><b><?php _e('Payment Failed:', 'contact-form-7-paypal-add-on'); ?> </b></td><td><input type='text' size='40' name='failed' value='<?php echo esc_attr($options['failed']); ?>'></td></tr>
				
			</table>

		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * PPCP Status Markup
 */
function cf7rl_free_ppcp_status_markup() {
	ob_start();

	$options = cf7rl_free_options();
	$status = cf7rl_free_ppcp_status();
	if ( !empty( $status ) ) {
		if ( empty( $status['errors'] ) ) {
			$notice_type = 'success';
			$show_links = false;
		} else {
			$notice_type = 'error';
			$show_links = true;
		}
		?>
        <div id="cf7rl-ppcp-status-table">
            <table>
                <tr>
                    <td class="cf7rl-cell-left">
                        <b><?php _e('Connection status:', 'contact-form-7-paypal-add-on'); ?> </b>
                    </td>
                    <td>
                        <div class="notice inline cf7rl-ppcp-connect notice-<?php echo $notice_type; ?>">
                            <p>
								<?php if ( !empty( $status['legal_name'] ) ) { ?>
                                    <strong><?php echo $status['legal_name']; ?></strong>
                                    <br>
								<?php } ?>
								<?php echo !empty( $status['primary_email'] ) ? $status['primary_email'] . ' — ' : ''; ?><?php _e('Administrator (Owner)', 'contact-form-7-paypal-add-on'); ?></p>
								<p><?php _e('Pay as you go pricing: 2% per-transaction fee + PayPal fees.', 'contact-form-7-paypal-add-on'); ?></p>
                        </div>
                        <div>
							<?php $reconnect_mode = $status['env'] === 'live' ? 'sandbox' : 'live'; ?>
                            <?php _e('Your PayPal account is connected in', 'contact-form-7-paypal-add-on'); ?> <strong><?php echo $status['env']; ?></strong> <?php _e('mode.', 'contact-form-7-paypal-add-on'); ?>
							<?php
							$query_args = [
								'action' => 'cf7rl-ppcp-onboarding-start',
								'nonce' => wp_create_nonce( 'cf7rl-ppcp-onboarding-start' )
							];
							if ( $reconnect_mode === 'sandbox' ) {
								$query_args['sandbox'] = 1;
							}
							?>
                            <a
                                class="cf7rl-ppcp-onboarding-start"
                                data-paypal-button="true"
                                href="<?php echo add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) ); ?>"
                                target="PPFrame"
                            ><?php _e('Connect in', 'contact-form-7-paypal-add-on'); ?> <strong><?php echo $reconnect_mode; ?></strong> <?php _e('mode', 'contact-form-7-paypal-add-on'); ?></a> <?php _e('or', 'contact-form-7-paypal-add-on'); ?> <a href="#" id="cf7rl-ppcp-disconnect"><?php _e('disconnect this account', 'contact-form-7-paypal-add-on'); ?></a>.
                        </div>

						<?php if ( !empty( $status['errors'] ) ) { ?>
                            <p>
                                <strong><?php _e('There were errors connecting your PayPal account. Resolve them in your account settings, by contacting support or by reconnecting your PayPal account.', 'contact-form-7-paypal-add-on'); ?></strong>
                            </p>
                            <p>
                                <strong><?php _e('See below for more details.', 'contact-form-7-paypal-add-on'); ?></strong>
                            </p>
                            <ul class="cf7rl-ppcp-list cf7rl-ppcp-list-error">
								<?php foreach ( $status['errors'] as $error ) { ?>
                                    <li><?php echo $error; ?></li>
								<?php } ?>
                            </ul>
						<?php } ?>

						<?php if ( $show_links ) { ?>
                            <ul class="cf7rl-ppcp-list">
                                <li><a href="https://www.paypal.com/myaccount/settings/"><?php _e('PayPal account settings', 'contact-form-7-paypal-add-on'); ?></a></li>
                                <li><a href="https://www.paypal.com/us/smarthelp/contact-us"><?php _e('PayPal support', 'contact-form-7-paypal-add-on'); ?></a></li>
                            </ul>
						<?php } ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br />
                    </td>
                </tr>
            </table>
        </div>
		<?php
	} else { ?>
        <table id="cf7rl-ppcp-status-table" class="cf7rl-ppcp-initial-view-table">
            <tr>
                <td>
                    <img class="cf7rl-ppcp-paypal-logo" src="<?php echo cf7rl_FREE_URL; ?>imgs/paypal-logo.png" alt="paypal-logo" />
                </td>
                <td class="cf7rl-ppcp-align-right cf7rl-ppcp-icons">
                    <img class="cf7rl-ppcp-paypal-methods" src="<?php echo cf7rl_FREE_URL; ?>imgs/paypal-express.png" alt="paypal-expresss" />
                    <img class="cf7rl-ppcp-paypal-methods" src="<?php echo cf7rl_FREE_URL; ?>imgs/paypal-advanced.png" alt="paypal-advanced" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3 class="cf7rl-ppcp-title"><?php _e('PayPal: The all-in-one checkout solution', 'contact-form-7-paypal-add-on'); ?></h3>
                    <ul class="cf7rl-ppcp-list">
                        <li><?php _e('Help drive conversion by offering customers a seamless checkout experience', 'contact-form-7-paypal-add-on'); ?></li>
                        <li><?php _e('Securely accepts all major credit/debit cards and local payment methods with the strength of the PayPal network', 'contact-form-7-paypal-add-on'); ?></li>
                        <li><?php _e('You only pay the standard PayPal fees + 2%.', 'contact-form-7-paypal-add-on'); ?></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
					<?php
					$mode = intval( $options['mode'] );
					$query_args = [
						'action' => 'cf7rl-ppcp-onboarding-start',
						'nonce' => wp_create_nonce( 'cf7rl-ppcp-onboarding-start' )
					];
					if ( $mode === 1 ) {
						$query_args['sandbox'] = 1;
					}
					?>
                    <a
                        id="cf7rl-ppcp-onboarding-start-btn"
                        class="cf7rl-ppcp-button cf7rl-ppcp-onboarding-start"
                        data-paypal-button="true"
                        href="<?php echo add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) ); ?>"
                        target="PPFrame"
                    ><?php _e('Get started', 'contact-form-7-paypal-add-on'); ?></a>
                </td>
                <td class="cf7rl-ppcp-align-right">
                    <a href="https://www.paypal.com/us/webapps/mpp/merchant-fees#statement-2" class="cf7rl-ppcp-link" target="_blank"><?php _e('View our simple and transparent pricing', 'contact-form-7-paypal-add-on'); ?></a>
                </td>
            </tr>
			<?php if ( !empty( $_GET['error'] ) && in_array( $_GET['error'], ['security', 'api'] ) ) { ?>
                <tr>
                    <td colspan="2">
                        <ul class="cf7rl-ppcp-list cf7rl-ppcp-list-error">
                            <li>
								<?php
								if ( $_GET['error'] === 'security' ) {
									_e( 'The request has not been authenticated. Please reload the page and try again.', 'contact-form-7-paypal-add-on' );
								} else {
									_e( 'The request ended with an error. Please reload the page and try again.', 'contact-form-7-paypal-add-on' );
								}
								?>
                            </li>
                        </ul>
                    </td>
                </tr>
			<?php } ?>
        </table>
		<?php
	}

	if ( !wp_doing_ajax() ) { ?>
        <script>
            (function(d, s, id){
                var js, ref = d.getElementsByTagName(s)[0]; if (!d.getElementById(id)){
                    js = d.createElement(s); js.id = id; js.async = true;
                    js.src =
                        "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
                    ref.parentNode.insertBefore(js, ref); }
            }(document, "script", "paypal-js"));
        </script>
	<?php }

	return ob_get_clean();
}
