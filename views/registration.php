<?php 
session_start();
$errors = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];


$nameErr = $errors['nameErr'] ?? '';
$emailErr = $errors['emailErr'] ?? '';
$passwordErr = $errors['passwordErr'] ?? '';
$confPasswordErr = $errors['confPasswordErr'] ?? '';
$roleErr = $errors['roleErr'] ?? '';
$genderErr = $errors['genderErr'] ?? '';


unset($_SESSION['errors']);
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/registrationStyle.css">
    </head>
    <body>
        <div class="form-box">
<form class="form" action="../controllers/registerControl.php" method="post">
    <span class="title">Sign up</span>
    <span class="subtitle">Create a free account with your email.</span>

    <select class="input" name="role">
        <option value="" disabled <?php echo empty($oldInput['role']) ? 'selected' : ''; ?>>Sign up as</option>
        <option value="project_owner" <?php echo ($oldInput['role'] ?? '') == 'project_owner' ? 'selected' : ''; ?>>Project Owner</option>
        <option value="project_applicant" <?php echo ($oldInput['role'] ?? '') == 'project_applicant' ? 'selected' : ''; ?>>Project Applicant</option>
    </select>
    <?php if ($roleErr): ?>
        <span style="color: red; font-size: 12px;"><?php echo $roleErr; ?></span>
    <?php endif; ?>
    
    <div class="form-container">
        <input type="text" class="input" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($oldInput['name'] ?? ''); ?>">
        <?php if ($nameErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $nameErr; ?></span>
        <?php endif; ?>
        
		<input type="email" class="input" name="email" placeholder="Email" value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>">
        <?php if ($emailErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $emailErr; ?></span>
        <?php endif; ?>
        
		<input type="password" class="input" name="pass" placeholder="Password">
        <?php if ($passwordErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $passwordErr; ?></span>
        <?php endif; ?>
        
		<input type="password" class="input" name="confPass" placeholder="Confirm Password">
        <?php if ($confPasswordErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $confPasswordErr; ?></span>
        <?php endif; ?>
        
        <div class="gender-selection">
            <div>Select your gender </div>
        <label for="male">
            <input type="radio" name="gender" id="male" value="male" <?php echo ($oldInput['gender'] ?? '') == 'male' ? 'checked' : ''; ?>> Male
        </label>
        <label for="female">
            <input type="radio" name="gender" id="female" value="female" <?php echo ($oldInput['gender'] ?? '') == 'female' ? 'checked' : ''; ?>> Female 
        </label>
        </div>
        <?php if ($genderErr): ?>
            <span style="color: red; font-size: 12px;"><?php echo $genderErr; ?></span>
        <?php endif; ?>
    </div>
    <button>Sign up</button>
</form>
<div class="form-section">
  <p>Have an account? <a href="login.php">Log in</a> </p>
</div>
</div>
    </body>
</html>
