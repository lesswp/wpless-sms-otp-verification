document.addEventListener('DOMContentLoaded', function () {
    // Ensure Firebase config exists
    if (!firebaseConfig || Object.keys(firebaseConfig).length === 0) {
        alert('Firebase is not configured. Please contact the administrator.');
        return;
    }

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    const auth = firebase.auth();

    document.getElementById('wpless-send-otp').addEventListener('click', function () {
        const phoneNumber = document.getElementById('wpless-phone').value;
        if (!phoneNumber) {
            alert('Please enter your phone number.');
            return;
        }

        const appVerifier = new firebase.auth.RecaptchaVerifier('wpless-send-otp', {
            size: 'invisible',
        });

        auth.signInWithPhoneNumber(phoneNumber, appVerifier)
            .then(function (confirmationResult) {
                window.confirmationResult = confirmationResult;
                alert('OTP sent. Please check your phone.');
            })
            .catch(function (error) {
                alert('Error sending OTP: ' + error.message);
            });
    });

    document.getElementById('wpless-otp-code').addEventListener('blur', function () {
        const otpCode = this.value;
        if (!otpCode) {
            alert('Please enter the OTP.');
            return;
        }

        window.confirmationResult.confirm(otpCode)
            .then(function (result) {
                alert('Phone number verified successfully.');
            })
            .catch(function (error) {
                alert('Invalid OTP: ' + error.message);
            });
    });
});
