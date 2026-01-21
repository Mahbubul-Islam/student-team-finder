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
                        <tr>
                            <td colspan="6" class="no-users">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function loadAdminStats() {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "../../controllers/fetch_admin_stats.php", true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    let data = JSON.parse(xhr.responseText);
                    
                    document.getElementById('totalUsers').textContent = data.total_users;
                    document.getElementById('activeUsers').textContent = data.active_users;
                    document.getElementById('inactiveUsers').textContent = data.inactive_users;
                    document.getElementById('totalProjects').textContent = data.total_projects;
                    document.getElementById('activeProjects').textContent = data.active_projects;
                    document.getElementById('inactiveProjects').textContent = data.inactive_projects;
                } else {
                    console.error('Error fetching stats:', xhr.status);
                }
            };

            xhr.onerror = function () {
                console.error('Request failed');
            };

            xhr.send();
        }

        function loadAllUsers() {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "../../controllers/fetch_all_users.php", true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    let users = JSON.parse(xhr.responseText);
                    let tbody = document.getElementById('usersTableBody');
                    
                    if (users.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="no-users">No users found</td></tr>';
                        return;
                    }

                    let html = '';
                    users.forEach(function(user) {
                        let statusClass = user.status === 'active' ? 'active' : 'inactive';
                        
                        html += '<tr>';
                        html += '<td>' + user.user_id + '</td>';
                        html += '<td>' + user.name + '</td>';
                        html += '<td>' + user.email + '</td>';
                        html += '<td><span class="role-badge">' + user.role.replace('_', ' ') + '</span></td>';
                        html += '<td><span class="status-badge ' + statusClass + '">' + user.status + '</span></td>';
                        html += '<td>';
                        html += '<div class="action-buttons">';
                        html += '<button class="edit-btn" onclick="editUser(' + user.user_id + ')" title="Edit User"><i class="fas fa-edit"></i></button>';
                        html += '<button class="delete-btn" onclick="deleteUser(' + user.user_id + ', \'' + user.name + '\')" title="Delete User"><i class="fas fa-trash"></i></button>';
                        html += '</div>';
                        html += '</td>';
                        html += '</tr>';
                    });

                    tbody.innerHTML = html;
                } else {
                    console.error('Error fetching users:', xhr.status);
                    document.getElementById('usersTableBody').innerHTML = 
                        '<tr><td colspan="6" class="no-users">Error loading users</td></tr>';
                }
            };

            xhr.onerror = function () {
                console.error('Request failed');
                document.getElementById('usersTableBody').innerHTML = 
                    '<tr><td colspan="6" class="no-users">Error loading users</td></tr>';
            };

            xhr.send();
        }

        function editUser(userId) {
            window.location.href = 'editUser.php?id=' + userId;
        }

        function deleteUser(userId, userName) {
            if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "../../controllers/deleteUserControl.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            alert('User deleted successfully');
                            loadAllUsers();
                            loadAdminStats();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    } else {
                        alert('Error deleting user');
                    }
                };

                xhr.onerror = function () {
                    alert('Request failed');
                };

                xhr.send('user_id=' + userId);
            }
        }

        
        loadAdminStats(); 
        loadAllUsers();
        
        
        setInterval(function() {
            loadAdminStats();
            loadAllUsers();
        }, 5000);
    </script>

    <?php include('../partials/footer.php'); ?>
</body>
</html>