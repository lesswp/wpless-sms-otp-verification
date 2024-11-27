<?php
/**
 * Plugin Name: WPLess SMS OTP Verification
 * Plugin URI: https://example.com
 * Description: SMS OTP verification using Firebase on WooCommerce registration and login.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: wpless-sms-otp-verification
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'WPLSS_SMS_OTP_VERIFICATION_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPLSS_SMS_OTP_VERIFICATION_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once WPLSS_SMS_OTP_VERIFICATION_DIR . 'includes/class-wpless-sms-otp-verification.php';
require_once WPLSS_SMS_OTP_VERIFICATION_DIR . 'includes/class-wpless-settings.php';
require_once WPLSS_SMS_OTP_VERIFICATION_DIR . 'includes/firebase-otp-handler.php';

// Initialize the plugin
function wpless_sms_otp_verification_init() {
    // Settings page setup
    Wpless_Settings::init();
    Wpless_SMS_OTP_Verification::init();
}
add_action( 'plugins_loaded', 'wpless_sms_otp_verification_init' );
