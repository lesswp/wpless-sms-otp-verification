<?php
/**
 * Plugin Name: WPLess SMS OTP Verification
 * Description: Replaces WooCommerce email login with phone number and OTP verification using Firebase.
 * Version: 1.1
 * Author: Your Name
 * Text Domain: wpless-sms-otp-verification
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enqueue Firebase and custom script
function wpless_enqueue_firebase_scripts() {
    wp_enqueue_script('firebase', 'https://www.gstatic.com/firebasejs/7.20.0/firebase-app.js', [], null, true);
    wp_enqueue_script('firebase-auth', 'https://www.gstatic.com/firebasejs/7.20.0/firebase-auth.js', ['firebase'], null, true);
    wp_enqueue_script('wpless-otp-script', plugin_dir_url(__FILE__) . 'js/wpless-otp.js', ['jquery', 'firebase-auth'], '1.1', true);

    // Get Firebase configuration from settings
    $firebase_config = get_option('wpless_firebase_config');
    $firebase_config = !empty($firebase_config) ? json_decode($firebase_config, true) : [];

    // Pass Firebase config to the JS file
    wp_localize_script('wpless-otp-script', 'firebaseConfig', $firebase_config);
}
add_action('wp_enqueue_scripts', 'wpless_enqueue_firebase_scripts');

// Add phone number input in WooCommerce login form
function wpless_add_phone_input_in_login() {
    ?>
    <p class="form-row form-row-wide">
        <label for="wpless-phone">Phone Number <span class="required">*</span></label>
        <input type="text" class="input-text" name="wpless_phone" id="wpless-phone" autocomplete="off" />
    </p>
    <p>
        <button id="wpless-send-otp" type="button">Send OTP</button>
        <input type="text" id="wpless-otp-code" placeholder="Enter OTP" name="wpless_otp" />
    </p>
    <div id="wpless-otp-message"></div>
    <?php
}
add_action('woocommerce_login_form', 'wpless_add_phone_input_in_login');

// Remove email field and WooCommerce email login
function wpless_remove_default_email_login($fields) {
    unset($fields['username']);
    return $fields;
}
add_filter('woocommerce_login_form_fields', 'wpless_remove_default_email_login');

// Admin settings to manage Firebase config
function wpless_register_settings() {
    add_option('wpless_firebase_config', '');
    register_setting('wpless_settings_group', 'wpless_firebase_config');
}
add_action('admin_init', 'wpless_register_settings');

function wpless_settings_page() {
    ?>
    <div class="wrap">
        <h1>WPLess SMS OTP Verification Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wpless_settings_group'); ?>
            <?php do_settings_sections('wpless_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Firebase Config (JSON)</th>
                    <td>
                        <textarea name="wpless_firebase_config" rows="10" cols="50"><?php echo esc_textarea(get_option('wpless_firebase_config')); ?></textarea>
                        <p>Enter your Firebase configuration in JSON format. Example:</p>
                        <pre>
{
  "apiKey": "your-api-key",
  "authDomain": "your-auth-domain",
  "projectId": "your-project-id",
  "storageBucket": "your-storage-bucket",
  "messagingSenderId": "your-messaging-sender-id",
  "appId": "your-app-id",
  "measurementId": "your-measurement-id"
}
                        </pre>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function wpless_add_settings_menu() {
    add_options_page(
        'WPLess OTP Settings',
        'WPLess OTP',
        'manage_options',
        'wpless-settings',
        'wpless_settings_page'
    );
}
add_action('admin_menu', 'wpless_add_settings_menu');
