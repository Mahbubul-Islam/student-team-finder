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


if ($_SESSION['user']['role'] !== 'project_owner') {
    echo json_encode(['success' => false, 'message' => 'Only project owners can reject join requests']);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}


$applicationId = $_POST['application_id'] ?? null;

if (!$applicationId) {
    echo json_encode(['success' => false, 'message' => 'Application ID is required']);
    exit();
}

// Get application details to verify ownership
$conn = require_once("../models/dbConnect.php");
$conn = dbConnect();

$sql = "SELECT pa.*, p.owner_id 
        FROM project_applications pa
        INNER JOIN projects p ON pa.project_id = p.project_id
        WHERE pa.application_id = ? AND pa.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $applicationId);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$application) {
    echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
    exit();
}

// Verify that the current user is the project owner
if ($application['owner_id'] != $_SESSION['user']['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You are not authorized to reject this request']);
    exit();
}

// Update application status to rejected
$result = updateApplicationStatus($applicationId, 'rejected');

if ($result) {
    // Get project details and notify applicant
    $project = getProjectById($application['project_id']);
    
    createNotification(
        $application['applicant_id'],
        "Your request to join \"" . htmlspecialchars($project['title']) . "\" has been declined."
    );
    
    echo json_encode(['success' => true, 'message' => 'Join request rejected']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reject join request']);
}
?>
