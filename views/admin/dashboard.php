<?php
require_once('../../controllers/authCheck.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboardStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"  />
</head>
<body>
    <?php include('../partials/navbar.php'); ?>
    
    <div class="dashboard-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-alert">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card users">
                <div class="stat-icon"><i class="fa-solid fa-user"></i></div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value" id="totalUsers">0</div>
            </div>

            <div class="stat-card active">
                <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                <div class="stat-label">Active Users</div>
                <div class="stat-value" id="activeUsers">0</div>
            </div>

            <div class="stat-card inactive">
                <div class="stat-icon"><i class="fas fa-user-slash"></i></div>
                <div class="stat-label">Inactive Users</div>
                <div class="stat-value" id="inactiveUsers">0</div>
            </div>

            <div class="stat-card projects">
                <div class="stat-icon"><i class="fa-solid fa-folder"></i></i></div>
                <div class="stat-label">Total Projects</div>
                <div class="stat-value" id="totalProjects">0</div>
            </div>

            <div class="stat-card active-projects">
                <div class="stat-icon"><i class="fa-solid fa-folder-open"></i></div>
                <div class="stat-label">Active Projects</div>
                <div class="stat-value" id="activeProjects">0</div>
            </div>

            <div class="stat-card inactive-projects">
                <div class="stat-icon"><i class="fa-duotone fa-solid fa-folder-closed"></i></div>
                <div class="stat-label">Closed Projects</div>
                <div class="stat-value" id="inactiveProjects">0</div>
            </div>
        </div>

        
        <div class="users-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> All Users</h2>
                <a href="addUser.php" class="add-user-btn">
                    <i class="fas fa-user-plus"></i> Add User
                </a>
            </div>
            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/dashboard.js"></script>

    <?php include('../partials/footer.php'); ?>
</body>
</html>