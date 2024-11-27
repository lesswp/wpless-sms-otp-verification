document.addEventListener('DOMContentLoaded', function () {
    const firebaseConfig = {
        apiKey: 'YOUR_API_KEY',
        authDomain: 'YOUR_AUTH_DOMAIN',
        projectId: 'YOUR_PROJECT_ID',
        storageBucket: 'YOUR_STORAGE_BUCKET',
        messagingSenderId: 'YOUR_MESSAGING_SENDER_ID',
        appId: 'YOUR_APP_ID',
    };
    
    // Initialize Firebase
    const app = firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();
    
    const phoneNumberInput = document.getElementById('phone-number');
    const sendCodeButton = document.getElementById('send-code');
    
    sendCodeButton.addEventListener('click', function () {
        const phoneNumber = phoneNumberInput.value;
        const appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
            size: 'invisible',
        });

        auth.signInWithPhoneNumber(phoneNumber, appVerifier)
            .then(function (confirmationResult) {
                window.confirmationResult = confirmationResult;
                alert('Verification code sent!');
            })
            .catch(function (error) {
                console.error('Error during Firebase OTP verification: ', error);
            });
    });
});
