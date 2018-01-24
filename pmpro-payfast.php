<?php
/*
Plugin Name: Paid Memberships Pro - PayFast Gateway
Plugin URI: https://www.paidmembershipspro.com/wp/pmpro-payfast/
Description: Adds PayFast as a gateway option for Paid Memberships Pro.
Version: .1
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
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
