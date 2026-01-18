<?php
    session_start();
    require_once("../models/users.php");

    
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }

    
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit();
    }

    
    $userId = $_POST["user_id"] ?? null;

    if (empty($userId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        exit();
    }


    if ($userId == $_SESSION['user']['user_id']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
        exit();
    }

    
    $user = getUserById($userId);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }

    
    if (!empty($user['profile_image'])) {
        $imagePath = "../resources/" . $user['role'] . "/image/" . $user['profile_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    
    $result = deleteUser($userId);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
?>
