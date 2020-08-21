=== Paid Memberships Pro - PayFast Gateway Add On ===
Contributors: strangerstudios,andrewza
Tags: paid memberships pro, pmpro, payfast, gateway, credit card
Requires at least: 4.7
Tested up to: 5.5
Stable tag: 0.9

Add the South African payment processing service PayFast as a gateway option for Paid Memberships Pro.

== Description ==

Add the South African payment processing service PayFast as a gateway option for Paid Memberships Pro.

[PayFast](https://www.payfast.co.za/) is a payments processing service for South Africans and South African websites. Their payment gateway offers a secure and instant transfer of money between online buyers and sellers. Merchants can accept funds from local and international customers from anywhere in the world in ZAR.

Fees are charged per-transaction [according to this fee schedule](https://www.payfast.co.za/fees/) and there are no setup or monthly fees.

[youtube http://www.youtube.com/watch?v=aDjjSjmJ-j8]

== Installation ==

1. Make sure you have the Paid Memberships Pro plugin installed and activated.
1. Upload the `pmpro-payfast` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to Memberships -> Payment Settings in your WordPress dashboard to complete the PayFast settings.

= Setup =
1. Register and validate your account with PayFast: https://www.payfast.co.za/user/register.
1. Log in to your PayFast account and select 'Settings' to retrieve your merchant details.
1. Log in to your WordPress dashboard and navigate to Memberships > Payment Settings.
1. Set your "Payment Gateway" to "PayFast". Fill out your merchant details and Security Passphrase (required in order to accept recurring payments).
1. Set your currency to "South African Rand".
1. Save your settings.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the GitHub issue tracker here: https://github.com/strangerstudios/pmpro-payfast/issues

For immediate help, also post to our premium support site at https://www.paidmembershipspro.com for more documentation and our support forums.

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at https://www.paidmembershipspro.com for more documentation and our support forums.

= I need to test PayFast in sandbox mode. =

To test PayFast payments without being billed in sandbox mode requires a sandbox account from PayFast. For more information in creating a sandbox account - https://developers.payfast.co.za/documentation/#the-sandbox


== Changelog ==
= 0.9 - 2020-08-21 =
* BUG FIX: Hide the PayFast logo on checkout when a discount is applied that sets the level to be free.
* ENHANCEMENT: Show warnings within the admin dashboard area when levels contain custom trials, not-supported billing periods.

= 0.8.5 - 2020-07-20 =
* BUG FIX: Fixed issue where discount code wasn't removing the recurring payments data correctly.

= 0.8.4 - 2020-07-06 =
* BUG FIX: Fixed issue where billing page update link was causing a fatal error. Redirect to PayFast login instead.

= 0.8.3 - 2019-10-16 =
* BUG FIX: Signature mismatch on recurring payments. (thanks Tauriq Stanley)

= 0.8.2 - 2019-04-26 =
* ENHANCEMENT: Prefix all constants to avoid conflict with other PayFast Plugins.

= 0.8.1 =
* SECURITY: Better sanitization of POST parameters in the ITN hander.
* BUG FIX/ENHANCEMENT: The PayFast signature is really called PayFast PassPhrase now.

= 0.8 =
* Initial release. Pluganized from the PayFast PMPro fork. Includes many bug fixes and improvements.

== Upgrade Notice ==
= 0.9 = 
* Please upgrade for some minor fixes.

= 0.8.4 =
* Please upgrade for bug fixes pertaining to discount codes at checkout.

= 0.8.3 =
* Please upgrade for bug fixes pertaining to recurring membership billing.
