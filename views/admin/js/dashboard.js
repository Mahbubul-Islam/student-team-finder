
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
    