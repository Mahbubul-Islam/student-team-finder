<?php
session_start();
require_once('../models/project_members.php');
require_once('../models/projects.php');
require_once('../models/notifications.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$projectId = $_POST['project_id'] ?? null;

if (!$projectId) {
    echo json_encode(['success' => false, 'message' => 'Missing project ID']);
    exit();
}

$userId = $_SESSION['user']['user_id'];

if (!isProjectMember($projectId, $userId)) {
    echo json_encode(['success' => false, 'message' => 'You are not a member of this project']);
    exit();
}

if (removeProjectMember($projectId, $userId)) {
    $project = getProjectById($projectId);
    $userName = $_SESSION['user']['name'];
    
    createNotification(
        $project['owner_id'],
        htmlspecialchars($userName) . " has left your project: " . htmlspecialchars($project['title'])
    );
    
    echo json_encode(['success' => true, 'message' => 'You have left the project']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to leave project']);
}
?>
