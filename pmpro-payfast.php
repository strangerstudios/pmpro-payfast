<?php
/*
Plugin Name: Paid Memberships Pro - PayFast Gateway
Plugin URI: https://www.paidmembershipspro.com/add-ons/payfast-payment-gateway/
Description: Adds PayFast as a gateway option for Paid Memberships Pro.
Version: .1
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
*/

define( "PMPRO_PAYFAST_DIR", plugin_dir_path( __FILE__ ) );

//load payment gateway class after all plugins are loaded to make sure PMPro stuff is available
function pmpro_payfast_plugins_loaded() {	

	//make sure PMPro is loaded
	if( !defined( 'PMPRO_DIR' ) ) {
		return;
	}
	
	require_once( PMPRO_PAYFAST_DIR . "/classes/class.pmprogateway_payfast.php" );
}
add_action('plugins_loaded', 'pmpro_payfast_plugins_loaded');

/* Register activation hook. */
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
			<p><?php printf( __( 'Thank you for activating. <a href="%s">Visit the payment settings page</a> to configure the Payfast Gateway.', 'pmpro-payfast' ), get_admin_url( null, 'admin.php?page=pmpro-paymentsettings' ) ); ?></p>
		</div>
		<?php
		// Delete transient, only display this notice once.
		delete_transient( 'pmpro-payfast-admin-notice' );
	}
}
add_action( 'admin_notices', 'pmpro_payfast_admin_notice' );
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
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/add-ons/payfast-payment-gateway/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro-payfast' ) . '</a>',
			'<a href="' . esc_url( 'https://paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro-payfast' ) . '</a>',
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmpro_payfast_plugin_row_meta', 10, 2 );
