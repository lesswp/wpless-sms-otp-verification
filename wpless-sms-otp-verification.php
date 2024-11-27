<?php
/*
Plugin Name: WP Less SMS OTP Verification
Description: Use Firebase for OTP verification during WooCommerce checkout.
Version: 1.0
Author: Your Name
*/

defined('ABSPATH') || exit;

// Define Plugin Path
define('WPLESS_SMS_OTP_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include Admin Settings
require_once WPLESS_SMS_OTP_PLUGIN_DIR . 'includes/admin-settings.php';

// Enqueue Scripts and Styles
function wpless_sms_otp_enqueue_scripts() {
    if (is_checkout()) {
        wp_enqueue_script('firebase-app', 'https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js', [], null, true);
        wp_enqueue_script('firebase-auth', 'https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js', ['firebase-app'], null, true);
        wp_enqueue_script('firebase-otp', plugins_url('includes/firebase-otp.js', __FILE__), ['firebase-auth'], null, true);
        wp_localize_script('firebase-otp', 'firebaseConfig', get_option('wpless_sms_otp_firebase_config'));
    }
}
add_action('wp_enqueue_scripts', 'wpless_sms_otp_enqueue_scripts');

// Add OTP Field in Checkout
function wpless_sms_otp_add_checkout_field($fields) {
    $fields['billing']['billing_otp'] = [
        'type'        => 'text',
        'label'       => __('OTP Verification', 'wpless'),
        'required'    => true,
        'class'       => ['form-row-wide'],
        'placeholder' => __('Enter OTP', 'wpless'),
    ];
    return $fields;
}
add_filter('woocommerce_billing_fields', 'wpless_sms_otp_add_checkout_field');

// Validate OTP Field
function wpless_sms_otp_validate_checkout_field($posted) {
    if (empty($posted['billing_otp']) || $posted['billing_otp'] !== $_SESSION['firebase_otp']) {
        wc_add_notice(__('Invalid OTP. Please try again.', 'wpless'), 'error');
    }
}
add_action('woocommerce_checkout_process', 'wpless_sms_otp_validate_checkout_field');
// Remove Email Field and Add Phone Number Field in Registration
function wpless_replace_email_with_phone($fields) {
    // Remove the email field
    unset($fields['email']);

    // Add the phone number field
    $fields['phone'] = [
        'type'        => 'tel',
        'label'       => __('Phone Number', 'wpless'),
        'required'    => true,
        'class'       => ['form-row-wide'],
        'placeholder' => __('Enter your phone number', 'wpless'),
    ];

    return $fields;
}
add_filter('woocommerce_registration_fields', 'wpless_replace_email_with_phone');

// Validate Phone Number During Registration
function wpless_validate_phone_on_registration($username, $email, $validation_errors) {
    if (empty($_POST['phone'])) {
        $validation_errors->add('phone_error', __('Phone number is required.', 'wpless'));
    } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $_POST['phone'])) {
        $validation_errors->add('phone_format_error', __('Please enter a valid phone number.', 'wpless'));
    }
}
add_action('woocommerce_register_post', 'wpless_validate_phone_on_registration', 10, 3);

// Save Phone Number as Username
function wpless_save_phone_as_username($customer_id) {
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        update_user_meta($customer_id, 'phone', sanitize_text_field($_POST['phone']));
        wp_update_user([
            'ID'         => $customer_id,
            'user_login' => sanitize_text_field($_POST['phone']),
        ]);
    }
}
add_action('woocommerce_created_customer', 'wpless_save_phone_as_username');

// Replace Login Email Field with Phone Number
function wpless_replace_email_with_phone_in_login($args) {
    $args['label_username'] = __('Phone Number', 'wpless');
    $args['placeholder_username'] = __('Enter your phone number', 'wpless');
    return $args;
}
add_filter('woocommerce_login_form_args', 'wpless_replace_email_with_phone_in_login');

// Authenticate User by Phone Number
function wpless_login_with_phone($user, $username, $password) {
    if (empty($username) || empty($password)) {
        return $user;
    }

    if (!is_email($username)) {
        $user_query = new WP_User_Query([
            'meta_key'   => 'phone',
            'meta_value' => sanitize_text_field($username),
            'number'     => 1,
            'fields'     => 'ID',
        ]);

        $user_id = $user_query->get_results();
        if (!empty($user_id)) {
            $user = get_user_by('id', $user_id[0]);
        }
    }

    return $user;
}
add_filter('authenticate', 'wpless_login_with_phone', 20, 3);

// Remove Email Field from My Account Page
function wpless_remove_email_in_account_page($fields) {
    if (isset($fields['account_email'])) {
        unset($fields['account_email']);
    }
    return $fields;
}
add_filter('woocommerce_edit_account_fields', 'wpless_remove_email_in_account_page');
