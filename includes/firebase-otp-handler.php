<?php
function wpless_handle_firebase_otp($phone_number) {
    // Get Firebase config JSON from settings
    $firebase_config_json = get_option( 'firebase_config_json' );

    if ( empty( $firebase_config_json ) ) {
        return new WP_Error( 'firebase_config_missing', 'Firebase configuration is missing' );
    }

    // Decode the JSON config into an array
    $firebase_config = json_decode( $firebase_config_json, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return new WP_Error( 'firebase_config_invalid', 'Invalid Firebase JSON configuration' );
    }

    // Initialize Firebase SDK with the config
    // Use the Firebase SDK to set up authentication (this part should be handled in Firebase Admin SDK or similar)

    // Example code for Firebase Admin SDK (on the server):
    // $firebase = new Firebase\FirebaseApp($firebase_config);
    // $auth = $firebase->getAuth();

    // For Firebase phone verification, you'd call the Firebase phone auth function
    // This part would need Firebase SDK to be available on your server

    return true; // Return success or error response
}
