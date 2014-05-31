<?php
/*
Plugin Name: Google Authenticator for WooCommerce
Plugin URI: https://github.com/draekko/google-authenticator-for-woocommerce
Description: Adds Google Authenticator login for WooCommerce. Requires the Google Authenticator plugin to function.
Author: Draekko
Author URI: http://draekko.com
Version: 1.0.1
License: GPLv3 or later
*/

/* Code borrowed from Two Factor Auth for Woocommerce plugin http://wordpress.org/plugins/two-factor-auth-for-woocommerce/  */

function googleAuthAddButtonToWC() {
	if(!is_user_logged_in()) {
		wp_enqueue_script('ga-wc-ajax-request', plugin_dir_url(__FILE__) . 'google-auth.js', array('jquery'));
		wp_localize_script('ga-wc-ajax-request', 'ga_wc_settings', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'click_to_enter_otp' => __("Click to enter Google Authenticator Token", 'ga_woocommerce'),
			'enter_username_first' => __('You have to enter a username first.', 'ga_woocommerce'),
			'google_otp' => __("Google Authenticator token (optional)", 'ga_woocommerce')
		));
	}
}
add_action('wp_enqueue_scripts', 'googleAuthAddButtonToWC');


function checkGoogleAuthforWCDependencies() {
	if(!is_plugin_active('google-authenticator/google-authenticator.php')) {
		deactivate_plugins(basename(__FILE__));
		die('<p>The <strong>Google Authenticator for WooCommerce</strong> needs the plugin <a href="http://wordpress.org/plugins/google-authenticator/">Google Authenticator</a> to be installed first.</p>');
	}
}
register_activation_hook(__FILE__, 'checkGoogleAuthforWCDependencies');
?>
