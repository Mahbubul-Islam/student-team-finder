<?php
session_start();
require_once('../models/users.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit();
}

$userId = $_POST['user_id'] ?? null;

if ($userId != $_SESSION['user']['user_id']) {
    $_SESSION['edit_errors']['generalErr'] = 'Unauthorized action';
    header("Location: ../views/common/editProfile.php");
    exit();
}

$errors = [];


$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    $errors['nameErr'] = 'Name is required';
} elseif (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
    $errors['nameErr'] = 'Only letters and spaces allowed';
}


$email = trim($_POST['email'] ?? '');
if (empty($email)) {
    $errors['emailErr'] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['emailErr'] = 'Invalid email format';
} else {
    $existingUser = getUserByEmail($email);
    if ($existingUser && $existingUser['user_id'] != $userId) {
        $errors['emailErr'] = 'Email already exists';
    }
}

$gender = $_POST['gender'] ?? '';
if (empty($gender) || !in_array($gender, ['male', 'female', 'other'])) {
    $errors['genderErr'] = 'Please select a valid gender';
}


$currentUser = getUserById($userId);
$role = $currentUser['role'];
$status = $currentUser['status'];

$profileImage = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    $fileType = $_FILES['profile_image']['type'];
    $fileSize = $_FILES['profile_image']['size'];
    
    if (!in_array($fileType, $allowedTypes)) {
        $errors['imageErr'] = 'Only PNG and JPG images are allowed';
    } elseif ($fileSize > 5 * 1024 * 1024) {
        $errors['imageErr'] = 'Image size must be less than 5MB';
    } else {
        $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profileImage = 'user_' . $userId . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . "/../resources/{$role}/image/";
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $uploadPath = $uploadDir . $profileImage;
        
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath)) {
            $errors['imageErr'] = 'Failed to upload image';
            $profileImage = null;
        } else {
            // Delete old image if exists
            if (!empty($currentUser['profile_image']) && $currentUser['profile_image'] !== 'default.png') {
                $oldImagePath = $uploadDir . $currentUser['profile_image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        }
    }
}


$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (!empty($newPassword) || !empty($confirmPassword)) {
    if (strlen($newPassword) < 6) {
        $errors['passwordErr'] = 'Password must be at least 6 characters';
    } elseif ($newPassword !== $confirmPassword) {
        $errors['passwordErr'] = 'Passwords do not match';
    }
}


if (!empty($errors)) {
    $_SESSION['edit_errors'] = $errors;
    header("Location: ../views/common/editProfile.php");
    exit();
}


if ($profileImage) {
    $result = updateUser($userId, $name, $email, $role, $gender, $status, $profileImage);
} else {
    $result = updateUser($userId, $name, $email, $role, $gender, $status);
}

if ($result) {

    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        updateUserPassword($userId, $hashedPassword);
    }
    
    
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['gender'] = $gender;
    if ($profileImage) {
        $_SESSION['user']['profile_image'] = $profileImage;
    }
    
    header("Location: ../views/common/profile.php");
} else {
    $_SESSION['edit_errors']['generalErr'] = 'Failed to update profile';
    header("Location: ../views/common/editProfile.php");
}
exit();
?>
