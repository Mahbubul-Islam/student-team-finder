<?php
session_start();
require_once("../models/project_applications.php");
require_once("../models/projects.php");
require_once("../models/notifications.php");

header('Content-Type: application/json');


if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}


if ($_SESSION['user']['role'] !== 'project_applicant') {
    echo json_encode(['success' => false, 'message' => 'Only project applicants can send join requests']);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}


$projectId = $_POST['project_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$projectId) {
    echo json_encode(['success' => false, 'message' => 'Project ID is required']);
    exit();
}

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please write a message']);
    exit();
}


if (strlen($message) < 20) {
    echo json_encode(['success' => false, 'message' => 'Message must be at least 20 characters']);
    exit();
}

if (strlen($message) > 500) {
    echo json_encode(['success' => false, 'message' => 'Message cannot exceed 500 characters']);
    exit();
}

$applicantId = $_SESSION['user']['user_id'];


$result = createJoinRequest($applicantId, $projectId, $message);

if ($result) {
    $project = getProjectById($projectId);
    $applicantName = $_SESSION['user']['name'];
    
    createNotification(
        $project['owner_id'],
        htmlspecialchars($applicantName) . " has requested to join your project: " . htmlspecialchars($project['title'])
    );
    
    echo json_encode(['success' => true, 'message' => 'Join request sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send join request. You may have already applied.']);
}
?>
