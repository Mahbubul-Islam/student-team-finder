<?php
    require_once("dbConnect.php");

    function generateOTP() {
        return sprintf("%06d", mt_rand(0, 999999));
    }


    function storeOTP($email, $otp) {
        $conn = dbConnect();
        
        // Delete any existing OTPs for this email
        $deleteQuery = "DELETE FROM password_resets WHERE email = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("s", $email);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        
        $query = "INSERT INTO password_resets (email, otp, expires_at) 
                  VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $otp);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
        
        return $result;
    }


    function verifyOTP($email, $otp) {
        $conn = dbConnect();
        
        $query = "SELECT * FROM password_resets 
                  WHERE email = ? 
                  AND otp = ? 
                  AND expires_at > NOW() 
                  AND is_used = FALSE 
                  ORDER BY created_at DESC 
                  LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $reset = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            return $reset;
        }
        
        $stmt->close();
        $conn->close();
        return null;
    }


    function markOTPAsUsed($email, $otp) {
        $conn = dbConnect();
        
        $query = "UPDATE password_resets SET is_used = TRUE WHERE email = ? AND otp = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $otp);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
        
        return $result;
    }


    function updatePassword($email, $newPassword) {
        $conn = dbConnect();
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $hashedPassword, $email);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
        
        return $result;
    }


    function sendOTPEmail($email, $otp) {
        
        $to = $email;
        $subject = "Password Reset OTP - Student Team Finder";
        $message = "Your OTP for password reset is: $otp\n\n";
        $message .= "This OTP will expire in 15 minutes.\n\n";
        $message .= "If you did not request this, please ignore this email.";
        $headers = "From: noreply@studentteamfinder.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // For testing purposes, write to a log file
        $logFile = __DIR__ . "/../otp_logs.txt";
        $logMessage = date('Y-m-d H:i:s') . " - Email: $email, OTP: $otp\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // Uncomment this line when you have email configured
        // return mail($to, $subject, $message, $headers);
        
        // For now, return true to simulate successful sending
        return true;
    }


    function cleanupExpiredOTPs() {
        $conn = dbConnect();
        
        $query = "DELETE FROM password_resets WHERE expires_at < NOW() OR is_used = TRUE";
        $result = $conn->query($query);
        
        $conn->close();
        
        return $result;
    }
?>
