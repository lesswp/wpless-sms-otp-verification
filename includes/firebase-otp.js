document.addEventListener("DOMContentLoaded", function () {
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    // OTP Verification Handler
    const otpField = document.querySelector('input[name="billing_otp"]');
    const phoneNumber = document.querySelector('input[name="billing_phone"]');

    if (phoneNumber && otpField) {
        phoneNumber.addEventListener("blur", function () {
            const appVerifier = new firebase.auth.RecaptchaVerifier("recaptcha-container");
            firebase.auth().signInWithPhoneNumber(phoneNumber.value, appVerifier)
                .then((confirmationResult) => {
                    const otp = prompt("Enter the OTP sent to your phone:");
                    return confirmationResult.confirm(otp);
                })
                .then((result) => {
                    sessionStorage.setItem("firebase_otp", otp);
                    alert("Phone number verified successfully!");
                })
                .catch((error) => alert("Error verifying phone number: " + error.message));
        });
    }
});
