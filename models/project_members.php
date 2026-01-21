<?php
require_once("dbConnect.php");

function addProjectMember($projectId, $userId) {
    $conn = dbConnect();
    
    // Check if member already exists
    $checkSql = "SELECT member_id FROM project_members WHERE project_id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $projectId, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        $checkStmt->close();
        $conn->close();
        return false; // Already a member
    }
    $checkStmt->close();
    
    // Add member
    $sql = "INSERT INTO project_members (project_id, user_id, joined_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $projectId, $userId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function removeProjectMember($projectId, $userId) {
    $conn = dbConnect();
    
    $sql = "DELETE FROM project_members WHERE project_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $projectId, $userId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function getProjectMembers($projectId) {
    $conn = dbConnect();
    
    $sql = "SELECT pm.*, u.name as member_name, u.email as member_email
            FROM project_members pm
            INNER JOIN users u ON pm.user_id = u.user_id
            WHERE pm.project_id = ?
            ORDER BY pm.joined_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $members = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $members;
}

function isProjectMember($projectId, $userId) {
    $conn = dbConnect();
    
    $sql = "SELECT member_id FROM project_members WHERE project_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $projectId, $userId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $isMember = $result->num_rows > 0;
    
    $stmt->close();
    $conn->close();
    
    return $isMember;
}

function getProjectMemberCount($projectId) {
    $conn = dbConnect();
    
    $sql = "SELECT COUNT(*) as count FROM project_members WHERE project_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    
    $stmt->close();
    $conn->close();
    
    return $count;
}
?>
