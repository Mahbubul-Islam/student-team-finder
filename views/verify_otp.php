<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_pass.php");
    exit();
}

$errors = $_SESSION['errors'] ?? [];
$successMessage = $_SESSION['success_message'] ?? '';

$otpErr = $errors['otpErr'] ?? '';
$email = $_SESSION['reset_email'];

unset($_SESSION['errors']);
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verify OTP - Student Team Finder</title>
        <link rel="stylesheet" href="css/forgotPasswordStyle.css">
    </head>
    <body>
        <div class="form-box">
            <form class="form" method="post" action="../controllers/forgotPasswordControl.php">
                <input type="hidden" name="action" value="verify_otp">
                
                <span class="title">Verify OTP</span>
                <span class="subtitle">Enter the 6-digit OTP sent to<br><strong><?php echo htmlspecialchars($email); ?></strong></span>
                
                <?php if ($successMessage): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <input 
                        type="text" 
                        class="input otp-input" 
                        name="otp" 
                        placeholder="Enter 6-digit OTP" 
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                    >
                    <?php if ($otpErr): ?>
                        <span class="error-message"><?php echo htmlspecialchars($otpErr); ?></span>
                    <?php endif; ?>
                    
                    <div class="otp-info">
                        <p>OTP expires in 15 minutes</p>
                    </div>
                </div>
                
                <button type="submit">Verify OTP</button>
            </form>
            
            <div class="form-section">
                <p>Didn't receive OTP? <a href="../controllers/forgotPasswordControl.php?action=resend_otp">Resend OTP</a></p>
                <p><a href="forgot_pass.php">Use different email</a></p>
            </div>
        </div>

        <script>
           
            document.querySelector('.otp-input').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        </script>
    </body>
</html>
