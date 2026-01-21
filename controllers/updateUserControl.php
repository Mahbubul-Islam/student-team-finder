<?php
    session_start();
    require_once("../models/users.php");
    require_once("../models/notifications.php");


    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header("Location: ../views/login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userId = $_POST["user_id"] ?? null;
        $name = $email = $role = $gender = $status = "";
        $hasError = false;
        $nameErr = $emailErr = $roleErr = $genderErr = $statusErr = $imageErr = $passwordErr = "";

        
        if (empty($userId)) {
            header("Location: ../views/admin/dashboard.php");
            exit();
        }

        
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
            $hasError = true;
        } else {
            if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST["name"])) {
                $nameErr = "Only letters and white space allowed";
                $hasError = true;
            } else {
                $name = trim($_POST["name"]);
            }
        }

       
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $hasError = true;
        } else {
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $hasError = true;
            } else {
                $email = trim($_POST["email"]);
                
                
                $existingUser = getUserByEmail($email);
                if ($existingUser && $existingUser['user_id'] != $userId) {
                    $emailErr = "Email is already registered to another user";
                    $hasError = true;
                }
            }
        }

        
        if (empty($_POST["role"])) {
            $roleErr = "Role is required";
            $hasError = true;
        } else {
            $allowedRoles = ['admin', 'project_owner', 'project_applicant'];
            if (!in_array($_POST["role"], $allowedRoles)) {
                $roleErr = "Invalid role selected";
                $hasError = true;
            } else {
                $role = $_POST["role"];
            }
        }

        
        if (empty($_POST["gender"])) {
            $genderErr = "Gender is required";
            $hasError = true;
        } else {
            $allowedGenders = ['male', 'female', 'other'];
            if (!in_array($_POST["gender"], $allowedGenders)) {
                $genderErr = "Invalid gender selected";
                $hasError = true;
            } else {
                $gender = $_POST["gender"];
            }
        }

        
        if (empty($_POST["status"])) {
            $statusErr = "Status is required";
            $hasError = true;
        } else {
            $allowedStatuses = ['active', 'inactive'];
            if (!in_array($_POST["status"], $allowedStatuses)) {
                $statusErr = "Invalid status selected";
                $hasError = true;
            } else {
                $status = $_POST["status"];
            }
        }

        
        $profileImagePath = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['profile_image'];
            
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $imageErr = "Error uploading file";
                $hasError = true;
            } else {
                
                $maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
                if ($file['size'] > $maxFileSize) {
                    $imageErr = "File size must be less than 5MB";
                    $hasError = true;
                }

                
                $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
                $fileType = mime_content_type($file['tmp_name']);
                
                if (!in_array($fileType, $allowedTypes)) {
                    $imageErr = "Only PNG and JPG images are allowed";
                    $hasError = true;
                }

                if (!$hasError) {
                    
                    $uploadDir = "../resources/" . $role . "/image/";
                    
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                  
                    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $uniqueFileName = "user_" . $userId . "_" . time() . "." . $fileExtension;
                    $targetPath = $uploadDir . $uniqueFileName;

                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $profileImagePath = $uniqueFileName;
                        
                        
                        $currentUser = getUserById($userId);
                        if ($currentUser && !empty($currentUser['profile_image'])) {
                            $oldImagePath = "../resources/" . $currentUser['role'] . "/image/" . $currentUser['profile_image'];
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    } else {
                        $imageErr = "Failed to upload file";
                        $hasError = true;
                    }
                }
            }
        }

        
        if (!empty($_POST['new_password'])) {
            if (strlen($_POST['new_password']) < 6) {
                $passwordErr = "Password must be at least 6 characters long";
                $hasError = true;
            } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
                $passwordErr = "Passwords do not match";
                $hasError = true;
            }
        }

        if ($hasError) {
            $_SESSION['edit_errors'] = [
                'nameErr' => $nameErr,
                'emailErr' => $emailErr,
                'roleErr' => $roleErr,
                'genderErr' => $genderErr,
                'statusErr' => $statusErr,
                'imageErr' => $imageErr,
                'passwordErr' => $passwordErr
            ];
            header("Location: ../views/admin/editUser.php?id=" . $userId);
            exit();
        }

        
        $updateResult = updateUser($userId, $name, $email, $role, $gender, $status, $profileImagePath);

       
        if (!empty($_POST['new_password'])) {
            updateUserPassword($userId, $_POST['new_password']);
        }

        if ($updateResult) {
            // Send notification to user about profile update by admin
            createNotification(
                $userId,
                "Your profile has been updated by an administrator."
            );
            
            $_SESSION['success_message'] = "User updated successfully";
            
            
            $returnPage = isset($_POST['return']) && $_POST['return'] === 'profile' 
                ? "../views/common/profile.php" 
                : "../views/admin/dashboard.php";
            
            header("Location: " . $returnPage);
        } else {
            $_SESSION['edit_errors'] = ['generalErr' => 'Failed to update user'];
            
            
            $returnParam = isset($_POST['return']) && $_POST['return'] === 'profile' ? '&return=profile' : '';
            header("Location: ../views/admin/editUser.php?id=" . $userId . $returnParam);
        }
        exit();
    } else {
        header("Location: ../views/admin/dashboard.php");
        exit();
    }
?>
