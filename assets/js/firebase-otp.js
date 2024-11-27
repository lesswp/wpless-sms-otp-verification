import { initializeApp } from 'https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js';
import { getAuth, RecaptchaVerifier, signInWithPhoneNumber } from 'https://www.gstatic.com/firebasejs/9.0.0/firebase-auth.js';

// Firebase configuration (you can load this dynamically from WordPress settings)
const firebaseConfig = {
    apiKey: 'YOUR_API_KEY',
    authDomain: 'YOUR_AUTH_DOMAIN',
    projectId: 'YOUR_PROJECT_ID',
    storageBucket: 'YOUR_STORAGE_BUCKET',
    messagingSenderId: 'YOUR_MESSAGING_SENDER_ID',
    appId: 'YOUR_APP_ID',
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

document.addEventListener('DOMContentLoaded', function () {
    const phoneNumberInput = document.getElementById('phone-number');
    const sendCodeButton = document.getElementById('send-code');

    sendCodeButton.addEventListener('click', function () {
        const phoneNumber = phoneNumberInput.value;

        // Recaptcha verifier
        const recaptchaVerifier = new RecaptchaVerifier('recaptcha-container', {
            size: 'invisible',
        }, auth);

        // Sign in with phone number
        signInWithPhoneNumber(auth, phoneNumber, recaptchaVerifier)
            .then(function (confirmationResult) {
                window.confirmationResult = confirmationResult;
                alert('Verification code sent!');
            })
            .catch(function (error) {
                console.error('Error during Firebase OTP verification: ', error);
                alert('Error: ' + error.message);
            });
    });
});
