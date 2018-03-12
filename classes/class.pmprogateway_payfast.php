<?php
/**
 * Based on the scripts by Ron Darby shared at https://www.payfast.co.za/shopping-carts/paid-memberships-pro/
 * @author     Ron Darby - PayFast
 * @copyright  2009-2014 PayFast (Pty) Ltd
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
    
// Require the default PMPro Gateway Class.
require_once( PMPRO_DIR . "/classes/gateways/class.pmprogateway.php" );

//load classes init method
add_action( 'init', array( 'PMProGateway_PayFast', 'init' ) );
class PMProGateway_PayFast {

    function __construct($gateway = NULL) {
       $this->gateway = $gateway;
       return $this->gateway;
    }

    /**
     * Run on WP init
     *
     * @since 1.8
     */
    static function init() {
        //make sure PayFast is a gateway option
        add_filter( 'pmpro_gateways', array( 'PMProGateway_PayFast', 'pmpro_gateways' ) );

        //add fields to payment settings
        add_filter('pmpro_payment_options', array('PMProGateway_PayFast', 'pmpro_payment_options'));

        add_filter('pmpro_payment_option_fields', array('PMProGateway_PayFast', 'pmpro_payment_option_fields'), 10, 2);

        add_filter('pmpro_include_billing_address_fields', '__return_false');
        add_filter('pmpro_include_payment_information_fields', '__return_false');

        add_filter('pmpro_required_billing_fields', '__return_empty_array');
        add_filter('pmpro_checkout_before_submit_button', array('PMProGateway_PayFast', 'pmpro_checkout_before_submit_button'));
        add_filter('pmpro_checkout_before_change_membership_level', array('PMProGateway_PayFast', 'pmpro_checkout_before_change_membership_level'), 10, 2);
        
        //itn handler
        add_action('wp_ajax_nopriv_pmpro_payfast_itn_handler', array('PMProGateway_PayFast', 'wp_ajax_pmpro_payfast_itn_handler'));
        add_action('wp_ajax_pmpro_payfast_itn_handler', array('PMProGateway_PayFast', 'wp_ajax_pmpro_payfast_itn_handler'));

        add_filter( 'pmpro_gateways_with_pending_status', array( 'PMProGateway_PayFast', 'pmpro_gateways_with_pending_status' ) );
    }


    /**
     * Add PayFast to the list of allowed gateways.
     *
     * @return array
     */
    static function pmpro_gateways_with_pending_status( $gateways ) {
        $gateways[] = 'payfast';

        return $gateways;
    }

    /**
     * Make sure this gateway is in the gateways list
     *
     * @since 1.8
     */
    static function pmpro_gateways( $gateways ) {           
        if( empty( $gateways['payfast'] ) )
            $gateways['payfast'] = __('PayFast', 'pmpro');

        return $gateways;
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
        //get stripe options
        $payfast_options = self::getGatewayOptions();

        //merge with others.
        $options = array_merge( $payfast_options, $options );

        return $options;
    }

    /**
     * Display fields for this gateway's options.
     *
     * @since 1.8
     */
    static function pmpro_payment_option_fields( $values, $gateway )
    {
    ?>
        <tr class="gateway gateway_payfast" <?php if( $gateway != "payfast" ) { ?>style="display: none;"<?php } ?>>
             <th scope="row" valign="top">
                 <label for="payfast_merchant_id"><?php _e('PayFast Merchant ID', 'pmpro'); ?>:</label>
             </th>
             <td>
                 <input id="payfast_merchant_id" name="payfast_merchant_id" value="<?php echo esc_attr($values['payfast_merchant_id']); ?>" />
             </td>
         </tr>
         <tr class="gateway gateway_payfast" <?php if( $gateway != "payfast" ) { ?>style="display: none;"<?php } ?>>
             <th scope="row" valign="top">
                 <label for="payfast_merchant_key"><?php _e('PayFast Merchant Key', 'pmpro');?>:</label>
             </th>
             <td>
                 <input id="payfast_merchant_key" name="payfast_merchant_key" value="<?php echo esc_attr($values['payfast_merchant_key']); ?>" />
             </td>
         </tr>
         <tr class="gateway gateway_payfast" <?php if( $gateway != "payfast" ) { ?>style="display: none;"<?php } ?>>
             <th scope="row" valign="top">
                 <label for="payfast_debug"><?php _e('PayFast Debug Mode', 'pmpro');?>:</label>
             </th>
             <td>
                 <select name="payfast_debug">
                     <option value="1" <?php if( isset($values['payfast_debug']) && $values['payfast_debug'] ) { ?>selected="selected"<?php } ?>><?php _e('On', 'pmpro');?></option>
                     <option value="0" <?php if( isset($values['payfast_debug']) && !$values['payfast_debug']) { ?>selected="selected"<?php } ?>><?php _e('Off', 'pmpro');?></option>
                 </select>
             </td>
         </tr>
        <tr class="gateway gateway_payfast" <?php if( $gateway != "payfast" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label for="payfast_passphrase"><?php _e('PayFast Signature', 'pmpro');?>:</label>
            </th>
            <td>
                <input id="payfast_passphrase" name="payfast_passphrase" value="<?php echo esc_attr($values['payfast_passphrase']); ?>" /> &nbsp;<small><?php _e( 'Do not set a password unless you have set it in your PayFast settings on www.PayFast.co.za'); ?></small>
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

        unset($fields['bfirstname']);
        unset($fields['blastname']);
        unset($fields['baddress1']);
        unset($fields['bcity']);
        unset($fields['bstate']);
        unset($fields['bzipcode']);
        unset($fields['bphone']);
        unset($fields['bemail']);
        unset($fields['bcountry']);
        unset($fields['CardType']);
        unset($fields['AccountNumber']);
        unset($fields['ExpirationMonth']);
        unset($fields['ExpirationYear']);
        unset($fields['CVV']);

        return $fields;
    }

    /**
     * Show information before PMPro's checkout button.
     * @todo: Add a filter to show/hide this notice.
     * @since 1.8
     */
    static function pmpro_checkout_before_submit_button() {
        global $gateway, $pmpro_requirebilling;

        // show PayFast information before checkout button.
        ?>
        <div id="pmpro_payfast_before_checkout" style="text-align:center;">
            <span id="pmpro_payfast_checkout" <?php if( ( $gateway != "paypalexpress" && $gateway != "payfast" ) || !$pmpro_requirebilling ) { ?>style="display: none;"<?php } ?>>
                <input type="hidden" name="submit-checkout" value="1" />
               
               <?php  echo '<strong>' . __( 'NOTE:', 'pmpro-payfast' ) . '</strong> ' . __( 'if changing a subscription it may take a minute or two to reflect. Please also login to your PayFast account to ensure the old subscription is cancelled.' ); ?>

                <p><img src="<?php echo plugins_url( 'img/payfast_logo.png', __DIR__ ); ?>" width="100px" /></p>
            </span>
        </div>

        <?php
    }

    /**
     * Instead of change membership levels, send users to PayFast to pay.
     *
     * @since 1.8
     */
    static function pmpro_checkout_before_change_membership_level( $user_id, $morder )
    {
        global $discount_code_id;

        //if no order, no need to pay
        if( empty( $morder ) )
            return;

        // bail if the current gateway is not set to PayFast.
        if( 'payfast' != $morder->gateway ) {
            return;
        }
        
        $morder->user_id = $user_id;
        $morder->saveOrder();

        //save discount code use
        if( !empty( $discount_code_id ) )
            $wpdb->query( "INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . $discount_code_id . "', '" . $user_id . "', '" . $morder->id . "', now())" );

        do_action( "pmpro_before_send_to_payfast", $user_id, $morder );

        $morder->Gateway->sendToPayFast( $morder );
    }


    /**
     * Send traffic to wp-admin/admin-ajax.php?action=pmpro_payfast_itn_handler to the itn handler
     */
    static function wp_ajax_pmpro_payfast_itn_handler() {
        require_once( PMPRO_PAYFAST_DIR . "services/payfast_itn_handler.php" ); 
        exit;
    }       

    function process( &$order )
    {
        if( empty( $order->code ) )
            $order->code = $order->getRandomCode();

        //clean up a couple values
        $order->payment_type = "PayFast";
        $order->CardType = "";
        $order->cardtype = "";

        //just save, the user will go to PayFast to pay
        $order->status = "review";
        $order->saveOrder();

        return true;
    }

    /**
     * @param $order
     */
    function sendToPayFast( &$order )
    {
        global $pmpro_currency;

        //taxes on initial amount
        $initial_payment = $order->InitialPayment;
        $initial_payment_tax = $order->getTaxForPrice( $initial_payment );
        $initial_payment = round( ( float )$initial_payment + (float)$initial_payment_tax, 2 );

        //taxes on the amount
        $amount = $order->PaymentAmount;
        $amount_tax = $order->getTaxForPrice( $amount );
        $order->subtotal = $amount;
        $amount = round( ( float )$amount + ( float )$amount_tax, 2 );

        //merchant details
        $merchant_id = pmpro_getOption( 'payfast_merchant_id' );
        $merchant_key = pmpro_getOption( 'payfast_merchant_key' );

        //build PayFast Redirect
        $environment = pmpro_getOption( "gateway_environment" );
        if( "sandbox" === $environment || "beta-sandbox" === $environment ) {
            $payfast_url = "https://sandbox.payfast.co.za/eng/process";
        }else{
            $payfast_url = "https://www.payfast.co.za/eng/process";
        }

        $data = array(
            'merchant_id'   => $merchant_id,
            'merchant_key'  => $merchant_key,
            'return_url'    => pmpro_url( 'confirmation', '?level=' . $order->membership_level->id ),
            'cancel_url'    => pmpro_url( 'levels' ),
            'notify_url'    => admin_url( 'admin-ajax.php' ) . '?action=pmpro_payfast_itn_handler',
            'name_first'    => $order->FirstName,
            'name_last'     => $order->LastName,
            'email_address' => $order->Email,
            'm_payment_id'  => $order->code,
            'amount'        => $initial_payment,
            'item_name'     => substr( $order->membership_level->name . ' at ' . get_bloginfo( 'name' ), 0, 99 ),
            'custom_int1'   => $order->user_id,
            );

        $data = apply_filters( 'pmpro_payfast_data', $data );

        $cycles = $order->membership_level->billing_limit;
        
        //convert PMPro cycle_number and period into a PayFast frequency
        switch ( $order->BillingPeriod )
        {

            case 'Month':
                $frequency = '3';

                break;

            case 'Year':
                $frequency = '6';

                break;
        }

        //set the recurringDiscount to true by default and change to false only if a non recurring code is used
        $recurringDiscount = true;

        //check if a discount code is being used
        if ( !empty ( $order->discount_code ) )
        {
            //check to see whether or not it is a recurring discount code
            if ( isset( $order->TotalBillingCycles ) )
            {
                $recurringDiscount = true; //1
            }
            else
            {
                $recurringDiscount = false;
            }
        }

        // Add subscription data
        if ( !empty( $frequency ) && !empty( $recurringDiscount ) )
        {
        //    $data['m_subscription_id'] = /*$order->getRandomCode()*/$order->code;
            $data['custom_str1'] = gmdate( 'Y-m-d' );
            $data['subscription_type'] = 1;
            $data['billing_date'] = gmdate( 'Y-m-d' );
            $data['recurring_amount'] = $amount;
            $data['frequency'] = $frequency;
            $data['cycles'] = $cycles == 0 ? 0 : $cycles + 1;

            if(empty( $order->code ) )
                $order->code = $order->getRandomCode();

            //filter order before subscription. use with care.
            $order = apply_filters( "pmpro_subscribe_order", $order, $this );

            //taxes on initial amount
            $initial_payment = $order->InitialPayment;
            $initial_payment_tax = $order->getTaxForPrice( $initial_payment );
            $initial_payment = round((float)$initial_payment + (float)$initial_payment_tax, 2);

            //taxes on the amount
            $amount = $order->PaymentAmount;
            $amount_tax = $order->getTaxForPrice( $amount );
            //$amount = round((float)$amount + (float)$amount_tax, 2);


            $order->status = "pending";
            $order->payment_transaction_id = $order->code;
            $order->subscription_transaction_id = $order->code;

            //update order
            $order->saveOrder();
        }

        $pfOutput = "";
        $pffOutput = "";

        foreach( $data  as $key => $val )
        {
            $pffOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }


        // Remove last ampersand
        $passPhrase = pmpro_getOption( 'payfast_passphrase' );

        // Add passphrase to URL.
        if( empty( $passPhrase ) ) {
            $pfOutput = substr( $pffOutput, 0, -1 );
        } else {
            $pfOutput = $pffOutput."passphrase=".urlencode( trim( $passPhrase ) );
        }


        // Create signature.
        $signature = md5( $pfOutput );

        /**
         * @todo: Check the user_agent and generate a better user agent for description.
         */
        $payfast_url .= '?'.$pffOutput.'&signature='.$signature.'&user_agent=Paid Memberships Pro ' . PMPRO_VERSION;

        wp_redirect( $payfast_url );
        exit;
    }

    function subscribe( &$order ) {
        global $pmpro_currency;

        if( empty( $order->code ) ){
            $order->code = $order->getRandomCode();
        }

        //filter order before subscription. use with care.
        $order = apply_filters( "pmpro_subscribe_order", $order, $this );

        //taxes on initial amount
        $initial_payment = $order->InitialPayment;
        $initial_payment_tax = $order->getTaxForPrice( $initial_payment );
        $initial_payment = round((float)$initial_payment + (float)$initial_payment_tax, 2);

        //taxes on the amount
        $amount = $order->PaymentAmount;
        $amount_tax = $order->getTaxForPrice( $amount );
        //$amount = round((float)$amount + (float)$amount_tax, 2);

        $order->status = "success";
        $order->payment_transaction_id = $order->code;
        $order->subscription_transaction_id = $order->code;

        //update order
        $order->saveOrder();

        return true;
    }

    function cancel(&$order)
    {
        //payfast profile stuff
        $nvpStr = "";
        $nvpStr .= "&PROFILEID=" . urlencode($order->subscription_transaction_id) . "&ACTION=Cancel&NOTE=" . urlencode("User requested cancel.");

        //    $this->httpParsedResponseAr = $this->PPHttpPost('ManageRecurringPaymentsProfileStatus', $nvpStr);
        if ( !empty( $order->subscription_transaction_id ) && $order->subscription_transaction_id == $order->payment_transaction_id )
        {
            ?>'<script type="text/javascript">alert("If cancelling a subscription, please login/create a PayFast account and ensure the subscription is cancelled"); </script>'<?php
            $hashArray = array();
            $guid = $order->paypal_token;
            $passphrase = pmpro_getOption('payfast_passphrase');

            $hashArray['version'] = 'v1';
            $hashArray['merchant-id'] = pmpro_getOption( 'payfast_merchant_id' );
            $hashArray['passphrase'] = $passphrase;
            $hashArray['timestamp'] = date('Y-m-d') . 'T' . date('H:i:s');

            $orderedPrehash = $hashArray;

            ksort($orderedPrehash);

            $signature = md5(http_build_query($orderedPrehash));

            $domain = "https://api.payfast.co.za";

            // configure curl
            $url = $domain . '/subscriptions/' . $guid . '/cancel';

            $ch = curl_init($url);
            $useragent = 'PayFast Sample PHP Recurring Billing Integration';

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            // curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'version: v1',
                'merchant-id: ' . pmpro_getOption('payfast_merchant_id'),
                'signature: ' . $signature,
                'timestamp: ' . $hashArray['timestamp']
            ));

            $response = curl_exec($ch);

            curl_close($ch);

            $order->updateStatus( "cancelled" );
            return true;
        }
    }
}