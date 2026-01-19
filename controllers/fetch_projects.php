<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

require_once('../models/projects.php');

header('Content-Type: application/json');

$projects = getAllProjects();

echo json_encode($projects);
?>
