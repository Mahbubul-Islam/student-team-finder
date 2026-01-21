<?php
require_once("dbConnect.php");

function hasUserApplied($userId, $projectId) {
    $conn = dbConnect();
    
    $sql = "SELECT application_id FROM project_applications 
            WHERE applicant_id = ? AND project_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $projectId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $hasApplied = $result->num_rows > 0; // 1 > 0 means applied
    
    $stmt->close();
    $conn->close();
    
    return $hasApplied;
}

function createJoinRequest($applicantId, $projectId, $message) {
    $conn = dbConnect();
    
    // Check if already applied
    if (hasUserApplied($applicantId, $projectId)) {
        $conn->close();
        return false;
    }
    
    $sql = "INSERT INTO project_applications (applicant_id, project_id, status, applied_at, message) 
            VALUES (?, ?, 'pending', NOW(), ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $applicantId, $projectId, $message);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function getJoinRequestsByProjectOwner($ownerId) {
    $conn = dbConnect();
    
    $sql = "SELECT pa.*, u.name as applicant_name, u.email as applicant_email, 
                   p.title as project_title, p.project_id
            FROM project_applications pa
            INNER JOIN users u ON pa.applicant_id = u.user_id
            INNER JOIN projects p ON pa.project_id = p.project_id
            WHERE p.owner_id = ? AND pa.status = 'pending'
            ORDER BY pa.applied_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ownerId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $requests = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $requests;
}

function getPendingApplicationById($applicationId) {
    $conn = dbConnect();

    $sql = "SELECT pa.*, p.owner_id, p.max_members, p.project_id
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

    return $application;
}

function getApplicationsByApplicant($applicantId) {
    $conn = dbConnect();
    
    $sql = "SELECT pa.*, p.title as project_title, p.status as project_status,
                   u.name as owner_name
            FROM project_applications pa
            INNER JOIN projects p ON pa.project_id = p.project_id
            INNER JOIN users u ON p.owner_id = u.user_id
            WHERE pa.applicant_id = ?
            ORDER BY pa.applied_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $applicantId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $applications = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $applications;
}

function updateApplicationStatus($applicationId, $status) {
    $conn = dbConnect();
    
    $sql = "UPDATE project_applications SET status = ? WHERE application_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $applicationId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}
?>
