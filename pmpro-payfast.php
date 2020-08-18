<?php
/*
Plugin Name: Paid Memberships Pro - PayFast Gateway
Plugin URI: https://www.paidmembershipspro.com/add-ons/payfast-payment-gateway/
Description: Adds PayFast as a gateway option for Paid Memberships Pro.
Version: 0.8.5
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
 * @since 0.1.0
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

/*
 * Fix PMPro Payfast showing SSL error in admin menus
 * when set up correctly.
 *
 * @since 0.9
 */
function pmpro_payfast_pmpro_is_ready( $pmpro_is_ready ) {
	global $pmpro_gateway_ready, $pmpro_pages_ready;

	if ( empty($pmpro_gateway_ready) && 'payfast' === pmpro_getOption( 'gateway' ) ) {
		if( pmpro_getOption( 'payfast_merchant_id' ) && pmpro_getOption( 'payfast_merchant_key' ) && pmpro_getOption( 'payfast_passphrase' ) ) {
			$pmpro_gateway_ready = true;
		}
	}

	return ( $pmpro_gateway_ready && $pmpro_pages_ready );
}
add_filter( 'pmpro_is_ready', 'pmpro_payfast_pmpro_is_ready' );

/**
 * Function to add links to the plugin action links
 *
 * @param array $links Array of links to be shown in plugin action links.
 */
function pmpro_payfast_plugin_action_links( $links ) {
	if ( current_user_can( 'manage_options' ) ) {
		$new_links = array(
			'<a href="' . get_admin_url( null, 'admin.php?page=pmpro-paymentsettings' ) . '">' . __( 'Configure Payfast', 'pmpro-payfast' ) . '</a>',
		);
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

function pmpro_checkForPayFastCompatibility( $level = NULL ){

	$gateway = pmpro_getOption("gateway");

	if( $gateway == "payfast" ){

		global $wpdb;

		//check ALL the levels
		if( empty( $level ) ){

			$sqlQuery = "SELECT * FROM $wpdb->pmpro_membership_levels ORDER BY id ASC";
			$levels = $wpdb->get_results($sqlQuery, OBJECT);
			
			if( !empty( $levels ) ){
				foreach( $levels as $level ){

					if( !pmpro_checkForPayFastCompatibility( $level->id ) ){
						return false;
					}

				}
			}

		} else {

			if( is_numeric( $level ) ){

				$level = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = %d LIMIT 1" , $level ) );

				if( $level->trial_amount > 0 || $level->trial_limit > 0 || ( $level->cycle_number > 0 && ( $level->cycle_period == "Day" || $level->cycle_period == "Week") ) ){
					return false;
				}

			}
						
		}
	}

	return true;

}

function pmpro_payfast_check_level_compat(){

	$compatible = pmpro_checkForPayFastCompatibility();
	
	if( !$compatible ){
		?>
		<div class="<?php if( !$compatible ) { ?>error<?php } else { ?>notice notice-warning<?php } ?> fade">		
			<p>
				<?php
					//only show the invalid part if they've entered a key
					
					if( $compatible ){
						?><strong><?php _e('Some of the billing details for some of your membership levels is not supported by PayFast.', 'pmpro-payfast' );?></strong><?php
					} 
				?>
				<?php _e("Custom trials are not supported by PayFast at this point in time.", 'pmpro-payfast' );?>
				<a href="<?php echo admin_url('admin.php?page=pmpro-membershiplevels');?>"><?php _e('View Levels', 'pmpro-payfast' );?></a>
			</p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'pmpro_payfast_check_level_compat' );