<?php
require_once("admin/inc/config.php");

if (isset($_POST['contact_no'])) {
    $contact_no = mysqli_real_escape_string($db, $_POST['contact_no']);
    
    $fetchUser = mysqli_query($db, "SELECT * FROM users WHERE contact_no = '$contact_no'") or die(mysqli_error($db));
    
    if (mysqli_num_rows($fetchUser) > 0) {
        $user = mysqli_fetch_assoc($fetchUser);
        $token = bin2hex(random_bytes(50)); // Generate a unique token
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour

        // Store token in database
        mysqli_query($db, "INSERT INTO password_resets (contact_no, token, expires_at) VALUES ('$contact_no', '$token', '$expiry')") or die(mysqli_error($db));
        
        // Send reset link via email
        $resetLink = "http://localhost/OnlineVotingSystem/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $resetLink";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($contact_no, $subject, $message, $headers)) {
            echo "<script>alert('Password reset link has been sent to your email.'); location.assign('index.php');</script>";
        } else {
            echo "<script>alert('Failed to send email. Please try again.'); location.assign('index.php');</script>";
        }
    } else {
        echo "<script>alert('Email address not found.'); location.assign('index.php');</script>";
    }
}
?>
