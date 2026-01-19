<?php
    session_start();
    require_once("../../models/users.php");

    
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }

    
    $userId = $_GET['id'] ?? null;
    if (!$userId) {
        header("Location: dashboard.php");
        exit();
    }

    
    $user = getUserById($userId);
    if (!$user) {
        header("Location: dashboard.php");
        exit();
    }

    
    $errors = $_SESSION['edit_errors'] ?? [];
    unset($_SESSION['edit_errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Dashboard</title>
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
                <h2><i class="fas fa-user-edit"></i> Edit User</h2>
                <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
            </div>

            <?php if (isset($errors['generalErr'])): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $errors['generalErr']; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/updateUserControl.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">

                
                <div class="profile-section">
                    <div class="current-image">
                        <?php 
                        $imagePath = (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png') 
                            ? "../../resources/{$user['role']}/image/{$user['profile_image']}" 
                            : "../../resources/default.png";
                        ?>
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                             alt="Profile" id="imagePreview">
                    </div>
                    <div class="upload-section">
                        <label for="profile_image" class="upload-btn">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/png, image/jpg, image/jpeg" style="display:none;">
                        <span class="file-note">Max 5MB, PNG or JPG</span>
                        <?php if (isset($errors['imageErr'])): ?>
                            <span class="error-text"><?php echo $errors['imageErr']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="form-grid">
                    <div class="form-field">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        <?php if (isset($errors['nameErr'])): ?>
                            <span class="error-text"><?php echo $errors['nameErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <?php if (isset($errors['emailErr'])): ?>
                            <span class="error-text"><?php echo $errors['emailErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-user-tag"></i> Role</label>
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="project_owner" <?php echo $user['role'] === 'project_owner' ? 'selected' : ''; ?>>Project Owner</option>
                            <option value="project_applicant" <?php echo $user['role'] === 'project_applicant' ? 'selected' : ''; ?>>Project Applicant</option>
                        </select>
                        <?php if (isset($errors['roleErr'])): ?>
                            <span class="error-text"><?php echo $errors['roleErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-venus-mars"></i> Gender</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo $user['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <?php if (isset($errors['genderErr'])): ?>
                            <span class="error-text"><?php echo $errors['genderErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field full-width">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <select name="status" required>
                            <option value="">Select Status</option>
                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <?php if (isset($errors['statusErr'])): ?>
                            <span class="error-text"><?php echo $errors['statusErr']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

              
                <div class="password-section">
                    <h3><i class="fas fa-key"></i> Change Password (Optional)</h3>
                    <p class="section-note">Leave blank to keep current password</p>

                    <div class="form-grid">
                        <div class="form-field">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Min 6 characters">
                        </div>

                        <div class="form-field">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" placeholder="Re-enter password">
                        </div>
                    </div>
                    <?php if (isset($errors['passwordErr'])): ?>
                        <span class="error-text"><?php echo $errors['passwordErr']; ?></span>
                    <?php endif; ?>
                </div>

                
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="dashboard.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>
    
</body>
</html>
