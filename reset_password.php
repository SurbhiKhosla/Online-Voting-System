<?php
require_once("admin/inc/config.php");

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($db, $_GET['token']);
    $fetchToken = mysqli_query($db, "SELECT * FROM password_resets WHERE token = '$token' AND expires_at > NOW()") or die(mysqli_error($db));
    
    if (mysqli_num_rows($fetchToken) > 0) {
        if (isset($_POST['reset_password'])) {
            $new_password = mysqli_real_escape_string($db, sha1($_POST['new_password']));
            $confirm_password = mysqli_real_escape_string($db, sha1($_POST['confirm_password']));
            
            if ($new_password == $confirm_password) {
                $tokenData = mysqli_fetch_assoc($fetchToken);
                $email = $tokenData['email'];
                
                mysqli_query($db, "UPDATE users SET password = '$new_password' WHERE email = '$email'") or die(mysqli_error($db));
                mysqli_query($db, "DELETE FROM password_resets WHERE token = '$token'") or die(mysqli_error($db));
                
                echo "<script>alert('Your password has been reset successfully.'); location.assign('index.php');</script>";
            } else {
                echo "<script>alert('Passwords do not match.');</script>";
            }
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Reset Password - Online Voting System</title>
            <link rel="stylesheet" href="assets/css/bootstrap.min.css">
            <link rel="stylesheet" href="assets/css/style.css">
        </head>
        <body>
            <div class="container h-100">
                <div class="d-flex justify-content-center h-100">
                    <div class="user_card">
                        <div class="d-flex justify-content-center">
                            <div class="brand_logo_container">
                                <img src="assets/images/logo1.gif" class="brand_logo" alt="Logo">
                            </div>
                        </div>
                        <div class="d-flex justify-content-center form_container">
                            <form method="POST">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="new_password" class="form-control input_pass" placeholder="New Password" required />
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="confirm_password" class="form-control input_pass" placeholder="Confirm Password" required />
                                </div>
                                <div class="d-flex justify-content-center mt-3 login_container">
                                    <button type="submit" name="reset_password" class="btn login_btn">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script src="assets/js/jquery.min.js"></script>
            <script src="assets/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
    } else {
        echo "<script>alert('Invalid or expired token.'); location.assign('index.php');</script>";
    }
}
?>
