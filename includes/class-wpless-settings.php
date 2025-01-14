<?php
class Wpless_Settings {
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    // Add settings page under the "Settings" menu
    public static function add_settings_page() {
        add_options_page(
            'WPLess SMS OTP Verification Settings',
            'WPLess SMS OTP',
            'manage_options',
            'wpless-sms-otp-verification',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    // Register settings fields
    public static function register_settings() {
        register_setting( 'wpless_sms_otp_settings_group', 'firebase_config_json' );
    }

    // Render settings page
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>WPLess SMS OTP Verification Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'wpless_sms_otp_settings_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Firebase Configuration (JSON)</th>
                        <td>
                            <textarea name="firebase_config_json" rows="10" class="large-text"><?php echo esc_textarea( get_option( 'firebase_config_json' ) ); ?></textarea>
                            <p class="description">Paste your Firebase JSON config here. You can find this in your Firebase console.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
