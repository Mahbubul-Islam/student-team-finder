<?php
session_start();
require_once('../models/projects.php');
require_once('../models/notifications.php');

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$projectId = $_POST['project_id'] ?? null;

if (!$projectId) {
    echo json_encode(['success' => false, 'message' => 'Project ID required']);
    exit();
}

$project = getProjectById($projectId);

if (!$project) {
    echo json_encode(['success' => false, 'message' => 'Project not found']);
    exit();
}

$isAdmin = $_SESSION['user']['role'] === 'admin';
$isOwner = $_SESSION['user']['user_id'] == $project['owner_id'];

if (!$isAdmin && !$isOwner) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Only admin or project owner can delete']);
    exit();
}

// delete cover photo
if (!empty($project['cover_image']) && $project['cover_image'] !== 'default_project.png') {
    $imagePath = __DIR__ . "/../resources/projects/{$project['cover_image']}";
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

$result = deleteProject($projectId);

header('Content-Type: application/json');

if ($result) {
    // If admin deleted the project, notify the owner
    if ($isAdmin && !$isOwner) {
        createNotification(
            $project['owner_id'],
            "Your project \"" . htmlspecialchars($project['title']) . "\" has been deleted by an administrator."
        );
    }
    
    echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete project']);
}
?>
