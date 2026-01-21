<?php
session_start();
require_once("../models/project_applications.php");
require_once("../models/project_members.php");
require_once("../models/projects.php");
require_once("../models/notifications.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SESSION['user']['role'] !== 'project_owner') {
    echo json_encode(['success' => false, 'message' => 'Only project owners can accept join requests']);
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


$application = getPendingApplicationById($applicationId);

if (!$application) {
    echo json_encode(['success' => false, 'message' => 'Application not found or already processed']);
    exit();
}


if ($application['owner_id'] != $_SESSION['user']['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You are not authorized to accept this request']);
    exit();
}

$currentMemberCount = getProjectMemberCount($application['project_id']);
if ($currentMemberCount >= $application['max_members']) {
    echo json_encode(['success' => false, 'message' => 'Project has reached maximum member limit']);
    exit();
}


$memberAdded = addProjectMember($application['project_id'], $application['applicant_id']);

if (!$memberAdded) {
    echo json_encode(['success' => false, 'message' => 'Failed to add member. User may already be a member.']);
    exit();
}


$statusUpdated = updateApplicationStatus($applicationId, 'accepted');

if ($statusUpdated) {
    
    $project = getProjectById($application['project_id']);
    
    
    createNotification(
        $application['applicant_id'],
        "Your request to join \"" . htmlspecialchars($project['title']) . "\" has been accepted!"
    );
    
   
    $newMemberCount = getProjectMemberCount($application['project_id']);
    if ($newMemberCount >= $application['max_members']) {
        $conn = dbConnect();
        $closeSql = "UPDATE projects SET status = 'closed' WHERE project_id = ?";
        $closeStmt = $conn->prepare($closeSql);
        $closeStmt->bind_param("i", $application['project_id']);
        $closeStmt->execute();
        $closeStmt->close();
        $conn->close();
        
        echo json_encode(['success' => true, 'message' => 'Join request accepted successfully. Project is now full and has been closed.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Join request accepted successfully']);
    }
} else {
    
    removeProjectMember($application['project_id'], $application['applicant_id']);
    echo json_encode(['success' => false, 'message' => 'Failed to update application status']);
}
?>
