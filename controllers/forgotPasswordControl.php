<?php
    session_start();
    require_once("../models/users.php");
    require_once("../models/password_reset.php");

   
    $action = $_POST['action'] ?? $_GET['action'] ?? ''; 

    
    if ($action === 'request_otp' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $email = "";
        $hasError = false;
        $emailErr = "";

        
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $hasError = true;
        } 
        else {
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $hasError = true;
            } 
            else {
                $email = trim($_POST["email"]);
            }
        }

        if ($hasError) {
            $_SESSION['errors'] = ['emailErr' => $emailErr];
            $_SESSION['old_input'] = ['email' => $email];
            header("Location: ../views/forgot_pass.php");
            exit();
        }

        
        $user = getUserByEmail($email);
        
        if (!$user) {
            $_SESSION['errors'] = ['emailErr' => 'No account found with this email'];
            $_SESSION['old_input'] = ['email' => $email];
            header("Location: ../views/forgot_pass.php");
            exit();
        }

        
        $otp = generateOTP();
        $stored = storeOTP($email, $otp);

        if ($stored) {
            $sent = sendOTPEmail($email, $otp);
            
            if ($sent) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['success_message'] = 'OTP has been sent to your email';
                cleanupExpiredOTPs();
                header("Location: ../views/verify_otp.php");
                exit();
            } 
            else {
                $_SESSION['errors'] = ['emailErr' => 'Failed to send OTP. Please try again.'];
                header("Location: ../views/forgot_pass.php");
                exit();
            }
        } else {
            $_SESSION['errors'] = ['emailErr' => 'Something went wrong. Please try again.'];
            header("Location: ../views/forgot_pass.php");
            exit();
        }
    }

    
    elseif ($action === 'verify_otp' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $otp = "";
        $hasError = false;
        $otpErr = "";

        
        $email = $_SESSION['reset_email'] ?? '';

        if (empty($email)) {
            header("Location: ../views/forgot_pass.php");
            exit();
        }

        
        if (empty($_POST["otp"])) {
            $otpErr = "OTP is required";
            $hasError = true;
        } else {
            $otp = trim($_POST["otp"]);
            
            if (!preg_match("/^[0-9]{6}$/", $otp)) {
                $otpErr = "OTP must be 6 digits";
                $hasError = true;
            }
        }

        if ($hasError) {
            $_SESSION['errors'] = ['otpErr' => $otpErr];
            header("Location: ../views/verify_otp.php");
            exit();
        }

        
        $resetRecord = verifyOTP($email, $otp);

        if ($resetRecord) {
            $_SESSION['verified_otp'] = $otp;
            $_SESSION['success_message'] = 'OTP verified successfully';
            header("Location: ../views/reset_password.php");
            exit();
        } else {
            $_SESSION['errors'] = ['otpErr' => 'Invalid or expired OTP'];
            header("Location: ../views/verify_otp.php");
            exit();
        }
    }

    
    elseif ($action === 'reset_password' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $newPassword = "";
        $confirmPassword = "";
        $hasError = false;
        $passwordErr = "";
        $confirmErr = "";

        
        $email = $_SESSION['reset_email'] ?? '';
        $otp = $_SESSION['verified_otp'] ?? '';

        if (empty($email) || empty($otp)) {
            header("Location: ../views/forgot_pass.php");
            exit();
        }

       
        if (empty($_POST["new_password"])) {
            $passwordErr = "New password is required";
            $hasError = true;
        } else {
            $newPassword = trim($_POST["new_password"]);
            
            if (strlen($newPassword) < 6) {
                $passwordErr = "Password must be at least 6 characters";
                $hasError = true;
            }
        }

        
        if (empty($_POST["confirm_password"])) {
            $confirmErr = "Please confirm your password";
            $hasError = true;
        } else {
            $confirmPassword = trim($_POST["confirm_password"]);
            
            if ($newPassword !== $confirmPassword) {
                $confirmErr = "Passwords do not match";
                $hasError = true;
            }
        }

        if ($hasError) {
            $_SESSION['errors'] = [
                'passwordErr' => $passwordErr,
                'confirmErr' => $confirmErr
            ];
            header("Location: ../views/reset_password.php");
            exit();
        }

        
        $updated = updatePassword($email, $newPassword);

        if ($updated) {
            
            markOTPAsUsed($email, $otp);
            
            
            unset($_SESSION['reset_email']);
            unset($_SESSION['verified_otp']);
            
            $_SESSION['success_message'] = 'Password reset successfully! You can now login.';
            header("Location: ../views/login.php");
            exit();
        } else {
            $_SESSION['errors'] = ['passwordErr' => 'Failed to update password. Please try again.'];
            header("Location: ../views/reset_password.php");
            exit();
        }
    }

    
    elseif ($action === 'resend_otp' && $_SERVER["REQUEST_METHOD"] == "GET") {
        $email = $_SESSION['reset_email'] ?? '';

        if (empty($email)) {
            header("Location: ../views/forgot_pass.php");
            exit();
        }

        
        $otp = generateOTP();
        $stored = storeOTP($email, $otp);

        if ($stored) {
            $sent = sendOTPEmail($email, $otp);
            
            if ($sent) {
                $_SESSION['success_message'] = 'New OTP has been sent to your email';
            } else {
                $_SESSION['errors'] = ['otpErr' => 'Failed to send OTP'];
            }
        } else {
            $_SESSION['errors'] = ['otpErr' => 'Failed to generate OTP'];
        }

        header("Location: ../views/verify_otp.php");
        exit();
    }

    
    else {
        header("Location: ../views/forgot_pass.php");
        exit();
    }
?>
