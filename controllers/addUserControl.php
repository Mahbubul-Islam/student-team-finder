<?php
    session_start();
    require_once("../models/users.php");

   
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header("Location: ../views/login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $email = $password = $confPassword = $role = $gender = $status = "";
        $hasError = false;
        $nameErr = $emailErr = $passwordErr = $confPasswordErr = $roleErr = $genderErr = $statusErr = "";

        // Validate Name
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
                if ($existingUser) {
                    $emailErr = "Email is already registered";
                    $hasError = true;
                }
            }
        }

       
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
            $hasError = true;
        } else {
            if (strlen($_POST["password"]) < 6) {
                $passwordErr = "Password must be at least 6 characters long";
                $hasError = true;
            } else {
                $password = $_POST["password"];
            }
        }

        
        if (empty($_POST["confirm_password"])) {
            $confPasswordErr = "Confirm Password is required";
            $hasError = true;
        } else {
            $confPassword = $_POST["confirm_password"];
        }

        if ($password !== $confPassword) {
            $confPasswordErr = "Passwords do not match";
            $hasError = true;
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

        if ($hasError) {
            $_SESSION['add_errors'] = [
                'nameErr' => $nameErr,
                'emailErr' => $emailErr,
                'passwordErr' => $passwordErr,
                'confPasswordErr' => $confPasswordErr,
                'roleErr' => $roleErr,
                'genderErr' => $genderErr,
                'statusErr' => $statusErr
            ];
            $_SESSION['add_old_input'] = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'status' => $_POST['status'] ?? ''
            ];
            header("Location: ../views/admin/addUser.php");
            exit();
        }

        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        
        $result = createUserWithStatus($name, $email, $hashedPassword, $role, $gender, $status);

        if ($result) {
            $_SESSION['success_message'] = "User added successfully";
            header("Location: ../views/admin/dashboard.php");
        } else {
            $_SESSION['add_errors'] = ['generalErr' => 'Failed to add user'];
            header("Location: ../views/admin/addUser.php");
        }
        exit();
    } else {
        header("Location: ../views/admin/dashboard.php");
        exit();
    }
?>
