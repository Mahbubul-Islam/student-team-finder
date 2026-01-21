<?php
session_start();
require_once('../models/notifications.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$userId = $_SESSION['user']['user_id'];
$count = getUnreadCount($userId);

echo json_encode(['success' => true, 'count' => $count]);
?>
