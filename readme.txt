=== Paid Memberships Pro - MPesa Gateway Add On ===
Contributors: strangerstudios, andrewza, paidmembershipspro
Tags: paid memberships pro, pmpro, mpesa, gateway, credit card
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.4.3

Add the South African payment processing service MPesa as a gateway option for Paid Memberships Pro.

== Description ==

Add the South African payment processing service MPesa as a gateway option for Paid Memberships Pro.

[MPesa](https://www.mpesa.io/) is a payments processing service for South Africans and South African websites. Their payment gateway offers a secure and instant transfer of money between online buyers and sellers. Merchants can accept funds from local and international customers from anywhere in the world in ZAR.

Fees are charged per-transaction [according to this fee schedule](https://www.mpesa.io/fees/) and there are no setup or monthly fees.

[youtube http://www.youtube.com/watch?v=aDjjSjmJ-j8]

== Installation ==

1. Make sure you have the Paid Memberships Pro plugin installed and activated.
1. Upload the `pmpro-mpesa` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to Memberships -> Payment Settings in your WordPress dashboard to complete the MPesa settings.

= Setup =
1. Register and validate your account with MPesa: https://registration.mpesa.io/.
1. Log in to your MPesa account and select 'Settings' to retrieve your merchant details.
1. Log in to your WordPress dashboard and navigate to Memberships > Payment Settings.
1. Set your "Payment Gateway" to "MPesa". Fill out your merchant details and Security Passphrase (required in order to accept recurring payments).
1. Set your currency to "South African Rand".
1. Save your settings.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the GitHub issue tracker here: https://github.com/strangerstudios/pmpro-mpesa/issues

For immediate help, also post to our premium support site at https://www.paidmembershipspro.com for more documentation and our support forums.

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at https://www.paidmembershipspro.com for more documentation and our support forums.

= I need to test MPesa in sandbox mode. =

To test MPesa payments without being billed in sandbox mode requires a sandbox account from MPesa. For more information in creating a sandbox account - https://developers.mpesa.co.za/documentation/#the-sandbox


== Changelog ==
= 1.4.3 - 2024-01-05 =
* BUG FIX: Fixed an issue where the ITN handler would fatal error when PMPROPF_DEBUG constant was missing. (@JarrydLong)

= 1.4.2 - 2023-12-28 =
* SECURITY: Improved the way the log file for ITN requests is generated. (@andrewlimaza, @dparker1005)

= 1.4.1 - 2023-11-30 =
* REFACTOR: Changed the checkout logo to the new MPesa logo
* REFACTOR: Moved to using get_option instead of the pmpro_getOption.

= 1.4 - 2022-12-01 =
* ENHANCEMENT: Added better support for MMPU when a member cancels their membership. (@dparker1005)
* BUG FIX/ENHANCEMENT: Fixed an issue where custom fields were not saving correctly during checkout. (@jarrydlong)
* BUG FIX: Fix fatal error during ITN service MPesa uses. (@louiswol94)
* REFACTOR: Removed redundant code and cleaned up code. (@mircobabini)

= 1.3.0 - 2021-12-15 =
* ENHANCEMENT: Set the default order status to "token" instead of "pending".
* ENHANCEMENT: Hide the Update/Cancel buttons on the membership billing page when MPesa is used.
* BUG FIX: Fixed fatal error where method was called incorrectly on the membership billing page.
* BUG FIX: Rename filter in the ITN Handler to prevent conflicts. Filter name changed from 'pmpro_ipnhandler_level' to 'pmpro_mpesa_itnhandler_level'.

= 1.2 - 2021-02-11 =
* BUG FIX: Fixed admin notice showing up on all pages in WordPress dashboard when custom trial is set.
* ENHANCEMENT: Support Subscriptions Delay Add On

= 1.1 - 2021-01-25 =
* BUG FIX: Fixed an issue where cancellations inside Paid Memberships Pro weren't cancelling the subscription inside MPesa.
* ENHANCEMENT: Moved the location of the 'pmpro_mpesa_data' filter to allow proper filtering of data passed to MPesa.

= 1.0 - 2020-10-21 =
* BUG FIX: Fixed a fatal error if Paid Memberships Pro (core) plugin was disabled.
* BUG FIX: Fixed an issue where initial order was showing incorrect amount charged if initial price wasn't the same as the recurring price.
* BUG FIX: Fixed a PHP warning when creating a new membership level while MPesa was enabled as the active gateway.

= 0.9 - 2020-21-08 =
* BUG FIX: Hide the MPesa logo on checkout when a discount is applied that sets the level to be free.
* ENHANCEMENT: Show warnings within the admin dashboard area when levels contain custom trials, not-supported billing periods.

= 0.8.5 - 2020-07-20 =
* BUG FIX: Fixed issue where discount code wasn't removing the recurring payments data correctly.

= 0.8.4 - 2020-07-06 =
* BUG FIX: Fixed issue where billing page update link was causing a fatal error. Redirect to MPesa login instead.

= 0.8.3 - 2019-10-16 =
* BUG FIX: Signature mismatch on recurring payments. (thanks Tauriq Stanley)

= 0.8.2 - 2019-04-26 =
* ENHANCEMENT: Prefix all constants to avoid conflict with other MPesa Plugins.

= 0.8.1 =
* SECURITY: Better sanitization of POST parameters in the ITN hander.
* BUG FIX/ENHANCEMENT: The MPesa signature is really called MPesa PassPhrase now.

= 0.8 =
* Initial release. Pluganized from the MPesa PMPro fork. Includes many bug fixes and improvements.

== Upgrade Notice ==
= 1.3.0 =
* This update includes minor bug fixes and general enhancements.

= 1.2 =
* Please upgrade for Subscriptions Delay support and minor bug fixes.

= 1.1 =
* Please upgrade for fixes to cancellation process.

= 0.9 = 
* Please upgrade for some minor fixes.

= 0.8.4 =
* Please upgrade for bug fixes pertaining to discount codes at checkout.

= 0.8.3 =
* Please upgrade for bug fixes pertaining to recurring membership billing.
