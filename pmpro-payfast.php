<?php
/*
Plugin Name: Paid Memberships Pro - PayFast Gateway
Plugin URI: https://www.paidmembershipspro.com/add-ons/payfast-payment-gateway/
Description: Adds PayFast as a gateway option for Paid Memberships Pro.
Version: 1.5
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
Text Domain: pmpro-payfast
Domain Path: /languages
*/

define( 'PMPRO_PAYFAST_DIR', plugin_dir_path( __FILE__ ) );

// load payment gateway class after all plugins are loaded to make sure PMPro stuff is available
function pmpro_payfast_plugins_loaded() {

	load_plugin_textdomain( 'pmpro-payfast', false, basename( __DIR__ ) . '/languages' );

	// make sure PMPro is loaded
	if ( ! defined( 'PMPRO_DIR' ) ) {
		return;
	}

	require_once( PMPRO_PAYFAST_DIR . '/classes/class.pmprogateway_payfast.php' );
}
add_action( 'plugins_loaded', 'pmpro_payfast_plugins_loaded' );

// Register activation hook.
register_activation_hook( __FILE__, 'pmpro_payfast_admin_notice_activation_hook' );
/**
 * Runs only when the plugin is activated.
 *
 * @since 0.1.0
 */
function pmpro_payfast_admin_notice_activation_hook() {
	// Create transient data.
	set_transient( 'pmpro-payfast-admin-notice', true, 5 );
}

/**
 * Admin Notice on Activation.
 *
 * @since 0.1
 */
function pmpro_payfast_admin_notice() {
	// Check transient, if available display notice.
	if ( get_transient( 'pmpro-payfast-admin-notice' ) ) { ?>
		<div class="updated notice is-dismissible">
			<p><?php printf( __( 'Thank you for activating. <a href="%s">Visit the payment settings page</a> to configure the Payfast Gateway.', 'pmpro-payfast' ), esc_url( get_admin_url( null, 'admin.php?page=pmpro-paymentsettings' ) ) ); ?></p>
		</div>
		<?php
		// Delete transient, only display this notice once.
		delete_transient( 'pmpro-payfast-admin-notice' );
	}
}
add_action( 'admin_notices', 'pmpro_payfast_admin_notice' );

/** 
 * Show an admin warning notice if there is a level setup that is incorrect.
 * @since 0.9
 */
 function pmpro_payfast_check_level_compat(){

	// Only show the notice on either the levels page or payment settings page.
	if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != 'pmpro-membershiplevels' ) {
		return;
	}

	$level = isset( $_REQUEST['edit'] ) ? intval( $_REQUEST['edit'] ) : '';

	// Don't check if level is not set.
	if ( empty( $level ) ) {
		return;
	}

	$compatible = pmpro_payfast_check_billing_compat( $level );
	
	if ( ! $compatible ){
		?>
		<div class="notice notice-error fade">		
			<p>
				<?php esc_html_e( "PayFast currently doesn't support custom trials. Please can you update your membership levels that may have these set.", 'pmpro-payfast' );?>
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'pmpro_payfast_check_level_compat' );

/**
 * Fix PMPro Payfast showing SSL error in admin menus
 * when set up correctly.
 *
 * @since 0.9
 */
function pmpro_payfast_pmpro_is_ready( $pmpro_is_ready ) {
	global $pmpro_gateway_ready, $pmpro_pages_ready;

	if ( empty($pmpro_gateway_ready) && 'payfast' === get_option( 'pmpro_gateway' ) ) {
		if( get_option( 'pmpro_payfast_merchant_id' ) && get_option( 'pmpro_payfast_merchant_key' ) && get_option( 'pmpro_payfast_passphrase' ) ) {
			$pmpro_gateway_ready = true;
		}
	}

	return ( $pmpro_gateway_ready && $pmpro_pages_ready );
}
add_filter( 'pmpro_is_ready', 'pmpro_payfast_pmpro_is_ready' );

/**
 * Check if there are billing compatibility issues for levels and PayFast.
 * @since 0.9
 */
 function pmpro_payfast_check_billing_compat( $level = NULL ){

 	if( !function_exists( 'pmpro_init' ) ){
 		return;
 	}
 	
	$gateway = get_option("pmpro_gateway");

	if( $gateway == "payfast" ){

		global $wpdb;

		//check ALL the levels
		if( empty( $level ) ){
			$sqlQuery = "SELECT * FROM $wpdb->pmpro_membership_levels ORDER BY id ASC";
			$levels = $wpdb->get_results($sqlQuery, OBJECT);
			
			if( !empty( $levels ) ){
				foreach( $levels as $level ){
					if( !pmpro_payfast_check_billing_compat( $level->id ) ){
						return false;
					}

				}
			}

		} else {

			if( is_numeric( $level ) && $level > 0 ){

				$level = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = %d LIMIT 1" , $level ) );
				
				if( pmpro_isLevelTrial( $level ) ){
					return false;
				}

			}
						
		}
	}

	return true;

}

/**
 * Show a warning if custom trial is selected during level setup.
 * @since 0.9
 */
