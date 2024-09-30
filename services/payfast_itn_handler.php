<?php
/**
 * Based on the scripts by Ron Darby shared at
 * https://www.payfast.co.za/shopping-carts/paid-memberships-pro/
 *
 * @author     Ron Darby - PayFast
 * @copyright  2009-2014 PayFast (Pty) Ltd
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */


// Bail if PMPro or the PayFast add on is not active
if ( ! defined( 'PMPRO_DIR' ) || ! defined( 'PMPRO_PAYFAST_DIR' ) ) {
	error_log( __( 'Paid Memberships Pro and the PMPro PayFast Add On must be activated for the PMPro PayFast ITN handler to function.', 'pmpro-payfast' ) );
	exit;
}

define( 'PMPROPF_SOFTWARE_NAME', 'Paid Memberships Pro' );
define( 'PMPROPF_SOFTWARE_VER', PMPRO_VERSION );
define( 'PMPROPF_MODULE_NAME', 'PayFast-PaidMembershipsPro' );
define( 'PMPROPF_MODULE_VER', '1.0' );

// Features
// - PHP
$pfFeatures = 'PHP ' . phpversion() . ';';

// Create user agrent
define( 'PMPROPF_USER_AGENT', PMPROPF_SOFTWARE_NAME . '/' . PMPROPF_SOFTWARE_VER . ' (' . trim( $pfFeatures ) . ') ' . PMPROPF_MODULE_NAME . '/' . PMPROPF_MODULE_VER );
// General Defines
define( 'PMPROPF_TIMEOUT', 15 );
define( 'PMPROPF_EPSILON', 0.01 );
// Messages
// Error
define( 'PMPROPF_ERR_AMOUNT_MISMATCH', __( 'Amount mismatch', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_BAD_ACCESS', __( 'Bad access of page', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_BAD_SOURCE_IP', __( 'Bad source IP address', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_CONNECT_FAILED', __( 'Failed to connect to PayFast', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_INVALID_SIGNATURE', __( 'Security signature mismatch', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_MERCHANT_ID_MISMATCH', __( 'Merchant ID mismatch', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_NO_SESSION', __( 'No saved session found for ITN transaction', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_ORDER_ID_MISSING_URL', __( 'Order ID not present in URL', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_ORDER_ID_MISMATCH', __( 'Order ID mismatch', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_ORDER_INVALID', __( 'This order ID is invalid', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_ORDER_NUMBER_MISMATCH', __( 'Order Number mismatch', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_ORDER_PROCESSED', __( 'This order has already been processed', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_PDT_FAIL', __( 'PDT query failed', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_PDT_TOKEN_MISSING', __( 'PDT token not present in URL', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_SESSIONID_MISMATCH', __( 'Session ID mismatch', 'pmpro-payfast' ) );
define( 'PMPROPF_ERR_UNKNOWN', __( 'Unkown error occurred', 'pmpro-payfast' ) );
	// General
define( 'PMPROPF_MSG_OK', __( 'Payment was successful', 'pmpro-payfast' ) );
define( 'PMPROPF_MSG_FAILED', __( 'Payment has failed', 'pmpro-payfast' ) );
define(
	'PMPROPF_MSG_PENDING',
	__( 'The payment is pending. Please note, you will receive another Instant', 'pmpro-payfast' ) .
	__( ' Transaction Notification when the payment status changes to', 'pmpro-payfast' ) .
	__( ' "Completed", or "Failed"', 'pmpro-payfast' )
);

// some globals
global $wpdb, $gateway_environment, $logstr;
$logstr = '';   // will put debug info here and write to ipnlog.txt
// Variable Initialization
$pfError = false;
$pfErrMsg = '';
$pfDone = false;
$pfData = array();
$pfHost = ( ( $gateway_environment == 'sandbox' ) ? 'sandbox' : 'www' ) . '.payfast.co.za';
$pfOrderId = '';
$pfParamString = '';
$initial_payment_status = '';
pmpro_payfast_itnlog( __( 'PayFast ITN call received', 'pmpro-payfast' ) );

// Notify PayFast that information has been received
if ( ! $pfError && ! $pfDone ) {
	header( 'HTTP/1.0 200 OK' );
	flush();
}

// Get data sent by PayFast
if ( ! $pfError && ! $pfDone ) {
	pmpro_payfast_itnlog( __( 'Get posted data', 'pmpro-payfast' ) );
	// Posted variables from ITN
	$pfData = pmpro_pfGetData();
	$morder = new MemberOrder( $pfData['m_payment_id'] );
	$morder->getMembershipLevel();
	$morder->getUser();

	pmpro_payfast_itnlog( __( 'PayFast Data: ', 'pmpro-payfast' ) . print_r( $pfData, true ) );
	if ( $pfData === false ) {
		$pfError = true;
		$pfErrMsg = PMPROPF_ERR_BAD_ACCESS;
	}
}

// Verify security signature
if ( ! $pfError && ! $pfDone ) {
	pmpro_payfast_itnlog( __( 'Verify security signature', 'pmpro-payfast' ) );
	$passPhrase = get_option( 'pmpro_payfast_passphrase' );
	$pfPassPhrase = empty( $passPhrase ) ? null : $passPhrase;
	// If signature different, log for debugging
	if ( ! pmpro_pfValidSignature( $pfData, $pfParamString, $pfPassPhrase ) ) {
		$pfError = true;
		$pfErrMsg = PMPROPF_ERR_INVALID_SIGNATURE;
	}
}
// Verify source IP (If not in debug mode)
if ( ! $pfError && ! $pfDone && ( ! defined( 'PMPROPF_DEBUG' ) || ! get_option( 'pmpro_payfast_debug' ) ) ) {
	pmpro_payfast_itnlog( __( 'Verify source IP', 'pmpro-payfast' ) );
	if ( ! pmpro_pfValidIP( $_SERVER['REMOTE_ADDR'] ) ) {
		$pfError = true;
		$pfErrMsg = PMPROPF_ERR_BAD_SOURCE_IP;
	}
}
// Verify data received
if ( ! $pfError ) {
	pmpro_payfast_itnlog( __( 'Verify data received', 'pmpro-payfast' ) );
	$pfValid = pmpro_pfValidData( $pfHost, $pfParamString );
	if ( ! $pfValid ) {
		$pfError = true;
		$pfErrMsg = PMPROPF_ERR_BAD_ACCESS;
	}
}

// Check data against internal order - Temporarily disabling this as it doesn't work with levels with different amounts.
if ( ! $pfError && ! $pfDone && $pfData['payment_status'] == 'COMPLETE' ) {
	// Only check initial orders.
	if ( empty( $pfData['token'] ) || strtotime( $pfData['custom_str1'] ) > strtotime( gmdate( 'Y-m-d', current_time( 'timestamp' ) ) . '- 2 days' ) ) {
		if ( ! pmpro_pfAmountsEqual( $pfData['amount_gross'], $morder->total ) ) {
			pmpro_payfast_itnlog( __( 'Amount Returned: ', 'pmpro-payfast' ) . $pfData['amount_gross'] );
			$pfError = true;
			$pfErrMsg = PMPROPF_ERR_AMOUNT_MISMATCH;
		}
	}
}

// Check status and update order
if ( ! $pfError && ! $pfDone ) {
	if ( $pfData['payment_status'] == 'COMPLETE' && ! empty( $pfData['token'] ) ) {
		$txn_id = $pfData['m_payment_id'];
		$subscr_id = $pfData['token'];
		// custom_str1 is the date of the initial order in gmt
		if ( strtotime( $pfData['custom_str1'] ) > strtotime( gmdate( 'Y-m-d', current_time( 'timestamp' ) ) . '- 2 days' ) ) {
			// Initial payment.
			// If there is no amount1, this membership has a trial, and we need to update membership/etc
			$amount = $pfData['amount_gross'];

			// trial, get the order
			$morder = new MemberOrder( $pfData['m_payment_id'] );
			$morder->paypal_token = $pfData['token'];
			$morder->getMembershipLevel();
			$morder->getUser();
			// no txn_id on these, so let's use the subscr_id
			$txn_id = $pfData['m_payment_id'];
			// update membership
			if ( pmpro_itnChangeMembershipLevel( $txn_id, $morder ) ) {
				pmpro_payfast_itnlog( 'Checkout processed (' . $morder->code . ') success!' );
			} else {
				pmpro_payfast_itnlog( __( "ERROR: Couldn't change level for order (", 'pmpro-payfast' ) . $morder->code . __( ').', 'pmpro-payfast' ) );
			}

			pmpro_payfast_ipnExit();
		} else {
			// Subscription Payment
			$last_subscr_order = new MemberOrder();
			if ( $last_subscr_order->getLastMemberOrderBySubscriptionTransactionID( $pfData['m_payment_id'] ) ) {
				$last_subscr_order->paypal_token = $pfData['token'];
				pmpro_ipnSaveOrder( $pfData['pf_payment_id'], $last_subscr_order );
			} else {
				pmpro_payfast_itnlog( __( "ERROR: Couldn't find last order for this recurring payment (", 'pmpro-payfast' ) . $pfData['m_payment_id'] . __( ').', 'pmpro-payfast' ) );
			}
			pmpro_payfast_ipnExit();
		}
	}
}

if ( $pfData['payment_status'] == 'CANCELLED' ) {
	if ( function_exists( 'pmpro_handle_subscription_cancellation_at_gateway' ) ) {
		// Using PMPro v3.0+, so we have a helper function to handle subscription cancellations.
		pmpro_payfast_itnlog( pmpro_handle_subscription_cancellation_at_gateway( $pfData['m_payment_id'], 'payfast', $gateway_environment ) );
		pmpro_payfast_ipnExit();
	}
	// PMPro version < 3.0. Use the legacy method of handling subscription cancellations.
	// find last order
	$last_subscr_order = new MemberOrder();
	if ( $last_subscr_order->getLastMemberOrderBySubscriptionTransactionID( $pfData['m_payment_id'] ) == false ) {
		pmpro_payfast_itnlog( __( "ERROR: Couldn't find this order to cancel (subscription_transaction_id=", 'pmpro-payfast' ) . $pfData['m_payment_id'] . __( ').', 'pmpro-payfast' ) );
		pmpro_payfast_ipnExit();
	} else {
		// found order, let's cancel the membership
		$user = get_userdata( $last_subscr_order->user_id );
		if ( empty( $user ) || empty( $user->ID ) ) {
			pmpro_payfast_itnlog( __( 'ERROR: Could not cancel membership. No user attached to order #', 'pmpro-payfast' ) . $last_subscr_order->id . __( ' with subscription transaction id = ', 'pmpro-payfast' ) . $last_subscr_order->subscription_transaction_id . __( '.', 'pmpro-payfast' ) );
		} else {

			if ( $last_subscr_order->status == 'cancelled' ) {
				pmpro_payfast_itnlog( __( "We've already processed this cancellation. Probably originated from WP/PMPro. (Order #", 'pmpro-payfast' ) . $last_subscr_order->id . __( ', Subscription Transaction ID #', 'pmpro-payfast' ) . $pfData['m_payment_id'] . __( ')', 'pmpro-payfast' ) );
			} elseif ( ! pmpro_hasMembershipLevel( $last_subscr_order->membership_id, $user->ID ) ) {
				pmpro_payfast_itnlog( __( 'This user has a different level than the one associated with this order. Their membership was probably changed by an admin or through an upgrade/downgrade. (Order #', 'pmpro-payfast' ) . $last_subscr_order->id . __( ', Subscription Transaction ID #', 'pmpro-payfast' ) . $pfData['m_payment_id'] . __( ')', 'pmpro-payfast' ) );
			} else {
				// if the initial payment failed, cancel with status error instead of cancelled
				if ( $initial_payment_status === 'Failed' ) {
					pmpro_cancelMembershipLevel( $last_subsc_order->membership_id, $last_subscr_order->user_id, 'error' );
				} else {
					// pmpro_changeMembershipLevel( 0, $last_subscr_order->user_id, 'cancelled' );
					$last_subscr_order->updateStatus( 'cancelled' );
					global $wpdb;
					$query = $wpdb->prepare(
						"UPDATE $wpdb->pmpro_memberships_orders 
						SET status = 'cancelled' 
						WHERE subscription_transaction_id = %d",
						$pfData['m_payment_id']
					);
					$wpdb->query( $query );
					$sqlQuery = $wpdb->prepare(
						"UPDATE $wpdb->pmpro_memberships_users 
						SET status = 'cancelled' 
						WHERE user_id = %d
						AND membership_id = %d
						AND status = 'active'",
						$last_subscr_order->user_id,
						$last_subscr_order->membership_id
					);
					$wpdb->query( $sqlQuery );
				}
				pmpro_payfast_itnlog( __( 'Cancelled membership for user with id = ', 'pmpro-payfast' ) . $last_subscr_order->user_id . __( '. Subscription transaction id = ', 'pmpro-payfast' ) . $pfData['m_payment_id'] . __( '.', 'pmpro-payfast' ) );
				// send an email to the member
				$myemail = new PMProEmail();
				$myemail->sendCancelEmail( $user );
				// send an email to the admin
				$myemail = new PMProEmail();
				$myemail->sendCancelAdminEmail( $user, $last_subscr_order->membership_id );
			}
		}
		pmpro_payfast_ipnExit();
	}
}

pmpro_payfast_itnlog( __( 'Check status and update order', 'pmpro-payfast' ) );
$transaction_id = $pfData['pf_payment_id'];
$morder = new MemberOrder( $pfData['m_payment_id'] );
$morder->getMembershipLevel();
$morder->getUser();
pmpro_payfast_itnlog( __( 'check token', 'pmpro-payfast' ) );
	// if ( ! empty( $pfData['token'] ) )
	// {
switch ( $pfData['payment_status'] ) {
	case 'COMPLETE':
		$morder = new MemberOrder( $pfData['m_payment_id'] );
		$morder->getMembershipLevel();
		$morder->getUser();
		// update membership
		if ( pmpro_itnChangeMembershipLevel( $transaction_id, $morder ) ) {
			pmpro_payfast_itnlog( 'Checkout processed (' . $morder->code . ') success!' );
		} else {
			pmpro_payfast_itnlog( __( "ERROR: Couldn't change level for order (", 'pmpro-payfast' ) . $morder->code . ').' );
		}
		break;
	case 'FAILED':
		pmpro_payfast_itnlog( __( 'ERROR: ITN from PayFast for order (', 'pmpro-payfast' ) . $morder->code . __( ') Failed.', 'pmpro-payfast' ) );
		break;
	case 'PENDING':
		pmpro_payfast_itnlog( __( 'ERROR: ITN from PayFast for order (', 'pmpro-payfast' ) . $morder->code . ') Pending.' );
		break;
	default:
		pmpro_payfast_itnlog( __( 'ERROR: Unknown error for order (', 'pmpro-payfast' ) . $morder->code . ').' );
		break;
}
	// }
	// If an error occurred
if ( $pfError ) {

	pmpro_payfast_itnlog( __( 'Error occurred: ', 'pmpro-payfast' ) . $pfErrMsg );
}

pmpro_payfast_ipnExit();

/*
	Add message to ipnlog string
*/
function pmpro_payfast_itnlog( $s ) {
	 global $logstr;
	$logstr .= "\t" . $s . "\n";
}

/*
	Output ipnlog and exit;
*/
function pmpro_payfast_ipnExit() {
	global $logstr;
	// for log
	if ( $logstr ) {
		$logstr = __( 'Logged On: ', 'pmpro-payfast' ) . date( 'm/d/Y H:i:s' ) . "\n" . $logstr . "\n-------------\n";
		echo esc_html( $logstr );

		//Log to file or email, 
		if ( get_option( 'pmpro_payfast_debug' ) || ( defined( 'PMPROPF_DEBUG' ) && PMPROPF_DEBUG === 'log' ) ) {
			// Let's create the file and add a random suffix to it, to tighten up security.
			$file_suffix = substr( md5( get_option( 'pmpro_payfast_merchant_id', true ) ), 0, 10 );
			$filename = 'payfast_itn_' . $file_suffix . '.txt';
			$logfile = apply_filters( 'pmpro_payfast_itn_logfile', PMPRO_PAYFAST_DIR . '/logs/'. $filename );

			// Make the /logs directory if it doesn't exist
			if ( ! file_exists( PMPRO_PAYFAST_DIR . '/logs' ) ) {
				mkdir( PMPRO_PAYFAST_DIR . '/logs', 0700 );
			}

			// If the log file doesn't exist let's create it.
			if ( ! file_exists( $logfile ) ) {
				// create a blank text file
				file_put_contents( $logfile, '' );
			}
						
			$loghandle = fopen( $logfile, "a+" );
			fwrite( $loghandle, $logstr );
			fclose( $loghandle );
		} elseif ( defined( 'PMPROPF_DEBUG' ) && false !== PMPROPF_DEBUG ) {
			// Send via email.
			$log_email = strpos( PMPROPF_DEBUG, '@' ) ? PMPROPF_DEBUG : get_option( 'admin_email' );
			wp_mail( $log_email, get_option( 'blogname' ) . ' PayFast Webhook Log', nl2br( esc_html( $logstr ) ) );
		}
	}
	exit;
}

/**
 * Change the membership level. We also update the membership order to include filtered values.
 * NOTE: Some legacy code has stayed within this function to ensure backwards compatibility.
 * 
 */
function pmpro_itnChangeMembershipLevel( $txn_id, &$morder ) {

	// update order status and transaction ids
	$morder->payment_transaction_id = $txn_id;

	if ( ! empty( $_POST['token'] ) ) {
		$morder->subscription_transaction_id = sanitize_text_field( $_POST['m_payment_id'] );
	} else {
		$morder->subscription_transaction_id = '';
	}
	$morder->saveOrder(); // Temporarily save the order before processing it.

	// Change level and complete the order.
	pmpro_pull_checkout_data_from_order( $morder );
	return pmpro_complete_async_checkout( $morder );
	
}

function pmpro_ipnSaveOrder( $txn_id, $last_order ) {
	global $wpdb;
	// check that txn_id has not been previously processed
	$old_txn = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT payment_transaction_id 
			FROM $wpdb->pmpro_membership_orders 
			WHERE payment_transaction_id = %d 
			LIMIT 1",
			$txn_id
		)
	);
	if ( empty( $old_txn ) ) {
		// hook for successful subscription payments
		// do_action("pmpro_subscription_payment_completed");
		// save order
		$morder = new MemberOrder();
		$morder->user_id = $last_order->user_id;
		$morder->membership_id = $last_order->membership_id;
		$morder->payment_transaction_id = $txn_id;
		$morder->subscription_transaction_id = $last_order->subscription_transaction_id;
		$morder->gateway = $last_order->gateway;
		$morder->gateway_environment = $last_order->gateway_environment;
		$morder->paypal_token = $last_order->paypal_token;
		// Payment Status
		$morder->status = 'success'; // We have confirmed that and thats the reason we are here.
		// Payment Type.
		$morder->payment_type = $last_order->payment_type;
		// set amount based on which PayPal type
		if ( $last_order->gateway == 'payfast' ) {
			$morder->InitialPayment = sanitize_text_field( $_POST['amount_gross'] );    // not the initial payment, but the class is expecting that
			$morder->PaymentAmount = sanitize_text_field( $_POST['amount_gross'] );
		}
		$morder->FirstName = sanitize_text_field( $_POST['name_first'] );
		$morder->LastName = sanitize_text_field( $_POST['name_last'] );
		$morder->Email = sanitize_email( $_POST['email_address'] );
		// get address info if appropriate
		if ( $last_order->gateway == 'payfast' ) {
			$morder->Address1 = get_user_meta( $last_order->user_id, 'pmpro_baddress1', true );
			$morder->City = get_user_meta( $last_order->user_id, 'pmpro_bcity', true );
			$morder->State = get_user_meta( $last_order->user_id, 'pmpro_bstate', true );
			$morder->CountryCode = 'ZA';
			$morder->Zip = get_user_meta( $last_order->user_id, 'pmpro_bzip', true );
			$morder->PhoneNumber = get_user_meta( $last_order->user_id, 'pmpro_bphone', true );

			if ( ! isset( $morder->billing ) ) {
				$morder->billing = new stdClass();
			}

			$morder->billing->name = sanitize_text_field( $_POST['name_first'] ) . ' ' . sanitize_text_field( $_POST['name_last'] );
			$morder->billing->street = get_user_meta( $last_order->user_id, 'pmpro_baddress1', true );
			$morder->billing->city = get_user_meta( $last_order->user_id, 'pmpro_bcity', true );
			$morder->billing->state = get_user_meta( $last_order->user_id, 'pmpro_bstate', true );
			$morder->billing->zip = get_user_meta( $last_order->user_id, 'pmpro_bzip', true );
			$morder->billing->country = get_user_meta( $last_order->user_id, 'pmpro_bcountry', true );
			$morder->billing->phone = get_user_meta( $last_order->user_id, 'pmpro_bphone', true );
			// get CC info that is on file
			$morder->cardtype = get_user_meta( $last_order->user_id, 'pmpro_CardType', true );
			$morder->accountnumber = hideCardNumber( get_user_meta( $last_order->user_id, 'pmpro_AccountNumber', true ), false );
			$morder->expirationmonth = get_user_meta( $last_order->user_id, 'pmpro_ExpirationMonth', true );
			$morder->expirationyear = get_user_meta( $last_order->user_id, 'pmpro_ExpirationYear', true );
			$morder->ExpirationDate = $morder->expirationmonth . $morder->expirationyear;
			$morder->ExpirationDate_YdashM = $morder->expirationyear . '-' . $morder->expirationmonth;
		}
		// figure out timestamp or default to none (today)
		// if(!empty($_POST['payment_date']))
		// $morder->timestamp = strtotime($_POST['payment_date']);
		// save
		$morder->saveOrder();
		$morder->getMemberOrderByID( $morder->id );
		// email the user their invoice
		$pmproemail = new PMProEmail();
		$pmproemail->sendInvoiceEmail( get_userdata( $last_order->user_id ), $morder );
		do_action( 'pmpro_subscription_payment_completed', $morder );

		pmpro_payfast_itnlog( __( 'New order (', 'pmpro-payfast' ) . $morder->code . __( ') created.', 'pmpro-payfast' ) );
		return true;
	} else {
		pmpro_payfast_itnlog( __( 'Duplicate Transaction ID: ', 'pmpro-payfast' ) . $txn_id );
		return true;
	}
}

/**
 * pfGetData
 * documentation reference - https://developers.payfast.co.za/documentation/#notify-page-itn
 * @uses pmpro_getParam - https://github.com/strangerstudios/paid-memberships-pro/blob/dev/includes/functions.php#L2260
 *
 * @author Jonathan Smit (PayFast.co.za)
 * @author Stranger Studios 2019 (paidmembershipspro.com)
 */
function pmpro_pfGetData() {
	
	$pfData = array();
    // Ensure that all posted data is used at the ITN stage
	$postedData = array_keys($_POST);

    // Sanitize all posted data
    foreach ( $postedData as $key ) {
		if ( $key != 'email_address' ) {
			$pfData[$key] = pmpro_getParam( $key, 'POST' );
		} else {
			$pfData[$key] = pmpro_getParam( $key, 'POST', '', 'sanitize_email' );
		}
	}

	// Return "false" if no data was received
	if ( sizeof( $pfData ) == 0 ) {
		return( false );
	} else {
		return( $pfData );
	}
}

/**
 * pfValidSignature
 *
 * @author Jonathan Smit (PayFast.co.za)
 */
function pmpro_pfValidSignature( $pfData = null, &$pfParamString = null, $passPhrase = null ) {
	 // Dump the submitted variables and calculate security signature
	foreach ( $pfData as $key => $val ) {
		if ( $key != 'signature' ) {
			$pfParamString .= $key . '=' . urlencode( $val ) . '&';
		} else {
			break;
		}
	}
	// Remove the last '&' from the parameter string
	$pfParamString = substr( $pfParamString, 0, -1 );

	if ( is_null( $passPhrase ) ) {
		$tempParamString = $pfParamString;
	} else {
		$tempParamString = $pfParamString . '&passphrase=' . urlencode( trim( $passPhrase ) );
	}
	
	$signature = md5( $tempParamString );
	$result = ( $pfData['signature'] == $signature );
	pmpro_payfast_itnlog( __( 'Signature Sent: ', 'pmpro-payfast' ) . $signature );
	pmpro_payfast_itnlog( __( 'Signature = ', 'pmpro-payfast' ) . ( $result ? __( 'valid', 'pmpro-payfast' ) : __( 'invalid', 'pmpro-payfast' ) ) );
	return( $result );
}

/**
 * pfValidData
 *
 * @author Jonathan Smit (PayFast.co.za)
 * @param $pfHost String Hostname to use
 * @param $pfParamString String Parameter string to send
 * @param $proxy String Address of proxy to use or NULL if no proxy
 */
function pmpro_pfValidData( $pfHost = 'www.payfast.co.za', $pfParamString = '', $pfProxy = null ) {
	pmpro_payfast_itnlog( __( 'Host = ', 'pmpro-payfast' ) . $pfHost );
	pmpro_payfast_itnlog( __( 'Params = ', 'pmpro-payfast' ) . $pfParamString );
	// Variable initialization
	$url = 'https://' . $pfHost . '/eng/query/validate';
	
	$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'sslverify' => false,
			'body' => $pfParamString,
			'timeout' => PMPROPF_TIMEOUT
		)
	);

	
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		pmpro_payfast_itnlog( 'Error validating data: ' . $error_message );
		die( 'Error validating data: ' . $error_message );
	}

	$body = wp_remote_retrieve_body( $response );

	pmpro_payfast_itnlog( $body );

	if ( $body === 'VALID' ) {
		return( true );
	} else {
		return( false );
	}
}

/**
 * pfValidIP
 *
 * @author Jonathan Smit (PayFast.co.za)
 * @param $sourceIP String Source IP address
 */
function pmpro_pfValidIP( $sourceIP ) {
	 // Variable initialization
	$validHosts = array(
		'www.payfast.co.za',
		'sandbox.payfast.co.za',
		'w1w.payfast.co.za',
		'w2w.payfast.co.za',
	);
	$validIps = array();
	foreach ( $validHosts as $pfHostname ) {
		$ips = gethostbynamel( $pfHostname );
		if ( $ips !== false ) {
			$validIps = array_merge( $validIps, $ips );
		}
	}
	// Remove duplicates
	$validIps = array_unique( $validIps );
	pmpro_payfast_itnlog( "Valid IPs:\n" . print_r( $validIps, true ) );
	if ( in_array( $sourceIP, $validIps ) ) {
		return( true );
	} else {
		return( false );
	}
}

/**
 * pfAmountsEqual
 *
 * Checks to see whether the given amounts are equal using a proper floating
 * point comparison with an Epsilon which ensures that insignificant decimal
 * places are ignored in the comparison.
 *
 * eg. 100.00 is equal to 100.0001
 *
 * @author Jonathan Smit (PayFast.co.za)
 * @param $amount1 Float 1st amount for comparison
 * @param $amount2 Float 2nd amount for comparison
 */
function pmpro_pfAmountsEqual( $amount1, $amount2 ) {
	if ( abs( floatval( $amount1 ) - floatval( $amount2 ) ) > PMPROPF_EPSILON ) {
		return( false );
	} else {
		return( true );
	}
}
