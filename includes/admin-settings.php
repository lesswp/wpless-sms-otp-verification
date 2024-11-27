<?php
function wpless_sms_otp_admin_menu() {
    add_options_page(
        'SMS OTP Verification Settings',
        'SMS OTP Verification',
        'manage_options',
        'wpless-sms-otp-verification',
        'wpless_sms_otp_settings_page'
    );
}
add_action('admin_menu', 'wpless_sms_otp_admin_menu');

function wpless_sms_otp_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        update_option('wpless_sms_otp_firebase_config', wp_unslash($_POST['firebase_config']));
        echo '<div class="updated"><p>Settings Saved.</p></div>';
    }

    $firebase_config = get_option('wpless_sms_otp_firebase_config', '');

    ?>
    <div class="wrap">
        <h1>SMS OTP Verification Settings</h1>
        <form method="post">
            <textarea name="firebase_config" rows="10" cols="50" style="width: 100%;"><?php echo esc_textarea($firebase_config); ?></textarea>
            <p class="description">Paste your Firebase configuration JSON here.</p>
            <p><input type="submit" value="Save Settings" class="button button-primary"></p>
        </form>
    </div>
    <?php
}
