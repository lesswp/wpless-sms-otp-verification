<?php
class Wpless_SMS_OTP_Verification {
    public static function init() {
        add_action( 'woocommerce_register_form', array( __CLASS__, 'add_phone_field' ) );
        add_action( 'woocommerce_checkout_process', array( __CLASS__, 'validate_otp_on_checkout' ) );
    }

    // Add phone number field to WooCommerce registration form
    public static function add_phone_field() {
        ?>
        <p class="form-row form-row-first">
            <label for="phone_number"><?php _e( 'Phone Number', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" name="phone_number" id="phone_number" class="input-text" value="" />
        </p>
        <p class="form-row">
            <button type="button" id="send-code" class="button"><?php _e( 'Send OTP', 'woocommerce' ); ?></button>
        </p>
        <div id="recaptcha-container"></div>
        <?php
        wp_enqueue_script( 'firebase-otp', WPLSS_SMS_OTP_VERIFICATION_URL . 'assets/js/firebase-otp.js', array(), null, true );
    }

    // Validate OTP on checkout
    public static function validate_otp_on_checkout() {
        if ( isset( $_POST['phone_number'] ) ) {
            $phone_number = sanitize_text_field( $_POST['phone_number'] );
            $otp = sanitize_text_field( $_POST['otp'] ); // You need to add OTP field in registration form

            // Call Firebase OTP verification here using phone number and OTP
            $firebase_result = wpless_handle_firebase_otp( $phone_number );

            if ( is_wp_error( $firebase_result ) ) {
                wc_add_notice( $firebase_result->get_error_message(), 'error' );
                return;
            }
        }
    }
}
