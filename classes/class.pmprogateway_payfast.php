<?php
/**
 * Based on the scripts by Ron Darby shared at
 * https://payfast.io/integration/shopping-carts/paid-memberships-pro/
 *
 * @author     Ron Darby - PayFast
 * @copyright  2009-2014 PayFast (Pty) Ltd
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

// Require the default PMPro Gateway Class.
require_once PMPRO_DIR . '/classes/gateways/class.pmprogateway.php';

// load classes init method
add_action( 'init', array( 'PMProGateway_PayFast', 'init' ) );
class PMProGateway_PayFast extends PMProGateway {

	function __construct( $gateway = NULL ) {
		$this->gateway = $gateway;
		$this->gateway_environment = get_option( 'pmpro_gateway_environment' );

		return $this->gateway;
	}

	/**
	 * Run on WP init
	 *
	 * @since 1.8
	 */
	static function init() {
		// make sure PayFast is a gateway option
		add_filter( 'pmpro_gateways', array( 'PMProGateway_PayFast', 'pmpro_gateways' ) );

		// add fields to payment settings
		add_filter( 'pmpro_payment_options', array( 'PMProGateway_PayFast', 'pmpro_payment_options' ) );

		add_filter( 'pmpro_payment_option_fields', array( 'PMProGateway_PayFast', 'pmpro_payment_option_fields' ), 10, 2 );

		if ( get_option( 'pmpro_gateway' ) == 'payfast' ) {
			add_filter( 'pmpro_include_billing_address_fields', '__return_false' );
			add_filter( 'pmpro_include_payment_information_fields', '__return_false' );
			add_filter( 'pmpro_billing_show_payment_method', '__return_false' );
			add_action( 'pmpro_billing_before_submit_button', array( 'PMProGateway_PayFast', 'pmpro_billing_before_submit_button' ) );
		}

		add_filter( 'pmpro_required_billing_fields', array( 'PMProGateway_PayFast', 'pmpro_required_billing_fields' ) );
		add_filter( 'pmpro_checkout_before_submit_button', array( 'PMProGateway_PayFast', 'pmpro_checkout_before_submit_button' ) );

		// itn handler
		add_action( 'wp_ajax_nopriv_pmpro_payfast_itn_handler', array( 'PMProGateway_PayFast', 'wp_ajax_pmpro_payfast_itn_handler' ) );
		add_action( 'wp_ajax_pmpro_payfast_itn_handler', array( 'PMProGateway_PayFast', 'wp_ajax_pmpro_payfast_itn_handler' ) );

	}

	/**
	 * Make sure this gateway is in the gateways list
	 *
	 * @since 1.8
	 */
	static function pmpro_gateways( $gateways ) {
		if ( empty( $gateways['payfast'] ) ) {
			$gateways['payfast'] = __( 'PayFast', 'pmpro-payfast' );
		}

		return $gateways;
	}

	/* What features does Payfast support
	 * 
	 * @since TBD
	 * 
	 * @return array
	 */
	public static function supports( $feature ) {
		$supports = array(
			'subscription_sync' => true,
		);

		if ( empty( $supports[$feature] ) ) {
			return false;
		}

		return $supports[$feature];
	}

	/**
	 * Get a list of payment options that the this gateway needs/supports.
	 *
	 * @since 1.8
	 */
	static function getGatewayOptions() {
		$options = array(
			'payfast_debug',
			'payfast_merchant_id',
			'payfast_merchant_key',
			'payfast_passphrase',
			'currency',
			'tax_state',
			'tax_rate',
		);

		return $options;
	}

	/**
	 * Set payment options for payment settings page.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_options( $options ) {
		// get stripe options
		$payfast_options = self::getGatewayOptions();

		// merge with others.
		$options = array_merge( $payfast_options, $options );

		return $options;
	}

	/**
	 * Display fields for this gateway's options.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_option_fields( $values, $gateway ) {      ?>
		<tr class="gateway gateway_payfast" 
			<?php
			if ( $gateway != 'payfast' ) {
				?>
			style="display: none;"<?php } ?>>
			 <th scope="row" valign="top">
				 <label for="payfast_merchant_id"><?php _e( 'PayFast Merchant ID', 'pmpro-payfast' ); ?>:</label>
			 </th>
			 <td>
				 <input id="payfast_merchant_id" name="payfast_merchant_id" value="<?php echo esc_attr( $values['payfast_merchant_id'] ); ?>" />
			 </td>
		 </tr>
		 <tr class="gateway gateway_payfast" 
			 <?php
				if ( $gateway != 'payfast' ) {
					?>
				style="display: none;"<?php } ?>>
			 <th scope="row" valign="top">
				 <label for="payfast_merchant_key"><?php _e( 'PayFast Merchant Key', 'pmpro-payfast' ); ?>:</label>
			 </th>
			 <td>
				 <input id="payfast_merchant_key" name="payfast_merchant_key" value="<?php echo esc_attr( $values['payfast_merchant_key'] ); ?>" />
			 </td>
		 </tr>
		 <tr class="gateway gateway_payfast" 
			 <?php
				if ( $gateway != 'payfast' ) {
					?>
				style="display: none;"<?php } ?>>
			 <th scope="row" valign="top">
				 <label for="payfast_debug"><?php _e( 'PayFast Debug Mode', 'pmpro-payfast' ); ?>:</label>
			 </th>
			 <td>
				 <select name="payfast_debug">
					 <option value="1" 
				 <?php
					if ( isset( $values['payfast_debug'] ) && $values['payfast_debug'] ) {
						?>
							selected="selected"<?php } ?>><?php _e( 'On', 'pmpro-payfast' ); ?>
					</option>
					<option value="0" 
						<?php
						if ( isset( $values['payfast_debug'] ) && ! $values['payfast_debug'] ) {
							?>
							selected="selected"<?php } ?>><?php _e( 'Off', 'pmpro-payfast' ); ?>
					</option>
				 </select>
			 </td>
		 </tr>
		<tr class="gateway gateway_payfast" 
			<?php
			if ( $gateway != 'payfast' ) {
				?>
			style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="payfast_passphrase"><?php _e( 'PayFast PassPhrase', 'pmpro-payfast' ); ?>:</label>
			</th>
			<td>
				<input id="payfast_passphrase" name="payfast_passphrase" value="<?php echo esc_attr( $values['payfast_passphrase'] ); ?>" /> &nbsp;<small><?php _e( 'A passphrase is now required for all transactions.', 'pmpro-payfast' ); ?></small>
			</td>
		</tr>
		<script>
			//trigger the payment gateway dropdown to make sure fields show up correctly
			jQuery(document).ready(function() {
				pmpro_changeGateway(jQuery('#gateway').val());
			});
		</script>
			<?php
	}

	/**
	 * Remove required billing fields
	 *
	 * @since 1.8
	 */
	static function pmpro_required_billing_fields( $fields ) {

		unset( $fields['bfirstname'] );
		unset( $fields['blastname'] );
		unset( $fields['baddress1'] );
		unset( $fields['bcity'] );
		unset( $fields['bstate'] );
		unset( $fields['bzipcode'] );
		unset( $fields['bphone'] );
		unset( $fields['bemail'] );
		unset( $fields['bcountry'] );
		unset( $fields['CardType'] );
		unset( $fields['AccountNumber'] );
		unset( $fields['ExpirationMonth'] );
		unset( $fields['ExpirationYear'] );
		unset( $fields['CVV'] );

		return $fields;
	}

	/**
	 * Show a notice on the Update Billing screen.
	 * 
	 * @since 1.0.0
	 */
	static function pmpro_billing_before_submit_button() {

		if ( apply_filters( 'pmpro_payfast_hide_update_billing_button', true ) ) {
		?>
		<script>
			jQuery(document).ready(function(){
				jQuery('.pmpro_form_submit').hide();
			});
		</script>
		<?php
		}
		echo sprintf( __( "If you need to update your billing details, please login to your %s account to update these credentials. Selecting the update button below will automatically redirect you to Payfast.", 'pmpro-payfast'), "<a href='https://payfast.io' target='_blank'>Payfast</a>" );
	}

	/**
	 * Show information before PMPro's checkout button.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_before_submit_button() {
		global $gateway, $pmpro_requirebilling;

		// Bail if gateway isn't PayFast.
		if ( $gateway != 'payfast' ) {
			return;
		}

		// see if Pay By Check Add On is active, if it's selected let's hide the PayFast information.
		if ( defined( 'PMPROPBC_VER' ) ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() { 
					jQuery('input:radio[name=gateway]').on( 'click', function() { 
						 var val = jQuery(this).val();

						 if ( val === 'check' ) {
							 jQuery( '#pmpro_payfast_before_checkout' ).hide();
						 } else {
							 jQuery( '#pmpro_payfast_before_checkout' ).show();
						 }
					});
				});	
			</script>
			<?php } ?>

		<div id="pmpro_payfast_before_checkout" style="text-align:center;">
			<span id="pmpro_payfast_checkout" 
			<?php
			if ( $gateway != 'payfast' || ! $pmpro_requirebilling ) {
				?>
				style="display: none;"<?php } ?>>
				<input type="hidden" name="submit-checkout" value="1" />
				<span class="screen-reader-text"><?php esc_html_e( 'PayFast Checkout description text. Please note that if changing a subscription it may take a minute or two to reflect. Please also login to your PayFast account to ensure the old subscription is cancelled.', 'pmpro-payfast' ); ?></span>
				<p><?php echo '<strong>' . esc_html__( 'NOTE:', 'pmpro-payfast' ) . '</strong> ' . esc_html__( 'if changing a subscription it may take a minute or two to reflect. Please also login to your PayFast account to ensure the old subscription is cancelled.', 'pmpro-payfast' ); ?> </p>
				<p><img src="<?php echo plugins_url( 'img/payfast_logo.png', __DIR__ ); ?>" alt="Payfast logo" width="100px" /></p>
			</span>
		</div>
			<?php
	}


	/**
	 * Send traffic to wp-admin/admin-ajax.php?action=pmpro_payfast_itn_handler to the itn handler
	 * 
	 * @since 1.0.0
	 */
	static function wp_ajax_pmpro_payfast_itn_handler() {
		require_once PMPRO_PAYFAST_DIR . 'services/payfast_itn_handler.php';
		exit;
	}

	function process( &$order ) {

		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		// clean up a couple values
		$order->payment_type = 'PayFast';
		$order->CardType     = '';
		$order->cardtype     = '';
		$order->status = 'token';
		$order->saveOrder();

		pmpro_save_checkout_data_to_order( $order );

		do_action( 'pmpro_before_send_to_payfast', $order->user_id, $order );

		$this->sendToPayFast( $order );
	}

	/**
	 * @param $order
	 */
	function sendToPayFast( &$order ) {
		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		$order->payment_type = 'PayFast';
		$order->CardType = "";
		$order->cardtype = "";
		$order->ProfileStartDate = date_i18n( 'Y-m-d', current_time( 'timestamp' ) );

		// Get the level amount so we can set the initial payment amount.
		$level = $order->getMembershipLevelAtCheckout();

		// taxes on initial payment
		$initial_payment     = $order->subtotal;
		$initial_payment_tax = $order->getTaxForPrice( $initial_payment );
		$initial_payment     = round( (float) $initial_payment + (float) $initial_payment_tax, 2 );

		// taxes on the amount
		$billing_amount          = empty( $level->billing_amount ) ? 0 : $level->billing_amount;
		$billing_amount_tax      = $order->getTaxForPrice( $billing_amount );
		$billing_amount          = round( (float) $billing_amount + (float) $billing_amount_tax, 2 );

		// merchant details
		$merchant_id  = get_option( 'pmpro_payfast_merchant_id' );
		$merchant_key = get_option( 'pmpro_payfast_merchant_key' );

		// build PayFast Redirect
		$environment = get_option( 'pmpro_gateway_environment' );
		if ( 'sandbox' === $environment || 'beta-sandbox' === $environment ) {
			$payfast_url = 'https://sandbox.payfast.co.za/eng/process';
		} else {
			$payfast_url = 'https://www.payfast.co.za/eng/process';
		}

		$user = get_userdata( $order->user_id );
		$nameparts = pnp_split_full_name( $order->billing->name );

		$data = array(
			'merchant_id'   => $merchant_id,
			'merchant_key'  => $merchant_key,
			'return_url'    => pmpro_url( 'confirmation', '?level=' . $order->membership_level->id ),
			'cancel_url'    => pmpro_url( 'levels' ),
			'notify_url'    => admin_url( 'admin-ajax.php' ) . '?action=pmpro_payfast_itn_handler',
			'name_first'    => empty( $nameparts['fname'] ) ? '' : $nameparts['fname'],
			'name_last'     => empty( $nameparts['lname'] ) ? '' : $nameparts['lname'],
			'email_address' => $user->user_email,
			'm_payment_id'  => $order->code,
			'amount'        => $initial_payment,
			'item_name'     => html_entity_decode( substr( $order->membership_level->name . ' at ' . get_bloginfo( 'name' ), 0, 99 ) ),
			'custom_int1'   => $order->user_id,
		);		

		$cycles = $order->membership_level->billing_limit;
		if( ! empty( $level->cycle_number ) ) {
			// convert PMPro cycle_number and period into a PayFast frequency
			switch ( $level->cycle_period ) {
				case 'Day':
					$frequency = '1';
					break;
				case 'Week':
					$frequency = '2';
					break;
				case 'Month':
					$frequency = '3';
					break;
				case 'Year':
					$frequency = '6';
					break;
			}
		}

		// Add subscription data
		if ( pmpro_isLevelRecurring( $level ) && ! empty( $frequency ) ) {
			$data['custom_str1']       = date( 'Y-m-d', current_time( 'timestamp' ) ); // This is used to store the date of the initial payment for referencing in the ITN.
			$data['subscription_type'] = 1;

			// Adding support to use the new method to calculate subscription delays, or any tweaks but falling back to using the filter in cases of older PMPro versions.
			if ( function_exists( 'pmpro_calculate_profile_start_date' ) ) {
				$data['billing_date'] 	   = pmpro_calculate_profile_start_date( $order, 'Y-m-d' );
			} else {
				$data['billing_date']      = apply_filters( 'pmpro_profile_start_date', $data['custom_str1'], $order );
			}

			$data['recurring_amount']  = $billing_amount;
			$data['frequency']         = $frequency;
			$data['cycles']            = $cycles == 0 ? 0 : $cycles + 1;

			// Remove time parameter if it's set.
			$data['billing_date'] = str_replace( 'T0:0:0', '', $data['billing_date'] );

			// filter order before subscription. use with care.
			$order = apply_filters( 'pmpro_subscribe_order', $order, $this );
		}

		$data = apply_filters( 'pmpro_payfast_data', $data, $order );

		$order->status                      = 'token';
		$order->payment_transaction_id      = $order->code;
		$order->subscription_transaction_id = $order->code;
		$order->tax = $initial_payment_tax;
		$order->total = $initial_payment;		

		// Save the order before redirecting to PayFast.
		$order->saveOrder();

		$pfOutput  = '';
		$pffOutput = '';

		foreach ( $data  as $key => $val ) {
			$pffOutput .= $key . '=' . urlencode( trim( $val ) ) . '&';
		}

		// Remove last ampersand
		$passPhrase = get_option( 'pmpro_payfast_passphrase' );

		// Add passphrase to URL.
		if ( empty( $passPhrase ) ) {
			$pfOutput = substr( $pffOutput, 0, -1 );
		} else {
			$pfOutput = $pffOutput . 'passphrase=' . urlencode( trim( $passPhrase ) );
		}

		// Create signature.
		$signature = md5( $pfOutput );

		/**
		 * @todo: Check the user_agent and generate a better user agent for description.
		 */
		$payfast_url .= '?' . $pffOutput . '&signature=' . $signature . '&user_agent=Paid Memberships Pro ' . PMPRO_VERSION;

		wp_redirect( $payfast_url );
		exit;
	}

	function subscribe( &$order ) {
		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		// filter order before subscription. use with care.
		$order = apply_filters( 'pmpro_subscribe_order', $order, $this );

		$order->status                      = 'success';
		$order->payment_transaction_id      = $order->code;
		$order->subscription_transaction_id = $order->code;

		// update order
		$order->saveOrder();

		return true;
	}

	// Note: Leaving this method here, as it may be used by a large number of users that aren't on PMPro 3.0 yet.
	function cancel( &$order ) {

		// Check to see if the order has a token and try to cancel it at the gateway. Only recurring subscriptions should have a token.
		if ( ! empty( $order->subscription_transaction_id ) ) {

			// Let's double check that the paypal_token isn't really there. (Payfast uses paypal_token to store their token)
			if ( empty( $order->paypal_token ) ) {
				$last_subscription_order = $order->get_orders( array( 'subscription_transaction_id' => $order->subscription_transaction_id, 'limit' => 1 ) );
				$order->paypal_token = $last_subscription_order[0]->paypal_token;
			}

			// cancel order status immediately.
			if ( $update_status ) {
				$order->updateStatus( 'cancelled' );
			}

			// check if we are getting an ITN notification which means it's already cancelled within PayFast.
			if ( ! empty( $_POST['payment_status'] ) && $_POST['payment_status'] == 'CANCELLED' ) {
				return true;
			}

			$token = $order->paypal_token;

			$hashArray  = array();
			$passphrase = get_option( 'pmpro_payfast_passphrase' );

			$hashArray['version']     = 'v1';
			$hashArray['merchant-id'] = get_option( 'pmpro_payfast_merchant_id' );
			$hashArray['passphrase']  = $passphrase;
			$hashArray['timestamp']   = date( 'Y-m-d' ) . 'T' . date( 'H:i:s' );

			$orderedPrehash = $hashArray;

			ksort( $orderedPrehash );

			$signature = md5( http_build_query( $orderedPrehash ) );

			$domain = 'https://api.payfast.co.za';

			$url = $domain . '/subscriptions/' . $token . '/cancel';

			$environment = get_option( 'pmpro_gateway_environment' );

			if ( 'sandbox' === $environment || 'beta-sandbox' === $environment ) {
				$url = $url . '?testing=true';
			}

			$response = wp_remote_post(
				$url,
				array(
					'method' => 'PUT',
					'timeout' => 60,
					'headers' => array(
						'version'     => 'v1',
						'merchant-id' => $hashArray['merchant-id'],
						'signature'   => $signature,
						'timestamp'   => $hashArray['timestamp'],
						'content-length' => 0
					),
				)
			);

			$response_code    = wp_remote_retrieve_response_code( $response );
			$response_message = wp_remote_retrieve_response_message( $response );

			if ( 200 == $response_code ) {
				return true;
			} else {
				$order->updateStatus( 'error' );
				$order->errorcode  = $response_code;
				$order->error      = $response_message;
				$order->shorterror = $response_message;

				return false;
			}
		}
	}

	/**
	 * Cancel subscription at the gateway.
	 * Supports PMPro 3.2+
	 *
	 * @param Object $subscription
	 * @return bool	$success Returns whether the subscription was successfully cancelled.
	 * @since 1.5
	 */
	function cancel_subscription( $subscription ) {
		
		$subscription_id = $subscription->get_subscription_transaction_id();
		$last_order = $subscription->get_orders( array( 'subscription_transaction_id' => $subscription_id, 'limit' => 1 ) );

		// Get the last order, if it's empty then we can't cancel the subscription.
		if ( empty( $last_order ) ) {
			return false;
		} else {
			$order = $last_order[0];
		}
		
		$payfast_token = isset( $order->paypal_token ) ? sanitize_text_field( $order->paypal_token ) : false;

		// No payfast token found, let's bail.
		if ( ! $payfast_token ) {
			return false;
		}
		
		// check if we are getting an ITN notification which means it's already cancelled within PayFast.
		if ( ! empty( $_POST['payment_status'] ) && $_POST['payment_status'] == 'CANCELLED' ) {
			return true;
		}

		$hashArray  = array();
		$passphrase = get_option( 'pmpro_payfast_passphrase' );

		$hashArray['version']     = 'v1';
		$hashArray['merchant-id'] = get_option( 'pmpro_payfast_merchant_id' );
		$hashArray['passphrase']  = $passphrase;
		$hashArray['timestamp']   = date( 'Y-m-d' ) . 'T' . date( 'H:i:s' );

		$orderedPrehash = $hashArray;

		ksort( $orderedPrehash );

		$signature = md5( http_build_query( $orderedPrehash ) );

		$domain = 'https://api.payfast.co.za';

		$url = $domain . '/subscriptions/' . $payfast_token . '/cancel';

		$environment = get_option( 'pmpro_gateway_environment' );

		if ( 'sandbox' === $environment || 'beta-sandbox' === $environment ) {
			$url = $url . '?testing=true';
		}

		$response = wp_remote_post(
			$url,
			array(
				'method' => 'PUT',
				'timeout' => 60,
				'headers' => array(
					'version'     => 'v1',
					'merchant-id' => $hashArray['merchant-id'],
					'signature'   => $signature,
					'timestamp'   => $hashArray['timestamp'],
					'content-length' => 0
				),
			)
		);

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( 200 == $response_code ) {
			$this->update_subscription_info( $subscription );
			return true;
		} else {
			$order->updateStatus( 'error' );
			$order->errorcode  = $response_code;
			$order->error      = $response_message;
			$order->shorterror = $response_message;

			return false;
		}

		return true; // If we made it here let's just return false for whatever reason. ///
	}

	/**
	 * Function to handle cancellations of Subscriptions.
	 *
	 * @param object $subscription The PMPro Subscription Object
	 * @return string|null Error message returned from gateway.
	 * @since 1.5
	 */
	function update_subscription_info( $subscription ) {

		// We need to get the token from the order with this $subscription_id.
		$subscription_id = $subscription->get_subscription_transaction_id();

		$last_subscription_order = $subscription->get_orders( array( 'subscription_transaction_id' => $subscription_id, 'limit' => 1 ) );
		
		$payfast_token = isset( $last_subscription_order[0]->paypal_token ) ? sanitize_text_field( $last_subscription_order[0]->paypal_token ) : false;

		// No token found, let's bail.
		if ( ! $payfast_token ) {
			return false;
		}

		$hashArray  = array();
		$passphrase = get_option( 'pmpro_payfast_passphrase' );

		$hashArray['version']     = 'v1';
		$hashArray['merchant-id'] = get_option( 'pmpro_payfast_merchant_id' );
		$hashArray['passphrase']  = $passphrase;
		$hashArray['timestamp']   = date( 'Y-m-d' ) . 'T' . date( 'H:i:s' );

		$orderedPrehash = $hashArray;

		ksort( $orderedPrehash );

		$signature = md5( http_build_query( $orderedPrehash ) );

		$domain = 'https://api.payfast.co.za';

		$url = $domain . '/subscriptions/' . $payfast_token . '/fetch';

		// Is this a test transaction?
		$environment = get_option( 'pmpro_gateway_environment' );
		if ( 'sandbox' === $environment || 'beta-sandbox' === $environment ) {
				$url = $url . '?testing=true';
			}

			$request = wp_remote_get(
				$url,
				array(
					'timeout' => 60,
					'headers' => array(
						'version'     => 'v1',
						'merchant-id' => $hashArray['merchant-id'],
						'signature'   => $signature,
						'timestamp'   => $hashArray['timestamp'],
						'content-length' => 0
					),
				)
			);
		
		// Get the data from the response now and update the subscription.
		if ( ! is_wp_error( $request ) ) {

		$response = json_decode( wp_remote_retrieve_body( $request ) );

		if ( 200 !== $response->code ) {
			return __( sprintf( 'Payfast error: %s', $response->data->response ), 'pmpro-payfast' );
		}

		// No data in the response.
		if ( empty( $response->data->response ) ) {
			return false;
		}

		$sub_info = $response->data->response;
		$update_array = array();
		
		// Get the subscription status and update it accordingly.
		// Status 1 = COMPLETED, Status 7 = UPSTREAM. We assume that status 7 is pending approval and okay and assume we're good.
		if ( ! in_array( $sub_info->status, array( 1, 7 ) ) ) {
			$update_array['status'] = 'cancelled';
		} else {
			$update_array['status'] = 'active';
		}

		// Convert the frequency of the subscription back to PMPro format.
		switch ( $sub_info->frequency ) {
			case '1':
				$update_array['cycle_period'] = 'Day';
				break;
			case '2':
				$update_array['cycle_period'] = 'Week';
				break;
			case '3':
				$update_array['cycle_period'] = 'Month';
				break;
			case '6':
				$update_array['cycle_period'] = 'Year';
				break;
			default:
				$update_array['cycle_period'] = 'Month';
		}

		$update_array['next_payment_date'] = sanitize_text_field( $sub_info->run_date );
		$update_array['billing_amount'] = (float) $sub_info->amount/100;

		$subscription->set( $update_array );
		} else {
			return esc_html__( 'There was an error connecting to Payfast. Please check your connectivity or API details and try again later.', 'pmpro-payfast' );
		}
	}

} //end of class
