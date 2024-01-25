<?php
/**
 * Based on the scripts by Ron Darby shared at
 * https://mpesa.io/integration/shopping-carts/paid-memberships-pro/
 *
 * @author     Ron Darby - MPesa
 * @copyright  2009-2014 MPesa (Pty) Ltd
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

// Require the default PMPro Gateway Class.
require_once PMPRO_DIR . '/classes/gateways/class.pmprogateway.php';

// load classes init method
add_action( 'init', array( 'PMProGateway_MPesa', 'init' ) );
class PMProGateway_MPesa extends PMProGateway {

	function __construct( $gateway = null ) {
		return parent::__construct( $gateway );
	}

	/**
	 * Run on WP init
	 *
	 * @since 1.8
	 */
	static function init() {
		// make sure MPesa is a gateway option
		add_filter( 'pmpro_gateways', array( 'PMProGateway_MPesa', 'pmpro_gateways' ) );

		// add fields to payment settings
		add_filter( 'pmpro_payment_options', array( 'PMProGateway_MPesa', 'pmpro_payment_options' ) );

		add_filter( 'pmpro_payment_option_fields', array( 'PMProGateway_MPesa', 'pmpro_payment_option_fields' ), 10, 2 );

		if ( get_option( 'pmpro_gateway' ) == 'mpesa' ) {
			add_filter( 'pmpro_include_billing_address_fields', '__return_false' );
			add_filter( 'pmpro_include_payment_information_fields', '__return_false' );
			add_filter( 'pmpro_billing_show_payment_method', '__return_false' );
			add_action( 'pmpro_billing_before_submit_button', array( 'PMProGateway_MPesa', 'pmpro_billing_before_submit_button' ) );
		}

		add_filter( 'pmpro_required_billing_fields', array( 'PMProGateway_MPesa', 'pmpro_required_billing_fields' ) );
		add_filter( 'pmpro_checkout_before_submit_button', array( 'PMProGateway_MPesa', 'pmpro_checkout_before_submit_button' ) );
		add_filter( 'pmpro_checkout_before_change_membership_level', array( 'PMProGateway_MPesa', 'pmpro_checkout_before_change_membership_level' ), 10, 2 );

		// itn handler
		add_action( 'wp_ajax_nopriv_pmpro_mpesa_itn_handler', array( 'PMProGateway_MPesa', 'wp_ajax_pmpro_mpesa_itn_handler' ) );
		add_action( 'wp_ajax_pmpro_mpesa_itn_handler', array( 'PMProGateway_MPesa', 'wp_ajax_pmpro_mpesa_itn_handler' ) );

		add_filter( 'pmpro_gateways_with_pending_status', array( 'PMProGateway_MPesa', 'pmpro_gateways_with_pending_status' ) );
	}


	/**
	 * Add MPesa to the list of allowed gateways.
	 *
	 * @return array
	 */
	static function pmpro_gateways_with_pending_status( $gateways ) {
		$gateways[] = 'mpesa';

		return $gateways;
	}

	/**
	 * Make sure this gateway is in the gateways list
	 *
	 * @since 1.8
	 */
	static function pmpro_gateways( $gateways ) {
		if ( empty( $gateways['mpesa'] ) ) {
			$gateways['mpesa'] = __( 'MPesa', 'pmpro-mpesa' );
		}

		return $gateways;
	}

	/**
	 * Get a list of payment options that the this gateway needs/supports.
	 *
	 * @since 1.8
	 */
	static function getGatewayOptions() {
		$options = array(
			'mpesa_debug',
			'mpesa_merchant_id',
			'mpesa_merchant_key',
			'mpesa_passphrase',
			'currency',
			'use_ssl',
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
		$mpesa_options = self::getGatewayOptions();

		// merge with others.
		$options = array_merge( $mpesa_options, $options );

		return $options;
	}

	/**
	 * Display fields for this gateway's options.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_option_fields( $values, $gateway ) {      ?>
		<tr class="gateway gateway_mpesa" 
			<?php
			if ( $gateway != 'mpesa' ) {
				?>
			style="display: none;"<?php } ?>>
			 <th scope="row" valign="top">
				 <label for="mpesa_merchant_id"><?php _e( 'MPesa Merchant ID', 'pmpro-mpesa' ); ?>:</label>
			 </th>
			 <td>
				 <input id="mpesa_merchant_id" name="mpesa_merchant_id" value="<?php echo esc_attr( $values['mpesa_merchant_id'] ); ?>" />
			 </td>
		 </tr>
		 <tr class="gateway gateway_mpesa" 
			 <?php
				if ( $gateway != 'mpesa' ) {
					?>
				style="display: none;"<?php } ?>>
			 <th scope="row" valign="top">
				 <label for="mpesa_merchant_key"><?php _e( 'MPesa Merchant Key', 'pmpro-mpesa' ); ?>:</label>
			 </th>
			 <td>
				 <input id="mpesa_merchant_key" name="mpesa_merchant_key" value="<?php echo esc_attr( $values['mpesa_merchant_key'] ); ?>" />
			 </td>
		 </tr>
		 <tr class="gateway gateway_mpesa" 
			 <?php
				if ( $gateway != 'mpesa' ) {
					?>
				style="display: none;"<?php } ?>>
			 <th scope="row" valign="top">
				 <label for="mpesa_debug"><?php _e( 'MPesa Debug Mode', 'pmpro-mpesa' ); ?>:</label>
			 </th>
			 <td>
				 <select name="mpesa_debug">
					 <option value="1" 
				 <?php
					if ( isset( $values['mpesa_debug'] ) && $values['mpesa_debug'] ) {
						?>
							selected="selected"<?php } ?>><?php _e( 'On', 'pmpro-mpesa' ); ?>
					</option>
					<option value="0" 
						<?php
						if ( isset( $values['mpesa_debug'] ) && ! $values['mpesa_debug'] ) {
							?>
							selected="selected"<?php } ?>><?php _e( 'Off', 'pmpro-mpesa' ); ?>
					</option>
				 </select>
			 </td>
		 </tr>
		<tr class="gateway gateway_mpesa" 
			<?php
			if ( $gateway != 'mpesa' ) {
				?>
			style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="mpesa_passphrase"><?php _e( 'MPesa PassPhrase', 'pmpro-mpesa' ); ?>:</label>
			</th>
			<td>
				<input id="mpesa_passphrase" name="mpesa_passphrase" value="<?php echo esc_attr( $values['mpesa_passphrase'] ); ?>" /> &nbsp;<small><?php _e( 'A passphrase is now required for all transactions.', 'pmpro-mpesa' ); ?></small>
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

		if ( apply_filters( 'pmpro_mpesa_hide_update_billing_button', true ) ) {
		?>
		<script>
			jQuery(document).ready(function(){
				jQuery('.pmpro_submit').hide();
			});
		</script>
		<?php
		}
		echo sprintf( __( "If you need to update your billing details, please login to your %s account to update these credentials. Selecting the update button below will automatically redirect you to MPesa.", 'pmpro-mpesa'), "<a href='https://mpesa.io' target='_blank'>MPesa</a>" );
	}

	/**
	 * Show information before PMPro's checkout button.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_before_submit_button() {
		global $gateway, $pmpro_requirebilling;

		// Bail if gateway isn't MPesa.
		if ( $gateway != 'mpesa' ) {
			return;
		}

		// see if Pay By Check Add On is active, if it's selected let's hide the MPesa information.
		if ( defined( 'PMPROPBC_VER' ) ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() { 
					jQuery('input:radio[name=gateway]').on( 'click', function() { 
						 var val = jQuery(this).val();

						 if ( val === 'check' ) {
							 jQuery( '#pmpro_mpesa_before_checkout' ).hide();
						 } else {
							 jQuery( '#pmpro_mpesa_before_checkout' ).show();
						 }
					});
				});	
			</script>
			<?php } ?>

		<div id="pmpro_mpesa_before_checkout" style="text-align:center;">
			<span id="pmpro_mpesa_checkout" 
			<?php
			if ( $gateway != 'mpesa' || ! $pmpro_requirebilling ) {
				?>
				style="display: none;"<?php } ?>>
				<input type="hidden" name="submit-checkout" value="1" />
				   <?php echo '<strong>' . __( 'NOTE:', 'pmpro-mpesa' ) . '</strong> ' . __( 'if changing a subscription it may take a minute or two to reflect. Please also login to your MPesa account to ensure the old subscription is cancelled.', 'pmpro-mpesa' ); ?>
				<p><img src="<?php echo plugins_url( 'img/mpesa_logo.png', __DIR__ ); ?>" width="100px" /></p>
			</span>
		</div>
			<?php
	}

	/**
	 * Instead of change membership levels, send users to MPesa to pay.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_before_change_membership_level( $user_id, $morder ) {
		global $discount_code_id, $wpdb;

		// if no order, no need to pay
		if ( empty( $morder ) ) {
			return;
		}

		// bail if the current gateway is not set to MPesa.
		if ( 'mpesa' != $morder->gateway ) {
			return;
		}

		$morder->user_id = $user_id;
		$morder->saveOrder();

		// if global is empty by query is available.
		if ( empty( $discount_code_id ) && isset( $_REQUEST['discount_code'] ) ) {
			$discount_code_id = $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_discount_codes WHERE code = '" . esc_sql( sanitize_text_field( $_REQUEST['discount_code'] ) ) . "'" );
		}

		// save discount code use
		if ( ! empty( $discount_code_id ) ) {
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO $wpdb->pmpro_discount_codes_uses 
					(code_id, user_id, order_id, timestamp) 
					VALUES( %d , %d, %d, %s )",
					$discount_code_id,
					$user_id,
					$morder->id,
					current_time( 'mysql' )
				)
			);
		}

		do_action( 'pmpro_before_send_to_mpesa', $user_id, $morder );

		$morder->Gateway->sendToMPesa( $morder );
	}

	/**
	 * Send traffic to wp-admin/admin-ajax.php?action=pmpro_mpesa_itn_handler to the itn handler
	 * 
	 * @since 1.0.0
	 */
	static function wp_ajax_pmpro_mpesa_itn_handler() {
		require_once PMPRO_MPESA_DIR . 'services/mpesa_itn_handler.php';
		exit;
	}

	function process( &$order ) {

		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		// clean up a couple values
		$order->payment_type = 'MPesa';
		$order->CardType     = '';
		$order->cardtype     = '';
		
		$order->status = "review";
		$order->saveOrder();

		return true;
	}

	/**
	 * @param $order
	 */
	function sendToMPesa( &$order ) {
		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		$order->payment_type = 'MPesa';
		$order->CardType = "";
		$order->cardtype = "";
		$order->ProfileStartDate = date_i18n( 'Y-m-d', current_time( 'timestamp' ) );

		// taxes on initial payment
		$initial_payment     = $order->InitialPayment;
		$initial_payment_tax = $order->getTaxForPrice( $initial_payment );
		$initial_payment     = round( (float) $initial_payment + (float) $initial_payment_tax, 2 );

		// taxes on the amount
		$amount          = $order->PaymentAmount;
		$amount_tax      = $order->getTaxForPrice( $amount );
		$order->subtotal = $amount;
		$amount          = round( (float) $amount + (float) $amount_tax, 2 );

		// merchant details
		$merchant_id  = get_option( 'pmpro_mpesa_merchant_id' );
		$merchant_key = get_option( 'pmpro_mpesa_merchant_key' );

		// build MPesa Redirect
		$environment = get_option( 'pmpro_gateway_environment' );
		if ( 'sandbox' === $environment || 'beta-sandbox' === $environment ) {
			$mpesa_url = 'https://sandbox.mpesa.co.za/eng/process';
		} else {
			$mpesa_url = 'https://www.mpesa.co.za/eng/process';
		}

		$data = array(
			'merchant_id'   => $merchant_id,
			'merchant_key'  => $merchant_key,
			'return_url'    => pmpro_url( 'confirmation', '?level=' . $order->membership_level->id ),
			'cancel_url'    => pmpro_url( 'levels' ),
			'notify_url'    => admin_url( 'admin-ajax.php' ) . '?action=pmpro_mpesa_itn_handler',
			'name_first'    => $order->FirstName,
			'name_last'     => $order->LastName,
			'email_address' => $order->Email,
			'm_payment_id'  => $order->code,
			'amount'        => $initial_payment,
			'item_name'     => html_entity_decode( substr( $order->membership_level->name . ' at ' . get_bloginfo( 'name' ), 0, 99 ) ),
			'custom_int1'   => $order->user_id,
		);		

		$cycles = $order->membership_level->billing_limit;

		if( ! empty( $order->BillingFrequency ) ) {
			// convert PMPro cycle_number and period into a MPesa frequency
			switch ( $order->BillingPeriod ) {
				case 'Month':
					$frequency = '3';
					break;

				case 'Year':
					$frequency = '6';
					break;
			}
		}

		// Add subscription data
		if ( ! empty( $frequency ) ) {
			// $data['m_subscription_id'] = /*$order->getRandomCode()*/$order->code;
			$data['custom_str1']       = $order->ProfileStartDate;
			$data['subscription_type'] = 1;
			$data['billing_date']      = apply_filters( 'pmpro_profile_start_date', $order->ProfileStartDate, $order );
			$data['recurring_amount']  = $amount;
			$data['frequency']         = $frequency;
			$data['cycles']            = $cycles == 0 ? 0 : $cycles + 1;

			// Remove time parameter if it's set.
			$data['billing_date'] = str_replace( 'T0:0:0', '', $data['billing_date'] );

			// filter order before subscription. use with care.
			$order = apply_filters( 'pmpro_subscribe_order', $order, $this );
		}

		$data = apply_filters( 'pmpro_mpesa_data', $data, $order );

		$order->status                      = 'token';
		$order->payment_transaction_id      = $order->code;
		$order->subscription_transaction_id = $order->code;
		$order->subtotal = $order->InitialPayment;
		$order->tax = $initial_payment_tax;
		$order->total = $initial_payment;		

		// Save the order before redirecting to MPesa.
		$order->saveOrder();

		$pfOutput  = '';
		$pffOutput = '';

		foreach ( $data  as $key => $val ) {
			$pffOutput .= $key . '=' . urlencode( trim( $val ) ) . '&';
		}

		// Remove last ampersand
		$passPhrase = get_option( 'pmpro_mpesa_passphrase' );

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
		$mpesa_url .= '?' . $pffOutput . '&signature=' . $signature . '&user_agent=Paid Memberships Pro ' . PMPRO_VERSION;

		wp_redirect( $mpesa_url );
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

	function cancel( &$order ) {

		// Check to see if the order has a token and try to cancel it at the gateway. Only recurring subscriptions should have a token.
		if ( ! empty( $order->paypal_token ) && ! empty( $order->subscription_transaction_id ) ) {

			// cancel order status immediately.
			$order->updateStatus( 'cancelled' );

			// check if we are getting an ITN notification which means it's already cancelled within MPesa.
			if ( ! empty( $_POST['payment_status'] ) && $_POST['payment_status'] == 'CANCELLED' ) {
				return true;
			}

			$token = $order->paypal_token;

			$hashArray  = array();
			$passphrase = get_option( 'pmpro_mpesa_passphrase' );

			$hashArray['version']     = 'v1';
			$hashArray['merchant-id'] = get_option( 'pmpro_mpesa_merchant_id' );
			$hashArray['passphrase']  = $passphrase;
			$hashArray['timestamp']   = date( 'Y-m-d' ) . 'T' . date( 'H:i:s' );

			$orderedPrehash = $hashArray;

			ksort( $orderedPrehash );

			$signature = md5( http_build_query( $orderedPrehash ) );

			$domain = 'https://api.mpesa.co.za';

			$url = $domain . '/subscriptions/' . $token . '/cancel';

			$environment = get_option( 'pmpro_gateway_environment' );

			if ( 'sandbox' === $environment || 'beta-sandbox' === $environment ) {
				$url = $url . '?testing=true';
			}

			$response = wp_remote_post(
				$url,
				array(
					'method'  => 'PUT',
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

} //end of class
