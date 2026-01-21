<?php
require_once('../../controllers/authCheck.php');
require_once('../../models/projects.php');
require_once('../../models/project_applications.php');
require_once('../../models/project_members.php');

$ownerId = $_SESSION['user']['user_id'];
$projects = getProjectsByOwnerId($ownerId);
$totalProjects = count($projects);

// Calculate active and closed project counts
$activeProjects = array_filter($projects, function($project) {
    return $project['status'] === 'active';
});
$closedProjects = array_filter($projects, function($project) {
    return $project['status'] === 'closed';
});
$totalActive = count($activeProjects);
$totalClosed = count($closedProjects);

$joinRequests = getJoinRequestsByProjectOwner($ownerId);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Owner Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="css/dashboardStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-bar"></i> My Projects Dashboard</h1>
            <p class="subtitle">Manage and track your projects</p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Projects</h3>
                    <p class="stat-number" id="totalProjects"></p>
                </div>
            </div>
            
            <div class="stat-card active">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>Active Projects</h3>
                    <p class="stat-number" id="activeProjects"></p>
                </div>
            </div>
            
            <div class="stat-card closed">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>Closed Projects</h3>
                    <p class="stat-number" id="closedProjects"></p>
                </div>
            </div>
        </div>

        <div class="join-requests-section">
            <div class="section-header">
                <h2><i class="fas fa-user-plus"></i> Join Requests</h2>
            </div>

            <?php if (count($joinRequests) > 0): ?>
                <div class="table-container">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Project</th>
                                <th>Applied Date</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-requests">
                    <i class="fas fa-inbox"></i>
                    <p>No pending join requests</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="projects-section">
            <div class="section-header">
                <h2><i class="fas fa-tasks"></i> My Projects</h2>
                <button class="btn-create-project" onclick="window.location.href='createProject.php'">
                    <i class="fas fa-plus-circle"></i> Create New Project
                </button>
            </div>

            <?php if (empty($projects)): ?>
                <div class="no-projects">
                    <i class="fas fa-folder-open"></i>
                    <p>You haven't created any projects yet</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="projects-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="projectsTableBody">
                            
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/dashboard.js"></script>
</body>
</html>