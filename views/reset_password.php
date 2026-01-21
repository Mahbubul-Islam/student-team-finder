<?php
session_start();


if (!isset($_SESSION['reset_email']) || !isset($_SESSION['verified_otp'])) {
    header("Location: forgot_pass.php");
    exit();
}

$errors = $_SESSION['errors'] ?? [];
$successMessage = $_SESSION['success_message'] ?? ''; 

$passwordErr = $errors['passwordErr'] ?? '';
$confirmErr = $errors['confirmErr'] ?? '';

unset($_SESSION['errors']);
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password - Student Team Finder</title>
        <link rel="stylesheet" href="css/forgotPasswordStyle.css">
    </head>
    <body>
        <div class="form-box">
            <form class="form" method="post" action="../controllers/forgotPasswordControl.php">
                <input type="hidden" name="action" value="reset_password">
                
                <span class="title">Reset Password</span>
                <span class="subtitle">Enter your new password</span>
                
                <?php if ($successMessage): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <input 
                        type="password" 
                        class="input" 
                        name="new_password" 
                        placeholder="New Password" 
                        minlength="6"
                    >
                    <?php if ($passwordErr): ?>
                        <span class="error-message"><?php echo htmlspecialchars($passwordErr); ?></span>
                    <?php endif; ?>
                    
                    <input 
                        type="password" 
                        class="input" 
                        name="confirm_password" 
                        placeholder="Confirm Password" 
                        minlength="6"
                    >
                    <?php if ($confirmErr): ?>
                        <span class="error-message"><?php echo htmlspecialchars($confirmErr); ?></span>
                    <?php endif; ?>
                    
                    <div class="password-requirements">
                        <p>Password must be at least 6 characters long</p>
                    </div>
                </div>
                
                <button type="submit">Reset Password</button>
            </form>
            
            <div class="form-section">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>

        <script>
            const newPassword = document.querySelector('input[name="new_password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');
            
            confirmPassword.addEventListener('input', function() {
                if (this.value !== newPassword.value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            newPassword.addEventListener('input', function() {
                if (confirmPassword.value !== '') {
                    if (confirmPassword.value !== this.value) {
                        confirmPassword.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                }
            });
        </script>
    </body>
</html>
