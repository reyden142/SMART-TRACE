<?php
// Include any necessary files or configurations

// Check if the form is submitted
if(isset($_POST['reset_pass'])) {
    // Retrieve the email from the form
    $email = $_POST['email'];

    // Perform validation on the email if necessary

    // Generate a random password reset token
    $token = bin2hex(random_bytes(32)); // Generates a 64-character hexadecimal string

    // Store the token in the database along with the user's email
    // You may need to have a database connection and update the user's record with this token

    // Example:
    // $sql = "UPDATE users SET reset_token = '$token' WHERE email = '$email'";
    // Execute the SQL query

    // Send the reset link to the user's email
    $reset_link = "http://localhost/rfid_ips/reset_password.php?email=" . urlencode($email) . "&token=" . urlencode($token);
    $subject = "Password Reset Link";
    $message = "Click on the following link to reset your password: $reset_link";
    $headers = "From: your_email@example.com"; // Change this to your email address

    // Send the email
    if(mail($email, $subject, $message, $headers)) {
        // Redirect the user back to the login page with a success message
        header("Location: login.php?reset=success");
        exit();
    } else {
        // Redirect the user back to the login page with an error message
        header("Location: login.php?error=email");
        exit();
    }
} else {
    // Redirect the user back to the login page if they access this page directly
    header("Location: login.php");
    exit();
}
?>
