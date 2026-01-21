<?php
require_once("dbConnect.php");

function getAllProjects() {
    $conn = dbConnect();
    
    $sql = "SELECT p.*, u.name as owner_name 
            FROM projects p 
            LEFT JOIN users u ON p.owner_id = u.user_id 
            ORDER BY p.created_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $projects = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();
        return $projects;
    }
    
    $conn->close();
    return [];
}

function getProjectById($projectId) {
    $conn = dbConnect();
    
    $sql = "SELECT p.*, u.name as owner_name 
            FROM projects p 
            LEFT JOIN users u ON p.owner_id = u.user_id 
            WHERE p.project_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $project;
}

function getProjectsByOwnerId($ownerId) {
    $conn = dbConnect();
    
    $sql = "SELECT * FROM projects WHERE owner_id = ? ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ownerId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $projects = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $projects;
}

function deleteProject($projectId) {
    $conn = dbConnect();
    
    $sql = "DELETE FROM projects WHERE project_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function updateProject($projectId, $title, $description, $requiredSkills, $maxMembers, $status, $coverImage) {
    $conn = dbConnect();
    
    $sql = "UPDATE projects 
            SET title = ?, description = ?, required_skills = ?, max_members = ?, status = ?, cover_image = ? 
            WHERE project_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $title, $description, $requiredSkills, $maxMembers, $status, $coverImage, $projectId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function createProject($ownerId, $title, $description, $requiredSkills, $maxMembers, $status, $coverImage) {
    $conn = dbConnect();
    
    $sql = "INSERT INTO projects (owner_id, title, description, required_skills, max_members, status, cover_image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssiis", $ownerId, $title, $description, $requiredSkills, $maxMembers, $status, $coverImage);
    
    if ($stmt->execute()) {
        $projectId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return $projectId;
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

function searchProjects($searchQuery) {
    $conn = dbConnect();
    
    $searchTerm = "%" . $searchQuery . "%";
    
    $sql = "SELECT p.*, u.name as owner_name 
            FROM projects p 
            LEFT JOIN users u ON p.owner_id = u.user_id 
            WHERE p.title LIKE ? 
            OR p.description LIKE ? 
            OR p.required_skills LIKE ?
            ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $projects = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $projects;
}
?>
