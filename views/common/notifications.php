<?php
    session_start();
    require_once("../../models/notifications.php");

    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }

    $userId = $_SESSION['user']['user_id'];
    $notifications = getUserNotifications($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" 
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./css/notificationsStyle.css">
</head>
<body>
    <?php include('../partials/navbar.php'); ?>

    <div class="main-content">
        <div class="notifications-container">
            <div class="notifications-header">
                <h1 style="color: whitesmoke;"><i class="fas fa-bell" style="color: #e9eaed;"></i> Notifications</h1>
                <?php if (!empty($notifications)): ?>
                    <button class="btn-mark-all" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                <?php endif; ?>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No notifications yet</h3>
                    <p>You'll see notifications here when there's activity on your projects</p>
                </div>
            <?php else: ?>
                <div class="notifications-list">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                             data-notification-id="<?php echo $notification['notification_id']; ?>">
                            <div class="notification-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-message">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                                <span class="notification-time">
                                    <i class="fas fa-clock"></i>
                                    <?php 
                                        $timestamp = strtotime($notification['created_at']);
                                        $now = time();
                                        $diff = $now - $timestamp;
                                        
                                        if ($diff < 60) {
                                            echo "Just now";
                                        } elseif ($diff < 3600) {
                                            echo floor($diff / 60) . " minutes ago";
                                        } elseif ($diff < 86400) {
                                            echo floor($diff / 3600) . " hours ago";
                                        } elseif ($diff < 604800) {
                                            echo floor($diff / 86400) . " days ago";
                                        } else {
                                            echo date('M d, Y H:i', $timestamp);
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                    <button class="btn-mark-read" 
                                            onclick="markAsRead(<?php echo $notification['notification_id']; ?>)"
                                            title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="btn-delete" 
                                        onclick="deleteNotification(<?php echo $notification['notification_id']; ?>)"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('../partials/footer.php'); ?>

    <script src="js/notifications.js"></script>
</body>
</html>
