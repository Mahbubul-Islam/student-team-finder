<?php
    require_once("dbConnect.php");
    
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function generateOTP() {
        return sprintf("%06d", mt_rand(0, 999999));
    }


    function storeOTP($email, $otp) {
        $conn = dbConnect();
        
        
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
        require __DIR__ . '/../vendor/autoload.php'; 

        loadEnvFile(__DIR__ . "/../.env");

        $smtpHost = env('SMTP_HOST', 'smtp.gmail.com');
        $smtpAuth = filter_var(env('SMTP_AUTH', 'true'), FILTER_VALIDATE_BOOLEAN);
        $smtpUser = env('SMTP_USERNAME', '');
        $smtpPass = env('SMTP_PASSWORD', '');
        $smtpPort = (int) env('SMTP_PORT', 465);
        $smtpSecure = env('SMTP_ENCRYPTION', 'ssl');

        $mailFromAddress = env('MAIL_FROM_ADDRESS', $smtpUser);
        $mailFromName = env('MAIL_FROM_NAME', 'TeamConnect');

        if (empty($smtpUser) || empty($smtpPass) || empty($mailFromAddress)) {
            return false;
        }
        
        $mail = new PHPMailer(true);
        
        try {
            
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = $smtpAuth;
            $mail->Username   = $smtpUser;
            $mail->Password   = $smtpPass;
            $mail->SMTPSecure = $smtpSecure === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $smtpPort;
            
            
            $mail->setFrom($mailFromAddress, $mailFromName);
            $mail->addAddress($email);
            
           
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP - TeamConnect';
            $mail->Body    = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <h2>Password Reset Request</h2>
                    <p>Your OTP for password reset is:</p>
                    <h1 style='color: #0066ff; font-size: 32px; letter-spacing: 5px;'>$otp</h1>
                    <p>This OTP will expire in <strong>15 minutes</strong>.</p>
                    <p>If you did not request this, please ignore this email.</p>
                    <hr>
                    <p style='color: #666; font-size: 12px;'>TeamConnect</p>
                </body>
                </html>
            ";
            $mail->AltBody = "Your OTP for password reset is: $otp\n\nThis OTP will expire in 15 minutes.\n\nIf you did not request this, please ignore this email.";
            
            $mail->send();
            
            
            $logFile = __DIR__ . "/../otp_logs.txt";
            $logMessage = date('Y-m-d H:i:s') . " - Email SENT to: $email, OTP: $otp\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return true;
            
        } catch (Exception $e) {
            $logFile = __DIR__ . "/../otp_logs.txt";
            $logMessage = date('Y-m-d H:i:s') . " - Email FAILED to: $email, Error: {$mail->ErrorInfo}\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);
            
            return false;
        }
    }


    function cleanupExpiredOTPs() {
        $conn = dbConnect();
        
        $query = "DELETE FROM password_resets WHERE expires_at < NOW() OR is_used = TRUE";
        $result = $conn->query($query);
        
        $conn->close();
        
        return $result;
    }
?>
