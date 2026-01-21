<?php
require_once("dbConnect.php");

function createNotification($userId, $message) {
    $conn = dbConnect();
    
    $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userId, $message);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function getUserNotifications($userId, $limit = 50) {
    $conn = dbConnect();
    
    $sql = "SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $notifications;
}

function getUnreadNotifications($userId) {
    $conn = dbConnect();
    
    $sql = "SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = FALSE 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $notifications;
}

function getUnreadCount($userId) {
    $conn = dbConnect();
    
    $sql = "SELECT COUNT(*) as count FROM notifications 
            WHERE user_id = ? AND is_read = FALSE";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    
    $stmt->close();
    $conn->close();
    
    return $count;
}

function markNotificationAsRead($notificationId, $userId) {
    $conn = dbConnect();
    
    $sql = "UPDATE notifications SET is_read = TRUE 
            WHERE notification_id = ? AND user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notificationId, $userId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function markAllAsRead($userId) {
    $conn = dbConnect();
    
    $sql = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

function deleteNotification($notificationId, $userId) {
    $conn = dbConnect();
    
    $sql = "DELETE FROM notifications WHERE notification_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notificationId, $userId);
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}
?>
