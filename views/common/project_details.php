<?php
    session_start();
    require_once("../../models/projects.php");

    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }

    $projectId = $_GET['id'] ?? null;
    if (!$projectId) {
        header("Location: home.php");
        exit();
    }

    $project = getProjectById($projectId);
    if (!$project) {
        header("Location: home.php");
        exit();
    }

    $isAdmin = $_SESSION['user']['role'] === 'admin';
    $isOwner = $_SESSION['user']['user_id'] == $project['owner_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - Project Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="css/projectDetailsStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-alert">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="project-details-container">
            <div class="details-header">
                <div class="header-left">
                    <a href="home.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
                    <h1><?php echo htmlspecialchars($project['title']); ?></h1>
                    <span class="status-badge <?php echo $project['status']; ?>">
                        <i class="fas fa-circle"></i> <?php echo ucfirst($project['status']); ?>
                    </span>
                </div>
                
                <?php if ($isAdmin || $isOwner): ?>
                <div class="header-actions">
                    <button class="btn-edit" onclick="editProject(<?php echo $project['project_id']; ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete" onclick="deleteProject(<?php echo $project['project_id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="project-content">
                <div class="project-image">
                    <?php 
                    $coverImage = (!empty($project['cover_image']) && $project['cover_image'] !== 'default_project.png') 
                        ? "../../resources/projects/{$project['cover_image']}" 
                        : "../../resources/default_project.png";
                    ?>
                    <img src="<?php echo htmlspecialchars($coverImage); ?>" 
                         alt="<?php echo htmlspecialchars($project['title']); ?>">
                </div>

                <div class="project-info-grid">
                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-user"></i> Project Owner
                        </div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($project['owner_name']); ?>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-users"></i> Team Size
                        </div>
                        <div class="info-value">
                            <?php echo $project['max_members']; ?> members
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i> Created Date
                        </div>
                        <div class="info-value">
                            <?php echo date('M d, Y', strtotime($project['created_at'])); ?>
                        </div>
                    </div>

                    <div class="info-card full-width">
                        <div class="info-label">
                            <i class="fas fa-info-circle"></i> Description
                        </div>
                        <div class="info-value description">
                            <?php echo nl2br(htmlspecialchars($project['description'])); ?>
                        </div>
                    </div>

                    <div class="info-card full-width">
                        <div class="info-label">
                            <i class="fas fa-code"></i> Required Skills
                        </div>
                        <div class="info-value">
                            <div class="skills-tags">
                                <?php 
                                $skills = explode(',', $project['required_skills']);
                                foreach ($skills as $skill): 
                                    $skill = trim($skill);
                                ?>
                                    <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/project_details.js"></script>
</body>
</html>
