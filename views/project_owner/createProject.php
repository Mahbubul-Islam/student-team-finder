<?php
require_once('../../controllers/authCheck.php');

// Check if user is a project owner
if ($_SESSION['user']['role'] !== 'project_owner') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="css/createProjectStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                <h1><i class="fas fa-plus-circle"></i> Create New Project</h1>
                <p class="subtitle">Fill in the details to create your project</p>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <form id="createProjectForm" action="../../controllers/createProjectControl.php" method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Project Title *</label>
                        <input type="text" id="title" name="title" minlength="5" maxlength="200" 
                               placeholder="Enter project title (5-200 characters)">
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Description *</label>
                        <textarea id="description" name="description" minlength="20" rows="6"
                                  placeholder="Describe your project in detail (minimum 20 characters)"></textarea>
                        <div class="char-info">
                            <span id="descCharCount">0</span> characters
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="required_skills"><i class="fas fa-tools"></i> Required Skills *</label>
                        <input type="text" id="required_skills" name="required_skills"
                               placeholder="e.g., Python, JavaScript, React (comma-separated)">
                        <small class="form-hint">Separate multiple skills with commas</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_members"><i class="fas fa-users"></i> Maximum Members *</label>
                            <input type="number" id="max_members" name="max_members" min="1" max="50" value="5">
                            <small class="form-hint">Between 1 and 50 members</small>
                        </div>

                        <div class="form-group">
                            <label for="status"><i class="fas fa-toggle-on"></i> Status *</label>
                            <select id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cover_image"><i class="fas fa-image"></i> Cover Image</label>
                        <input type="file" id="cover_image" name="cover_image" accept="image/*">
                        <small class="form-hint">Upload a cover image for your project (JPG, PNG, GIF - max 5MB)</small>
                        
                        <div id="imagePreview" class="image-preview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="window.location.href='dashboard.php'">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/createProject.js"></script>
</body>
</html>
