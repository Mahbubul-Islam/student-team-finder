<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];

$emailErr = $errors['emailErr'] ?? '';
$passwordErr = $errors['passwordErr'] ?? '';

unset($_SESSION['errors']);
unset($_SESSION['old_input']);

?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/loginStyle.css">
    </head>
    <body>
        <div class="form-box">
<form class="form" method="post" action="../controllers/authControl.php">
    <span class="title">Log in</span>
    <span class="subtitle">Log in with your email and password.</span>
    <div class="form-container">
		<input type="email" class="input" name="email" placeholder="Email" value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>">
        <?php if ($emailErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $emailErr; ?></span>
        <?php endif; ?>
		<input type="password" class="input" name="pass" placeholder="Password">
        <?php if ($passwordErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $passwordErr; ?></span>
        <?php endif; ?>
    </div>
    <button>Log in</button>
</form>
<div class="form-section">
  <p>Don't have an account? <a href="registration.php">Sign up</a> </p>
  <p>Forgot password? <a href="forgot_pass.php">Reset here</a></p>
</div>
</div>
    </body>
</html>