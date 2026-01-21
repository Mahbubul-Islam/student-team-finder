<?php
session_start();

// Check authentication and role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_owner') {
    header("Location: ../views/login.php");
    exit();
}

require_once("../models/projects.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $requiredSkills = trim($_POST['required_skills'] ?? '');
    $maxMembers = intval($_POST['max_members'] ?? 0);
    $status = 'active'; // Always set to active by default
    $ownerId = $_SESSION['user']['user_id'];

    // Validation
    $errors = [];

    if (strlen($title) < 5 || strlen($title) > 200) {
        $errors[] = "Title must be between 5 and 200 characters";
    }

    if (strlen($description) < 20) {
        $errors[] = "Description must be at least 20 characters";
    }

    if (empty($requiredSkills)) {
        $errors[] = "Required skills cannot be empty";
    }

    if ($maxMembers < 1 || $maxMembers > 50) {
        $errors[] = "Maximum members must be between 1 and 50";
    }

    if (!in_array($status, ['active', 'closed'])) {
        $errors[] = "Invalid status value";
    }

    // Handle image upload
    $coverImage = 'default_project.png';
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['cover_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        }

        if ($file['size'] > $maxSize) {
            $errors[] = "Image size must be less than 5MB";
        }

        if (empty($errors)) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueName = 'project_' . uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = '../resources/projects/' . $uniqueName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $coverImage = $uniqueName;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }

    // If no errors, create project
    if (empty($errors)) {
        $projectId = createProject($ownerId, $title, $description, $requiredSkills, $maxMembers, $status, $coverImage);
        
        if ($projectId) {
            $_SESSION['project_success_message'] = "Project created successfully!";
            header("Location: ../views/common/project_details.php?id=" . $projectId);
            exit();
        } else {
            $errors[] = "Failed to create project";
        }
    }

    // If there are errors, redirect back with error message
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode(", ", $errors);
        header("Location: ../views/project_owner/createProject.php");
        exit();
    }
} else {
    header("Location: ../views/project_owner/createProject.php");
    exit();
}
?>
