<?php
    session_start();
    
    
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit();
    }

    require_once('../models/admin_stats.php');

    header('Content-Type: application/json');

    $stats = getAdminStats();

    echo json_encode($stats);
?>
