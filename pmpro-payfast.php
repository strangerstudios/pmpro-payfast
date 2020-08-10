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

function pmpro_payfast_discount_code_result( $discount_code, $discount_code_id, $level_id, $code_level ){
		
		global $wpdb;

		//okay, send back new price info
		$sqlQuery = "SELECT l.id, cl.*, l.name, l.description, l.allow_signups FROM $wpdb->pmpro_discount_codes_levels cl LEFT JOIN $wpdb->pmpro_membership_levels l ON cl.level_id = l.id LEFT JOIN $wpdb->pmpro_discount_codes dc ON dc.id = cl.code_id WHERE dc.code = '" . $discount_code . "' AND cl.level_id = '" . $level_id . "' LIMIT 1";
		
		$code_level = $wpdb->get_row($sqlQuery);

		//if the discount code doesn't adjust the level, let's just get the straight level
		if(empty($code_level)){
			$code_level = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '" . $level_id . "' LIMIT 1");
		}

		if( !empty( $code_level->code_id ) ){ //A valid discount code was returned
			?>
				jQuery('#pmpro_payfast_before_checkout').hide();
			<?php
		}

	}
add_action( 'pmpro_applydiscountcode_return_js', 'pmpro_payfast_discount_code_result', 10, 4 );