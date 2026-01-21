<?php
require_once('../../controllers/authCheck.php');
require_once('../../models/project_applications.php');

$applicantId = $_SESSION['user']['user_id'];
$applications = getApplicationsByApplicant($applicantId);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Applicant Dashboard</title>
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
            <h1><i class="fas fa-paper-plane"></i> My Applications Dashboard</h1>
            <p class="subtitle">Track your join requests and applications</p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-details">
                    <h3>Pending</h3>
                    <p class="stat-number" id="pendingCount"><?php echo count(array_filter($applications, function($app) { return $app['status'] === 'pending'; })); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon accepted">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>Accepted</h3>
                    <p class="stat-number" id="acceptedCount"><?php echo count(array_filter($applications, function($app) { return $app['status'] === 'accepted'; })); ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-details">
                    <h3>Rejected</h3>
                    <p class="stat-number" id="rejectedCount"><?php echo count(array_filter($applications, function($app) { return $app['status'] === 'rejected'; })); ?></p>
                </div>
            </div>
        </div>

        <div class="applications-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> My Applications</h2>
            </div>

            <?php if (empty($applications)): ?>
                <div class="no-applications">
                    <i class="fas fa-inbox"></i>
                    <p>You haven't applied to any projects yet</p>
                    <a href="../common/home.php" class="btn-browse">
                        <i class="fas fa-search"></i> Browse Projects
                    </a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="applications-table">
                        <thead>
                            <tr>
                                <th>Project Title</th>
                                <th>Owner</th>
                                <th>Project Status</th>
                                <th>Application Status</th>
                                <th>Applied Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicationsTableBody">
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td class="project-title">
                                        <i class="fas fa-project-diagram"></i>
                                        <?php echo htmlspecialchars($app['project_title']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($app['owner_name']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $app['project_status']; ?>">
                                            <?php echo ucfirst($app['project_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="application-badge <?php echo $app['status']; ?>">
                                            <i class="fas fa-<?php 
                                                echo $app['status'] === 'pending' ? 'clock' : 
                                                    ($app['status'] === 'accepted' ? 'check-circle' : 'times-circle'); 
                                            ?>"></i>
                                            <?php echo ucfirst($app['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                    <td class="action-buttons">
                                        <button class="btn-view" onclick="window.location.href='../common/project_details.php?id=<?php echo $app['project_id']; ?>'">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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