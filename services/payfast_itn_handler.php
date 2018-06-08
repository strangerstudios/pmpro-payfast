<?php
/**
 * Based on the scripts by Ron Darby shared at https://www.payfast.co.za/shopping-carts/paid-memberships-pro/
 * @author     Ron Darby - PayFast
 * @copyright  2009-2014 PayFast (Pty) Ltd
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
     //in case the file is loaded directly
    if(!defined("WP_USE_THEMES"))
    {
        global $isapage;
        $isapage = true;
        define('WP_USE_THEMES', false);
        require_once(dirname(__FILE__) . '/../../../../wp-load.php');
    }
	
	//Bail if PMPro or the PayFast add on is not active
	if(!defined('PMPRO_DIR') || !defined('PMPRO_PAYFAST_DIR')) {
		error_log(__('Paid Memberships Pro and the PMPro PayFast Add On must be activated for the PMPro PayFast ITN handler to function.', 'pmpro-payfast'));
		exit;
	}
    define( 'PF_SOFTWARE_NAME', 'Paid Membership Pro' );
    define( 'PF_SOFTWARE_VER',  '1.9.4.2');
    define( 'PF_MODULE_NAME', 'PayFast-PaidMembershipPro' );
    define( 'PF_MODULE_VER', '1.1.0' );
    define( 'PF_DEBUG', pmpro_getOption("payfast_debug") );
	
    // Features
    // - PHP
    $pfFeatures = 'PHP '. phpversion() .';';
    // - cURL
    if( in_array( 'curl', get_loaded_extensions() ) )
    {
        define( 'PF_CURL', '' );
        $pfVersion = curl_version();
        $pfFeatures .= ' curl '. $pfVersion['version'] .';';
    }
    else
        $pfFeatures .= ' nocurl;';
    // Create user agrent
    define( 'PF_USER_AGENT', PF_SOFTWARE_NAME .'/'. PF_SOFTWARE_VER .' ('. trim( $pfFeatures ) .') '. PF_MODULE_NAME .'/'. PF_MODULE_VER );
    // General Defines
    define( 'PF_TIMEOUT', 15 );
    define( 'PF_EPSILON', 0.01 );
    // Messages
        // Error
    define( 'PF_ERR_AMOUNT_MISMATCH', 'Amount mismatch' );
    define( 'PF_ERR_BAD_ACCESS', 'Bad access of page' );
    define( 'PF_ERR_BAD_SOURCE_IP', 'Bad source IP address' );
    define( 'PF_ERR_CONNECT_FAILED', 'Failed to connect to PayFast' );
    define( 'PF_ERR_INVALID_SIGNATURE', 'Security signature mismatch' );
    define( 'PF_ERR_MERCHANT_ID_MISMATCH', 'Merchant ID mismatch' );
    define( 'PF_ERR_NO_SESSION', 'No saved session found for ITN transaction' );
    define( 'PF_ERR_ORDER_ID_MISSING_URL', 'Order ID not present in URL' );
    define( 'PF_ERR_ORDER_ID_MISMATCH', 'Order ID mismatch' );
    define( 'PF_ERR_ORDER_INVALID', 'This order ID is invalid' );
    define( 'PF_ERR_ORDER_NUMBER_MISMATCH', 'Order Number mismatch' );
    define( 'PF_ERR_ORDER_PROCESSED', 'This order has already been processed' );
    define( 'PF_ERR_PDT_FAIL', 'PDT query failed' );
    define( 'PF_ERR_PDT_TOKEN_MISSING', 'PDT token not present in URL' );
    define( 'PF_ERR_SESSIONID_MISMATCH', 'Session ID mismatch' );
    define( 'PF_ERR_UNKNOWN', 'Unkown error occurred' );
        // General
    define( 'PF_MSG_OK', 'Payment was successful' );
    define( 'PF_MSG_FAILED', 'Payment has failed' );
    define( 'PF_MSG_PENDING',
        'The payment is pending. Please note, you will receive another Instant'.
        ' Transaction Notification when the payment status changes to'.
        ' "Completed", or "Failed"' );
    define( 'PMPRO_IPN_DEBUG', 'log' ); //this is called inside wp-config rather.
    
    //some globals
    global $wpdb, $gateway_environment, $logstr;
    $logstr = "";   //will put debug info here and write to ipnlog.txt
    // Variable Initialization
    $pfError = false;
    $pfErrMsg = '';
    $pfDone = false;
    $pfData = array();
    $pfHost = ( ( $gateway_environment == 'sandbox' ) ? 'sandbox' : 'www' ) . '.payfast.co.za';
    $pfOrderId = '';
    $pfParamString = '';
    ipnlog(  'PayFast ITN call received' );
    //// Notify PayFast that information has been received
    if( !$pfError && !$pfDone )
    {
        header( 'HTTP/1.0 200 OK' );
        flush();
    }
    
    //// Get data sent by PayFast
    if( !$pfError && !$pfDone )
    {
        ipnlog(  'Get posted data' );
        // Posted variables from ITN
        $pfData = pmpro_pfGetData();
        $morder = new MemberOrder( $pfData['m_payment_id'] );
        $morder->getMembershipLevel();
        $morder->getUser();
        ipnlog(  'PayFast Data: '. print_r( $pfData, true ) );
        if( $pfData === false )
        {
            $pfError = true;
            $pfErrMsg = PF_ERR_BAD_ACCESS;
        }
    }
    //// Verify security signature
    if( !$pfError && !$pfDone )
    {
        ipnlog(  'Verify security signature' );
        $passPhrase = pmpro_getOption( 'payfast_passphrase' );
        $pfPassPhrase = empty( $passPhrase ) ? null : $passPhrase;
        // If signature different, log for debugging
        if( !pmpro_pfValidSignature( $pfData, $pfParamString, $pfPassPhrase ) )
        {
            $pfError = true;
            $pfErrMsg = PF_ERR_INVALID_SIGNATURE;
        }
    }
    //// Verify source IP (If not in debug mode)
    if( !$pfError && !$pfDone && !PF_DEBUG )
    {
        ipnlog(  'Verify source IP' );
        if( !pmpro_pfValidIP( $_SERVER['REMOTE_ADDR'] ) )
        {
            $pfError = true;
            $pfErrMsg = PF_ERR_BAD_SOURCE_IP;
        }
    }
    //// Verify data received
    if( !$pfError )
    {
        ipnlog(  'Verify data received' );
        $pfValid = pmpro_pfValidData( $pfHost, $pfParamString );
        if( !$pfValid )
        {
            $pfError = true;
            $pfErrMsg = PF_ERR_BAD_ACCESS;
        }
    }

    //// Check data against internal order - Temporarily disabling this as it doesn't work with levels with different amounts.
    if( !$pfError && !$pfDone && $pfData['payment_status'] == 'COMPLETE' )
    {		
        // Only check initial orders.		
		if ( empty( $pfData['token'] ) || strtotime( $pfData['custom_str1'] ) > strtotime( gmdate( 'Y-m-d', current_time( 'timestamp' ) ). '- 2 days' ) )
        {
			if( !pmpro_pfAmountsEqual( $pfData['amount_gross'], $morder->total) )
			{
				ipnlog(  'Amount Returned: '.$pfData['amount_gross']."\n Amount in Cart:".$checkTotal );
				$pfError = true;
				$pfErrMsg = PF_ERR_AMOUNT_MISMATCH;
			}
        }
    }

    //// Check status and update order
    if( !$pfError && !$pfDone )
    {
        if ( $pfData['payment_status'] == 'COMPLETE' && !empty( $pfData['token'] ) )
        {
            $txn_id = $pfData['m_payment_id'];
            $subscr_id = $pfData['token'];
            // custom_str1 is the date of the initial order in gmt			
			if ( strtotime( $pfData['custom_str1'] ) > strtotime( gmdate( 'Y-m-d', current_time( 'timestamp' ) ). '- 2 days' ) )
            {
                // Initial payment.
				// If there is no amount1, this membership has a trial, and we need to update membership/etc
                $amount = $pfData['amount_gross'];
               
				//trial, get the order
				$morder = new MemberOrder( $pfData['m_payment_id'] );
				$morder->paypal_token = $pfData['token'];
				$morder->getMembershipLevel();
				$morder->getUser();
				//no txn_id on these, so let's use the subscr_id
				$txn_id = $pfData['m_payment_id'];
				//update membership
				if ( pmpro_itnChangeMembershipLevel( $txn_id, $morder ) ) {
					ipnlog( "Checkout processed (" . $morder->code . ") success!" );
				} else {
					ipnlog( "ERROR: Couldn't change level for order (" . $morder->code . ")." );
				}
					
                pmpro_ipnExit();
            } else {
				// Subscription Payment				
				$last_subscr_order = new MemberOrder();
                if ($last_subscr_order->getLastMemberOrderBySubscriptionTransactionID($pfData['m_payment_id'])) {
                    $last_subscr_order->paypal_token = $pfData['token'];
                    pmpro_ipnSaveOrder($pfData['pf_payment_id'], $last_subscr_order);                    
                } else {
                    ipnlog("ERROR: Couldn't find last order for this recurring payment (" . $pfData['m_payment_id'] . ").");
                }
                pmpro_ipnExit();
			}
        }
    }
	
	if ( $pfData['payment_status'] == 'CANCELLED' )
	{
		//find last order
		$last_subscr_order = new MemberOrder();
		if ( $last_subscr_order->getLastMemberOrderBySubscriptionTransactionID( $pfData['m_payment_id'] ) == false )
		{
			ipnlog( "ERROR: Couldn't find this order to cancel (subscription_transaction_id=" . $pfData['m_payment_id'] . ")." );
			pmpro_ipnExit();
		}
		else
		{
			//found order, let's cancel the membership
			$user = get_userdata( $last_subscr_order->user_id );
			if ( empty( $user ) || empty( $user->ID ) )
			{
				ipnlog( "ERROR: Could not cancel membership. No user attached to order #" . $last_subscr_order->id . " with subscription transaction id = " . $recurring_payment_id . "." );
			}
			else
			{
				
				if ( $last_subscr_order->status == "cancelled" )
				{
					ipnlog( "We've already processed this cancellation. Probably originated from WP/PMPro. (Order #" . $last_subscr_order->id . ", Subscription Transaction ID #" . $pfData['m_payment_id'] . ")" );
				}
				elseif ( ! pmpro_hasMembershipLevel( $last_subsc_order->membership_id, $user->ID ) )
				{
					ipnlog( "This user has a different level than the one associated with this order. Their membership was probably changed by an admin or through an upgrade/downgrade. (Order #" . $last_subscr_order->id . ", Subscription Transaction ID #" . $pfData['m_payment_id'] . ")" );
				}
				else
				{
					//if the initial payment failed, cancel with status error instead of cancelled
					if ( $initial_payment_status === "Failed" )
					{
						pmpro_changeMembershipLevel( 0, $last_subscr_order->user_id, 'error' );
					}
					else
					{
					   // pmpro_changeMembershipLevel( 0, $last_subscr_order->user_id, 'cancelled' );
						$last_subscr_order->updateStatus( "cancelled" );
						global $wpdb;
						$query = "UPDATE $wpdb->pmpro_memberships_orders SET status = 'cancelled' WHERE subscription_transaction_id = " . $pfData['m_payment_id'];
						$wpdb->query($query);
						$sqlQuery = "UPDATE $wpdb->pmpro_memberships_users SET status = 'cancelled' WHERE user_id = '" . $last_subscr_order->user_id . "' AND membership_id = '" . $last_subscr_order->membership_id . "' AND status = 'active'";
						$wpdb->query($sqlQuery); 
					}
					ipnlog( "Cancelled membership for user with id = " . $last_subscr_order->user_id . ". Subscription transaction id = " . $pfData['m_payment_id'] . "." );
					//send an email to the member
					$myemail = new PMProEmail();
					$myemail->sendCancelEmail( $user );
					//send an email to the admin
					$myemail = new PMProEmail();
					$myemail->sendCancelAdminEmail( $user, $last_subscr_order->membership_id );
				}
			}
			pmpro_ipnExit();
		}
	}
	ipnlog(  'Check status and update order' );
	$transaction_id = $pfData['pf_payment_id'];
	$morder = new MemberOrder( $pfData['m_payment_id' ]);
	$morder->getMembershipLevel();
	$morder->getUser();
    ipnlog( 'check token' );
	// if ( ! empty( $pfData['token'] ) )
	// {
		switch ($pfData['payment_status']) {
			case 'COMPLETE':
				$morder = new MemberOrder( $pfData['m_payment_id'] );
				$morder->getMembershipLevel();
                $morder->getUser();
				//update membership
				if ( pmpro_itnChangeMembershipLevel( $transaction_id, $morder ) )
				{
					ipnlog( "Checkout processed (" . $morder->code . ") success!" );
				}
				else
				{
					ipnlog( "ERROR: Couldn't change level for order (" . $morder->code . ")." );
				}
				break;
			case 'FAILED':
				ipnlog( "ERROR: ITN from PayFast for order (" . $morder->code . ") Failed." );
				break;
			case 'PENDING':
				ipnlog( "ERROR: ITN from PayFast for order (" . $morder->code . ") Pending." );
				break;
			default:
				ipnlog( "ERROR: Unknown error for order (" . $morder->code . ")." );
				break;
		}
	// }
    // If an error occurred
    if( $pfError )
    {
        ipnlog( 'Error occurred: '. $pfErrMsg );
    }
    pmpro_ipnExit();
    /*
        Add message to ipnlog string
    */
    function ipnlog($s)
    {
        global $logstr;
        $logstr .= "\t" . $s . "\n";
    }
    /*
        Output ipnlog and exit;
    */
    function pmpro_ipnExit()
    {
        global $logstr;
        //for log
        if($logstr)
        {
            $logstr = "Logged On: " . date("m/d/Y H:i:s") . "\n" . $logstr . "\n-------------\n";
            //log?
            if( PF_DEBUG )
            {   
                echo $logstr;
                $loghandle = fopen( PMPRO_PAYFAST_DIR . "/logs/payfast_itn.txt", "a+" );
                fwrite( $loghandle, $logstr );
                fclose( $loghandle );				
            }
        }
        exit;
    }
    /*
        Change the membership level. We also update the membership order to include filtered valus.
    */
    function pmpro_itnChangeMembershipLevel($txn_id, &$morder)
    {
        global $wpdb;
        //filter for level
        $morder->membership_level = apply_filters("pmpro_ipnhandler_level", $morder->membership_level, $morder->user_id);
        //fix expiration date
        if(!empty($morder->membership_level->expiration_number))
        {
            $enddate = "'" . date("Y-m-d", strtotime("+ " . $morder->membership_level->expiration_number . " " . $morder->membership_level->expiration_period)) . "'";
        }
        else
        {
            $enddate = "NULL";
        }
        //get discount code     (NOTE: but discount_code isn't set here. How to handle discount codes for PayPal Standard?)
        $morder->getDiscountCode();
        if( !empty( $morder->discount_code ) )
        {
            //update membership level
            $morder->getMembershipLevel(true);
            $discount_code_id = $morder->discount_code->id;
        }
        else
            $discount_code_id = "";
        //set the start date to current_time('timestamp') but allow filters
        $startdate = apply_filters("pmpro_checkout_start_date", "'" . current_time('mysql') . "'", $morder->user_id, $morder->membership_level);
        //custom level to change user to
        $custom_level = array(
            'user_id' => $morder->user_id,
            'membership_id' => $morder->membership_level->id,
            'code_id' => $discount_code_id,
            'initial_payment' => $morder->membership_level->initial_payment,
            'billing_amount' => $morder->membership_level->billing_amount,
            'cycle_number' => $morder->membership_level->cycle_number,
            'cycle_period' => $morder->membership_level->cycle_period,
            'billing_limit' => $morder->membership_level->billing_limit,
            'trial_amount' => $morder->membership_level->trial_amount,
            'trial_limit' => $morder->membership_level->trial_limit,
            'startdate' => $startdate,
            'enddate' => $enddate);
        global $pmpro_error;
        if(!empty($pmpro_error))
        {
            echo $pmpro_error;
            ipnlog($pmpro_error);
        }
        //change level and continue "checkout"
        if(pmpro_changeMembershipLevel($custom_level, $morder->user_id) !== false)
        {
            //update order status and transaction ids
            $morder->status = "success";
            $morder->payment_transaction_id = $txn_id;
            if(!empty($_POST['token']))
                $morder->subscription_transaction_id = $_POST['m_payment_id'];
            else
                $morder->subscription_transaction_id = "";
            $morder->saveOrder();
            //add discount code use
            if(!empty($discount_code) && !empty($use_discount_code))
            {
                $wpdb->query("INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . $discount_code_id . "', '" . $morder->user_id . "', '" . $morder->id . "', '" . current_time('mysql') . "");
            }
            //save first and last name fields
            if(!empty($_POST['first_name']))
            {
                $old_firstname = get_user_meta($morder->user_id, "first_name", true);
                if(!empty($old_firstname))
                    update_user_meta($morder->user_id, "first_name", $_POST['first_name']);
            }
            if(!empty($_POST['last_name']))
            {
                $old_lastname = get_user_meta($morder->user_id, "last_name", true);
                if(!empty($old_lastname))
                    update_user_meta($morder->user_id, "last_name", $_POST['last_name']);
            }
            //hook
            do_action("pmpro_after_checkout", $morder->user_id);
            //setup some values for the emails
            if(!empty($morder))
                $invoice = new MemberOrder($morder->id);
            else
                $invoice = NULL;
            $user = get_userdata($morder->user_id);
            $user->membership_level = $morder->membership_level;        //make sure they have the right level info
            //send email to member
            $pmproemail = new PMProEmail();
            $pmproemail->sendCheckoutEmail($user, $invoice);
            //send email to admin
            $pmproemail = new PMProEmail();
            $pmproemail->sendCheckoutAdminEmail($user, $invoice);
            // cancel order previous PayFast subscription if applicable
          //  $oldSub = $wpdb->get_var("SELECT paypal_token FROM $wpdb->pmpro_membership_orders WHERE user_id = '" . $_POST['custom_int1'] . "' AND status = 'cancelled' ORDER BY timestamp DESC LIMIT 1");
//            if ( !empty( $oldSub ) && !empty( $_POST['token'] ) )
//            {
//                ipnlog('oldsub: ' . $oldSub);
//                $hashArray = array();
//                $guid = $oldSub;
//                $passphrase = pmpro_getOption( 'payfast_passphrase' );
//
//                $hashArray['version'] = 'v1';
//                $hashArray['merchant-id'] = pmpro_getOption( 'payfast_merchant_id' );
//                $hashArray['passphrase'] = $passphrase;
//                $hashArray['timestamp'] = date('Y-m-d').'T'. date('H:i:s');
//
//                $orderedPrehash = $hashArray;
//
//                ksort($orderedPrehash);
//
//                $signature = md5(http_build_query($orderedPrehash));
//
//                $domain = "https://api.payfast.co.za";
//
//                    // configure curl
//                $url =  $domain .'/subscriptions/'. $guid . '/cancel';
//
//                $ch = curl_init($url);
//                $useragent = 'PayFast Sample PHP Recurring Billing Integration';
//
//                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
//                curl_setopt( $ch, CURLOPT_HEADER, false );
//                curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
//                curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
//                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT");
//                // curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($payload));
//                curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
//                curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
//                    'version: v1',
//                    'merchant-id: ' . pmpro_getOption( 'payfast_merchant_id' ),
//                    'signature: ' . $signature,
//                    'timestamp: ' . $hashArray['timestamp']
//                ));
//
//                $response = curl_exec( $ch );
//
//                curl_close( $ch );
//            }
            
            return true;
        }
        else
            return false;
    }
function pmpro_ipnSaveOrder( $txn_id, $last_order )
{
    global $wpdb;
    //check that txn_id has not been previously processed
    $old_txn = $wpdb->get_var("SELECT payment_transaction_id FROM $wpdb->pmpro_membership_orders WHERE payment_transaction_id = '" . $txn_id . "' LIMIT 1");
    if (empty($old_txn))
    {
        //hook for successful subscription payments
		//do_action("pmpro_subscription_payment_completed");
        //save order
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
        //set amount based on which PayPal type
        if ($last_order->gateway == "payfast") {
            $morder->InitialPayment = $_POST['amount_gross'];    //not the initial payment, but the class is expecting that
            $morder->PaymentAmount = $_POST['amount_gross'];
        }
        $morder->FirstName = $_POST['name_first'];
        $morder->LastName = $_POST['name_last'];
        $morder->Email = $_POST['email_address'];
        //get address info if appropriate
        if ($last_order->gateway == "payfast") {
            $morder->Address1 = get_user_meta($last_order->user_id, "pmpro_baddress1", true);
            $morder->City = get_user_meta($last_order->user_id, "pmpro_bcity", true);
            $morder->State = get_user_meta($last_order->user_id, "pmpro_bstate", true);
            $morder->CountryCode = "ZA";
            $morder->Zip = get_user_meta($last_order->user_id, "pmpro_bzip", true);
            $morder->PhoneNumber = get_user_meta($last_order->user_id, "pmpro_bphone", true);
            $morder->billing->name = $_POST['name_first'] . " " . $_POST['name_last'];
            $morder->billing->street = get_user_meta($last_order->user_id, "pmpro_baddress1", true);
            $morder->billing->city = get_user_meta($last_order->user_id, "pmpro_bcity", true);
            $morder->billing->state = get_user_meta($last_order->user_id, "pmpro_bstate", true);
            $morder->billing->zip = get_user_meta($last_order->user_id, "pmpro_bzip", true);
            $morder->billing->country = get_user_meta($last_order->user_id, "pmpro_bcountry", true);
            $morder->billing->phone = get_user_meta($last_order->user_id, "pmpro_bphone", true);
            //get CC info that is on file
            $morder->cardtype = get_user_meta($last_order->user_id, "pmpro_CardType", true);
            $morder->accountnumber = hideCardNumber(get_user_meta($last_order->user_id, "pmpro_AccountNumber", true), false);
            $morder->expirationmonth = get_user_meta($last_order->user_id, "pmpro_ExpirationMonth", true);
            $morder->expirationyear = get_user_meta($last_order->user_id, "pmpro_ExpirationYear", true);
            $morder->ExpirationDate = $morder->expirationmonth . $morder->expirationyear;
            $morder->ExpirationDate_YdashM = $morder->expirationyear . "-" . $morder->expirationmonth;
        }
        //figure out timestamp or default to none (today)
//        if(!empty($_POST['payment_date']))
//            $morder->timestamp = strtotime($_POST['payment_date']);
        //save
        $morder->saveOrder();
        $morder->getMemberOrderByID($morder->id);
        //email the user their invoice
        $pmproemail = new PMProEmail();
        $pmproemail->sendInvoiceEmail(get_userdata($last_order->user_id), $morder);
        do_action( "pmpro_subscription_payment_completed", $morder );
        ipnlog("New order (" . $morder->code . ") created.");
        return true;
    } else {
        ipnlog("Duplicate Transaction ID: " . $txn_id);
        return true;
    }
}
    /**
     * pfGetData
     *
     * @author Jonathan Smit (PayFast.co.za)
     */
    function pmpro_pfGetData()
    {
        // Posted variables from ITN
        $pfData = $_POST;
        // Strip any slashes in data
        foreach( $pfData as $key => $val )
            $pfData[$key] = stripslashes( $val );
        // Return "false" if no data was received
        if( sizeof( $pfData ) == 0 )
            return( false );
        else
            return( $pfData );
    }
    /**
     * pfValidSignature
     *
     * @author Jonathan Smit (PayFast.co.za)
     */
    function pmpro_pfValidSignature( $pfData = null, &$pfParamString = null, $passPhrase = null )
    {
        // Dump the submitted variables and calculate security signature
        foreach( $pfData as $key => $val )
        {
            if( $key != 'signature' )
            {
                $pfParamString .= $key .'='. urlencode( $val ) .'&';
            }
            else
            {
                break;
            }
        }
        // Remove the last '&' from the parameter string
        $pfParamString = substr( $pfParamString, 0, -1 );
      
        if( is_null( $passPhrase ) )
        {
            $tempParamString = $pfParamString;
        }
        else
        {
            $tempParamString = $pfParamString."&passphrase=".urlencode( trim( $passPhrase ) );
        }
        $signature = md5( $tempParamString );
        $result = ( $pfData['signature'] == $signature );
        ipnlog( 'Signature Sent: ' . $signature );
        ipnlog(  'Signature = '. ( $result ? 'valid' : 'invalid' ) );
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
    function pmpro_pfValidData( $pfHost = 'www.payfast.co.za', $pfParamString = '', $pfProxy = null )
    {
        ipnlog(  'Host = '. $pfHost );
        ipnlog(  'Params = '. $pfParamString );
        // Use cURL (if available)
        if( defined( 'PF_CURL' ) )
        {
            // Variable initialization
            $url = 'https://'. $pfHost .'/eng/query/validate';
            // Create default cURL object
            $ch = curl_init();
            // Set cURL options - Use curl_setopt for freater PHP compatibility
            // Base settings
            curl_setopt( $ch, CURLOPT_USERAGENT, PF_USER_AGENT );  // Set user agent
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
            curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            // Standard settings
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
            curl_setopt( $ch, CURLOPT_TIMEOUT, PF_TIMEOUT );
            if( !empty( $pfProxy ) )
                curl_setopt( $ch, CURLOPT_PROXY, $proxy );
            // Execute CURL
            $response = curl_exec( $ch );
            curl_close( $ch );
	        ipnlog( 'Curl used' );
        }
        // Use fsockopen
        else
        {
            // Variable initialization
            $header = '';
            $res = '';
            $headerDone = false;
            // Construct Header
            $header = "POST /eng/query/validate HTTP/1.0\r\n";
            $header .= "Host: ". $pfHost ."\r\n";
            $header .= "User-Agent: ". PF_USER_AGENT ."\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen( $pfParamString ) . "\r\n\r\n";
            // Connect to server
            $socket = fsockopen( 'ssl://'. $pfHost, 443, $errno, $errstr, PF_TIMEOUT );
            // Send command to server
            fputs( $socket, $header . $pfParamString );
            // Read the response from the server
            while( !feof( $socket ) )
            {
                $line = fgets( $socket, 1024 );
                // Check if we are finished reading the header yet
                if( strcmp( $line, "\r\n" ) == 0 )
                {
                    // read the header
                    $headerDone = true;
                }
                // If header has been processed
                else if( $headerDone )
                {
                    // Read the main response
                    $response .= $line;
                }
            }
			ipnlog( 'fsockopen' );
        }
        ipnlog(  "Response:\n". print_r( $response, true ) );
        // Interpret Response
        $lines = explode( "\r\n", $response );
        $verifyResult = trim( $lines[0] );
        if( strcasecmp( $verifyResult, 'VALID' ) == 0 )
            return( true );
        else
            return( false );
    }
    /**
     * pfValidIP
     *
     * @author Jonathan Smit (PayFast.co.za)
     * @param $sourceIP String Source IP address
     */
    function pmpro_pfValidIP( $sourceIP )
    {
        // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
            );
        $validIps = array();
        foreach( $validHosts as $pfHostname )
        {
            $ips = gethostbynamel( $pfHostname );
            if( $ips !== false )
                $validIps = array_merge( $validIps, $ips );
        }
        // Remove duplicates
        $validIps = array_unique( $validIps );
        ipnlog(  "Valid IPs:\n". print_r( $validIps, true ) );
        if( in_array( $sourceIP, $validIps ) )
            return( true );
        else
            return( false );
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
    function pmpro_pfAmountsEqual( $amount1, $amount2 )
    {
        if( abs( floatval( $amount1 ) - floatval( $amount2 ) ) > PF_EPSILON )
            return( false );
        else
            return( true );
    }