function pmpro_payfast_custom_trial_js_check() {
	$gateway = get_option( 'pmpro_gateway' );

	if ( $gateway !== 'payfast' ) {
		return;
	}

	$custom_trial_warning = __( sprintf( 'PayFast does not support custom trials. Please use the %s instead.', "<a href='https://www.paidmembershipspro.com/add-ons/subscription-delays' target='_blank'>Subscription Delay Add On</a>" ), 'pmpro-payfast' ); ?>
		<script>
			jQuery(document).ready(function(){
				var message = "<?php echo $custom_trial_warning; ?>";
				jQuery( '<tr id="payfast-trial-warning" style="display:none"><th></th><td><em><strong>' + message + '</strong></em></td></tr>' ).insertAfter( '.trial_info' );

				// Show for existing levels.
				if ( jQuery('#custom-trial').is(':checked') ) {
					jQuery( '#payfast-trial-warning' ).show();

				}

				// Toggle if checked or not
				pmpro_payfast_trial_checked();

				function pmpro_payfast_trial_checked() {

					jQuery('#custom_trial').change(function(){
						if ( jQuery(this).prop('checked') ) {
							jQuery( '#payfast-trial-warning' ).show();
						} else {
							jQuery( '#payfast-trial-warning' ).hide();
						}
					});
				}
			});
		</script>
	<?php
}
add_action( 'pmpro_membership_level_after_other_settings', 'pmpro_payfast_custom_trial_js_check' );

/**
 * Function to add links to the plugin action links
 *
 * @param array $links Array of links to be shown in plugin action links.
 */
function pmpro_payfast_plugin_action_links( $links ) {
	$new_links = array();

	if ( current_user_can( 'manage_options' ) ) {
		$new_links[] = '<a href="' . get_admin_url( null, 'admin.php?page=pmpro-paymentsettings' ) . '">' . __( 'Configure Payfast', 'pmpro-payfast' ) . '</a>';
	}

	return array_merge( $new_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pmpro_payfast_plugin_action_links' );

/**
 * Function to add links to the plugin row meta
 *
 * @param array  $links Array of links to be shown in plugin meta.
 * @param string $file Filename of the plugin meta is being shown for.
 */
function pmpro_payfast_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'pmpro-payfast.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/add-ons/payfast-payment-gateway/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-payfast' ) ) . '">' . __( 'Docs', 'pmpro-payfast' ) . '</a>',
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-payfast' ) ) . '">' . __( 'Support', 'pmpro-payfast' ) . '</a>',
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmpro_payfast_plugin_row_meta', 10, 2 );

function pmpro_payfast_discount_code_result( $discount_code, $discount_code_id, $level_id, $code_level ){
		
		global $wpdb;

		//okay, send back new price info
		$sqlQuery = "SELECT l.id, cl.*, l.name, l.description, l.allow_signups FROM $wpdb->pmpro_discount_codes_levels cl LEFT JOIN $wpdb->pmpro_membership_levels l ON cl.level_id = l.id LEFT JOIN $wpdb->pmpro_discount_codes dc ON dc.id = cl.code_id WHERE dc.code = '" . $discount_code . "' AND cl.level_id = '" . $level_id . "' LIMIT 1";
		
		$code_level = $wpdb->get_row($sqlQuery);

		//if the discount code doesn't adjust the level, let's just get the straight level
		if(empty($code_level)){
			$code_level = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '" . $level_id . "' LIMIT 1");
		}

		if( pmpro_isLevelFree( $code_level ) ){ //A valid discount code was returned
			?>
				jQuery('#pmpro_payfast_before_checkout').hide();
			<?php
		}

	}
add_action( 'pmpro_applydiscountcode_return_js', 'pmpro_payfast_discount_code_result', 10, 4 );

/**
 * Store the checkout vars in the order meta before sending to PayFast.
 * 
 * @since 1.4
 */
function pmpro_payfast_before_send_to_payfast_save_data( $user_id, $morder ) {

	$submit_values = $_REQUEST;

	// We don't need to store the password fields for this fix.
	if ( isset( $submit_values['password'] ) ) {
		unset( $submit_values['password'] );
	}

	if ( isset( $submit_values['password2'] ) ) {
		unset( $submit_values['password2'] );
	}

	// Loop through $_REQUEST and sanitize each value
	foreach ( $submit_values as $key => $value ) {
		$submit_values[ $key ] = sanitize_text_field( $value );
	}

	update_pmpro_membership_order_meta( $morder->id, 'checkout_vars', $submit_values );

}
add_action( 'pmpro_before_send_to_payfast', 'pmpro_payfast_before_send_to_payfast_save_data', 1, 2 );

/**
 * Load the checkout vars from the order meta into the 
 * $_REQUEST variable so that everything in the after_checkout
 * hook can access the data.
 * 
 * @since 1.4
 */
function pmpro_payfast_after_checkout_clean_data( $user_id, $morder ) {

	$checkout_vars = get_pmpro_membership_order_meta( $morder->id, 'checkout_vars', true );

	// Merge these values into $_REQUEST so that everything in the after_checkout.
	if ( ! empty( $checkout_vars ) ) {
		$_REQUEST = array_merge( $_REQUEST, $checkout_vars );		
	}
	
	delete_pmpro_membership_order_meta( $morder->id, 'checkout_vars' ); //Delete afterwards as we don't need it.
	
}
add_action( 'pmpro_after_checkout', 'pmpro_payfast_after_checkout_clean_data', 1, 2 );
