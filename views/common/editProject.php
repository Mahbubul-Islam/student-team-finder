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

    if (!$isAdmin && !$isOwner) {
        header("Location: project_details.php?id=" . $projectId);
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
    <title>Edit Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/editProjectStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <h2><i class="fas fa-edit"></i> Edit Project</h2>
                <a href="project_details.php?id=<?php echo $projectId; ?>" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            <?php if (isset($errors['generalErr'])): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $errors['generalErr']; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/updateProjectControl.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project['project_id']); ?>">

                <div class="cover-section">
                    <div class="current-cover">
                        <?php 
                        $coverImage = (!empty($project['cover_image']) && $project['cover_image'] !== 'default_project.png') 
                            ? "../../resources/projects/{$project['cover_image']}" 
                            : "../../resources/default_project.png";
                        ?>
                        <img src="<?php echo htmlspecialchars($coverImage); ?>" 
                             alt="Cover" id="coverPreview">
                    </div>
                    <div class="upload-section">
                        <label for="cover_image" class="upload-btn">
                            <i class="fas fa-camera"></i> Change Cover
                        </label>
                        <input type="file" id="cover_image" name="cover_image" accept="image/png, image/jpg, image/jpeg" style="display:none;">
                        <span class="file-note">Max 5MB, PNG or JPG</span>
                        <?php if (isset($errors['imageErr'])): ?>
                            <span class="error-text"><?php echo $errors['imageErr']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-field full-width">
                        <label><i class="fas fa-heading"></i> Project Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>">
                        <?php if (isset($errors['titleErr'])): ?>
                            <span class="error-text"><?php echo $errors['titleErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field full-width">
                        <label><i class="fas fa-align-left"></i> Description</label>
                        <textarea name="description" rows="6"><?php echo htmlspecialchars($project['description']); ?></textarea>
                        <?php if (isset($errors['descriptionErr'])): ?>
                            <span class="error-text"><?php echo $errors['descriptionErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field full-width">
                        <label><i class="fas fa-code"></i> Required Skills (comma-separated)</label>
                        <input type="text" name="required_skills" 
                               value="<?php echo htmlspecialchars($project['required_skills']); ?>" 
                               placeholder="e.g., PHP, JavaScript, MySQL">
                        <?php if (isset($errors['skillsErr'])): ?>
                            <span class="error-text"><?php echo $errors['skillsErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-users"></i> Maximum Members</label>
                        <input type="number" name="max_members" 
                               value="<?php echo htmlspecialchars($project['max_members']); ?>" 
                               min="1" max="50">
                        <?php if (isset($errors['membersErr'])): ?>
                            <span class="error-text"><?php echo $errors['membersErr']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-field">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <select name="status">
                            <option value="">Select Status</option>
                            <option value="active" <?php echo $project['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="closed" <?php echo $project['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                        </select>
                        <?php if (isset($errors['statusErr'])): ?>
                            <span class="error-text"><?php echo $errors['statusErr']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="project_details.php?id=<?php echo $projectId; ?>" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/editProject.js"></script>
</body>
</html>
