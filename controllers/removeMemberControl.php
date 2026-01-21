<?php
session_start();
require_once('../models/project_members.php');
require_once('../models/projects.php');
require_once('../models/notifications.php');
require_once('../models/users.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_owner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$projectId = $_POST['project_id'] ?? null;
$userId = $_POST['user_id'] ?? null;

if (!$projectId || !$userId) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Verify project ownership
$project = getProjectById($projectId);
if (!$project || $project['owner_id'] != $_SESSION['user']['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You are not the owner of this project']);
    exit();
}

// Remove member
if (removeProjectMember($projectId, $userId)) {
    // Create notification for removed member
    $memberInfo = getUserById($userId);
    createNotification(
        $userId,
        "You have been removed from the project: " . htmlspecialchars($project['title'])
    );
    
    echo json_encode(['success' => true, 'message' => 'Member removed successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove member']);
}
?>
