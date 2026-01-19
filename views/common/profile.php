<?php
    session_start();
    require_once("../../models/users.php");

    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }

    $userId = $_SESSION['user']['user_id'];
    $user = getUserById($userId);
    
    if (!$user) {
        $role = $_SESSION['user']['role'];
        header("Location: ../{$role}/dashboard.php");
        exit();
    }
    
    
    $dashboardUrl = '../' . $_SESSION['user']['role'] . '/dashboard.php';
    
    
    $editUrl = "editProfile.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/profileStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <div class="profile-container">
            <div class="profile-header">
                <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            </div>

            <div class="profile-card">
                <div class="profile-image-section">
                    <?php 
                    $imagePath = (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png') 
                        ? "../../resources/{$user['role']}/image/{$user['profile_image']}" 
                        : "../../resources/default.png";
                    ?>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                         alt="Profile Picture" class="profile-picture">
                </div>

                <div class="profile-details">
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-user"></i> Full Name
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-envelope"></i> Email
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-user-tag"></i> Role
                        </div>
                        <div class="detail-value">
                            <span class="role-badge"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-venus-mars"></i> Gender
                        </div>
                        <div class="detail-value">
                            <?php echo ucfirst($user['gender']); ?>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-toggle-on"></i> Status
                        </div>
                        <div class="detail-value">
                            <span class="status-badge <?php echo $user['status']; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="<?php echo $editUrl; ?>" class="btn-edit">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>
</body>
</html>
