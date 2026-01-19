<?php
require_once('../../controllers/authCheck.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Owner Dashboard</title>
    
</head>
<body>
    <?php include('../partials/navbar.php'); ?>
    
    <div class="dashboard-content">
        <div class="welcome-card">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
            
            <div class="info-grid">
                <div class="info-item">
                    <strong>User ID</strong>
                    <?php echo htmlspecialchars($_SESSION['user']['user_id']); ?>
                </div>
                <div class="info-item">
                    <strong>Email</strong>
                    <?php echo htmlspecialchars($_SESSION['user']['email']); ?>
                </div>
                <div class="info-item">
                    <strong>Role</strong>
                    <?php echo htmlspecialchars($_SESSION['user']['role']); ?>
                </div>
                <div class="info-item">
                    <strong>Profile Image</strong>
                    <?php echo htmlspecialchars($_SESSION['user']['profile_image']); ?>
                </div>
            </div>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>
</body>
</html>