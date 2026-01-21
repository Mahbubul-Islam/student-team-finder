<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
$successMessage = $_SESSION['success_message'] ?? '';

$emailErr = $errors['emailErr'] ?? '';

unset($_SESSION['errors']);
unset($_SESSION['old_input']);
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password - Student Team Finder</title>
        <link rel="stylesheet" href="css/forgotPasswordStyle.css">
    </head>
    <body>
        <div class="form-box">
            <form class="form" method="post" action="../controllers/forgotPasswordControl.php">
                <input type="hidden" name="action" value="request_otp"> 
                
                <span class="title">Forgot Password</span>
                <span class="subtitle">Enter your email address and we'll send you an OTP to reset your password.</span>
                
                <?php if ($successMessage): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <input 
                        type="email" 
                        class="input" 
                        name="email" 
                        placeholder="Enter your email" 
                        value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>"
                    >
                    <?php if ($emailErr): ?>
                        <span class="error-message"><?php echo htmlspecialchars($emailErr); ?></span>
                    <?php endif; ?>
                </div>
                
                <button type="submit">Send OTP</button>
            </form>
            
            <div class="form-section">
                <p>Remember your password? <a href="login.php">Log in</a></p>
                <p>Don't have an account? <a href="registration.php">Sign up</a></p>
            </div>
        </div>
    </body>
</html>
