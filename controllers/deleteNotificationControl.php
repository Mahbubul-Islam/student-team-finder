<?php
session_start();
require_once('../models/notifications.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$notificationId = $_POST['notification_id'] ?? null;
$userId = $_SESSION['user']['user_id'];

if (!$notificationId) {
    echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
    exit();
}

if (deleteNotification($notificationId, $userId)) {
    echo json_encode(['success' => true, 'message' => 'Notification deleted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete notification']);
}
?>
