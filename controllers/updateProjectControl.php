<?php
session_start();
require_once("../models/projects.php");


if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/common/home.php");
    exit();
}


$projectId = $_POST['project_id'] ?? null;
if (!$projectId) {
    header("Location: ../views/common/home.php");
    exit();
}


$project = getProjectById($projectId);
if (!$project) {
    header("Location: ../views/common/home.php");
    exit();
}

// only admin or owner can update
$isAdmin = $_SESSION['user']['role'] === 'admin';
$isOwner = $_SESSION['user']['user_id'] == $project['owner_id'];

if (!$isAdmin && !$isOwner) {
    $_SESSION['edit_errors'] = ['generalErr' => 'You are not authorized to edit this project'];
    header("Location: ../views/common/project_details.php?id=" . $projectId);
    exit();
}


$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$requiredSkills = trim($_POST['required_skills'] ?? '');
$maxMembers = $_POST['max_members'] ?? '';
$status = $_POST['status'] ?? '';
$errors = [];


if (empty($title)) {
    $errors['titleErr'] = "Project title is required";
} elseif (strlen($title) < 5 || strlen($title) > 200) {
    $errors['titleErr'] = "Title must be between 5 and 200 characters";
}

if (empty($description)) {
    $errors['descriptionErr'] = "Description is required";
} elseif (strlen($description) < 20) {
    $errors['descriptionErr'] = "Description must be at least 20 characters";
}

if (empty($requiredSkills)) {
    $errors['skillsErr'] = "Required skills are required";
} elseif (strlen($requiredSkills) < 3) {
    $errors['skillsErr'] = "Please enter valid skills";
}

if (empty($maxMembers) || !is_numeric($maxMembers)) {
    $errors['membersErr'] = "Maximum members must be a number";
} elseif ($maxMembers < 1 || $maxMembers > 50) {
    $errors['membersErr'] = "Maximum members must be between 1 and 50";
}

if (empty($status)) {
    $errors['statusErr'] = "Status is required";
} elseif (!in_array($status, ['active', 'closed'])) {
    $errors['statusErr'] = "Invalid status selected";
}


$coverImage = $project['cover_image']; 

if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) { 
    $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    $maxFileSize = 5 * 1024 * 1024; 

    $fileType = $_FILES['cover_image']['type'];
    $fileSize = $_FILES['cover_image']['size'];

    if (!in_array($fileType, $allowedTypes)) {
        $errors['imageErr'] = "Only PNG and JPG images are allowed";
    } elseif ($fileSize > $maxFileSize) {
        $errors['imageErr'] = "File size must be less than 5MB";
    } else {
        $uploadDir = "../resources/projects/";
        
       
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        
        $fileExtension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $newFileName = "project_" . $projectId . "_" . time() . "." . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
            
            if (!empty($project['cover_image']) && 
                $project['cover_image'] !== 'default_project.png' && 
                file_exists($uploadDir . $project['cover_image'])) {
                unlink($uploadDir . $project['cover_image']);
            }
            
            $coverImage = $newFileName;
        } else {
            $errors['imageErr'] = "Failed to upload image";
        }
    }
} elseif (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $errors['imageErr'] = "Error uploading file";
}


if (!empty($errors)) {
    $_SESSION['edit_errors'] = $errors;
    header("Location: ../views/common/editProject.php?id=" . $projectId);
    exit();
}


$updateResult = updateProject($projectId, $title, $description, $requiredSkills, $maxMembers, $status, $coverImage);

if ($updateResult) {
    $_SESSION['success_message'] = "Project updated successfully";
    header("Location: ../views/common/project_details.php?id=" . $projectId);
    exit();
} else {
    $_SESSION['edit_errors'] = ['generalErr' => 'Failed to update project. Please try again.'];
    header("Location: ../views/common/editProject.php?id=" . $projectId);
    exit();
}
?>
