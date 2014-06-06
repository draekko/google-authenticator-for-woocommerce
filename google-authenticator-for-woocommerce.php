<?php
/*
Plugin Name: Google Authenticator for WooCommerce
Plugin URI: https://github.com/draekko/google-authenticator-for-woocommerce
Description: Adds Google Authenticator login for WooCommerce. Requires the Google Authenticator plugin to function.
Author: Draekko
Author URI: http://draekko.com
Version: 1.1.5
License: GPLv3 or later
*/

/* Code borrowed from Two Factor Auth for Woocommerce plugin http://wordpress.org/plugins/two-factor-auth-for-woocommerce/  */

function googleAuthAddButtonToWC() {
	if(!is_user_logged_in()) {
		$GAFW_login_label = trim( sanitize_text_field( get_option('googleauthenticatorforwoocommerce_login_label') ) );
		$GAFW_login_button = trim( sanitize_text_field( get_option('googleauthenticatorforwoocommerce_login_button') ) );
		$GAFW_login_empty = trim( sanitize_text_field( get_option('googleauthenticatorforwoocommerce_login_empty') ) );
		if (empty($GAFW_login_label)) {
			$GAFW_login_label = "Google Authenticator token (optional)";
		}
		if (empty($GAFW_login_button)) {
			$GAFW_login_button = "Click to enter Google Authenticator Token";
		}
		if (empty($GAFW_login_empty)) {
			$GAFW_login_empty = 'You have to enter a username first.';
		}
		
		
		wp_enqueue_script('ga-wc-ajax-request', plugin_dir_url(__FILE__) . 'google-auth.js', array('jquery'));
		wp_localize_script('ga-wc-ajax-request', 'ga_wc_settings', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'click_to_enter_otp' => __($GAFW_login_button, 'ga_woocommerce'),
			'enter_username_first' => __($GAFW_login_empty, 'ga_woocommerce'),
			'google_otp' => __($GAFW_login_label, 'ga_woocommerce')
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

/* --------------- ADMIN CODE START --------------- */ 

/** 
* Create admin menu 
*/
function gafw_admin_create_menu() {
    //create new top-level menu
    add_submenu_page('options-general.php', 'Google Authenticator For WooCommerce', 'Google Authenticator For WooCommerce', 'manage_options', 'google-autheticator-for-woocommerce-page', 'gafw_admin_settings_page' );

    //call register settings function
    add_action( 'admin_init', 'register_gafw_settings' );
}
add_action( 'admin_menu', 'gafw_admin_create_menu' );


/** 
* register site options 
*/
function register_gafw_settings() {
    //register our settings
    register_setting( 'googleauthenticatorforwoocommerce-settings-group', 'googleauthenticatorforwoocommerce_login_label' );
    register_setting( 'googleauthenticatorforwoocommerce-settings-group', 'googleauthenticatorforwoocommerce_login_button' );
    register_setting( 'googleauthenticatorforwoocommerce-settings-group', 'googleauthenticatorforwoocommerce_login_empty' );
}

/** 
* admin settings page 
*/
function gafw_admin_settings_page() {

	do_update_admin_settings();
	
	echo "<div class=\"wrap\">\n";
	echo "<h2>Google Authenticator For WooCommerce Settings</h2>";

	$GAFW_login_label = trim( sanitize_text_field( get_option('googleauthenticatorforwoocommerce_login_label') ) );
	$GAFW_login_button = trim( sanitize_text_field( get_option('googleauthenticatorforwoocommerce_login_button') ) );
	$GAFW_login_empty = trim( sanitize_text_field( get_option('googleauthenticatorforwoocommerce_login_empty') ) );
	if (empty($GAFW_login_label)) {
		$GAFW_login_label = "Google Authenticator token (optional)";
	}
	if (empty($GAFW_login_button)) {
		$GAFW_login_button = "Click to enter Google Authenticator Token";
	}
	if (empty($GAFW_login_empty)) {
		$GAFW_login_empty = 'You have to enter a username first.';
	}
	
	echo "<form method=\"post\" action=\"\">";

	settings_fields( 'googleauthenticator-settings-group' ); 
   do_settings_sections( 'googleauthenticator-settings-group' ); ?>
   
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Login label message</th>
        <td><input type="text" name="GAFW_login_label" id="GAFW_login_label" value="<?php echo $GAFW_login_label; ?>" class="regular-text"/></td>
        </tr>

        <tr valign="top">
        <th scope="row">Token button message</th>
        <td><input type="text" name="GAFW_login_button" id="GAFW_login_button" value="<?php echo $GAFW_login_button; ?>"  class="regular-text"/></td>
        </tr>

        <tr valign="top">
        <th scope="row">Empty login message</th>
        <td><input type="text" name="GAFW_login_empty" id="GAFW_login_empty" value="<?php echo $GAFW_login_empty; ?>"  class="regular-text"/></td>
        </tr>
    </table>
    
	<p class="submit">
		<input type="submit" name="gafw_submit" id="gafw_submit" class="button button-primary" value="Save Changes">
	</p>

	</form>
	<?php

	echo "</div>";
}

/**
* updated settings 
*/
function do_update_admin_settings() {
	if (isset($_POST['gafw_submit'])) {
		// Get _POST action
		$GAFW_login_label_saved = trim( sanitize_text_field( $_POST['GAFW_login_label'] ) );
		$GAFW_login_button_saved = trim( sanitize_text_field( $_POST['GAFW_login_button'] ) );
		$GAFW_login_empty_saved = trim( sanitize_text_field( $_POST['GAFW_login_empty'] ) );
	
		// save options
		update_option('googleauthenticatorforwoocommerce_login_label', $GAFW_login_label_saved);
		update_option('googleauthenticatorforwoocommerce_login_button', $GAFW_login_button_saved);
		update_option('googleauthenticatorforwoocommerce_login_empty', $GAFW_login_empty_saved);
	} 
}

/* --------------- ADMIN CODE END --------------- */ 


?>

