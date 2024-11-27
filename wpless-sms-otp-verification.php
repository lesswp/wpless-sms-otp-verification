<?php
/*
Plugin Name: WP Less SMS OTP Verification
Description: Use Firebase for OTP verification during WooCommerce checkout.
Version: 1.0
Author: Your Name
*/
 // Ensure WooCommerce is active
if (!class_exists('WooCommerce')) {
    return;
}

// Add Phone Number Field to Registration and Remove Email Field
function wpless_replace_email_with_phone($fields) {
    // Remove email field
    unset($fields['email']);

    // Add phone field
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

// Save Phone Number as Username During Registration
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

// Replace Email Field with Phone Number in Login Form
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

    // Check if username is not an email and look for the phone number
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
        unset($fields['account_email']); // Remove email field
    }

    // Add Phone Number Field (if not already displayed)
    if (!isset($fields['account_phone'])) {
        $fields['account_phone'] = [
            'type'        => 'tel',
            'label'       => __('Phone Number', 'wpless'),
            'required'    => true,
            'class'       => ['form-row-wide'],
            'placeholder' => __('Enter your phone number', 'wpless'),
        ];
    }

    return $fields;
}
add_filter('woocommerce_edit_account_fields', 'wpless_remove_email_in_account_page');

// Save Phone Number from My Account Form
function wpless_save_account_details($user_id) {
    if (isset($_POST['account_phone']) && !empty($_POST['account_phone'])) {
        update_user_meta($user_id, 'phone', sanitize_text_field($_POST['account_phone']));
    }
}
add_action('woocommerce_save_account_details', 'wpless_save_account_details');
