<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

require_once('../models/projects.php');

header('Content-Type: application/json');


$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($searchQuery)) {
    $projects = searchProjects($searchQuery);
} else {
    $projects = getAllProjects();
}

echo json_encode($projects);
?>
