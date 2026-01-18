<?php
    session_start();
    require_once("../../models/users.php");

    // Check if user is admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }

    // Get errors from session
    $errors = $_SESSION['add_errors'] ?? [];
    $oldInput = $_SESSION['add_old_input'] ?? [];
    unset($_SESSION['add_errors']);
    unset($_SESSION['add_old_input']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="css/editUserStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="fas fa-user-plus"></i> Add New User</h2>
                <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
            </div>

            <?php if (isset($errors['generalErr'])): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $errors['generalErr']; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/addUserControl.php" method="POST">
                <div class="form-grid">
                    <div class="form-field">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($oldInput['name'] ?? ''); ?>" required>
                        <?php if (isset($errors['nameErr'])): ?>
                            <span class="error-text"><?php echo $errors['nameErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>" required>
                        <?php if (isset($errors['emailErr'])): ?>
                            <span class="error-text"><?php echo $errors['emailErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-user-tag"></i> Role</label>
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin" <?php echo ($oldInput['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="project_owner" <?php echo ($oldInput['role'] ?? '') === 'project_owner' ? 'selected' : ''; ?>>Project Owner</option>
                            <option value="project_applicant" <?php echo ($oldInput['role'] ?? '') === 'project_applicant' ? 'selected' : ''; ?>>Project Applicant</option>
                        </select>
                        <?php if (isset($errors['roleErr'])): ?>
                            <span class="error-text"><?php echo $errors['roleErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-venus-mars"></i> Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo ($oldInput['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($oldInput['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($oldInput['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['genderErr'])): ?>
                            <span class="error-text"><?php echo $errors['genderErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" placeholder="Minimum 6 characters" required>
                        <?php if (isset($errors['passwordErr'])): ?>
                            <span class="error-text"><?php echo $errors['passwordErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                        <?php if (isset($errors['confPasswordErr'])): ?>
                            <span class="error-text"><?php echo $errors['confPasswordErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field full-width">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <select name="status" required>
                            <option value="">Select Status</option>
                            <option value="active" <?php echo ($oldInput['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($oldInput['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <?php if (isset($errors['statusErr'])): ?>
                            <span class="error-text"><?php echo $errors['statusErr']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

               
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-user-plus"></i> Add User
                    </button>
                    <a href="dashboard.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
        });
    </script>
</body>
</html>
