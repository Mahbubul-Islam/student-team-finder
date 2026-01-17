<?php

    session_start();
    require_once("../models/users.php");
    $errors = $_SESSION['errors'] ?? [];
    $oldInput = $_SESSION['old_input'] ?? [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $password = "";
        $hasError = false;
        $emailErr = $passwordErr = "";

        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
            $hasError = true;
        } 
        else {
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
                $hasError = true;
            } 
            else {
                $email = trim($_POST["email"]);
            }
        }

        if (empty($_POST["pass"])) {
            $passwordErr = "Password is required";
            $hasError = true;
        } 
        else {
            if (strlen($_POST["pass"]) < 6) {
                $passwordErr = "Password must be at least 6 characters long";
                $hasError = true;
            } else {
                $password = $_POST["pass"];
            }
        }

        if ($hasError) {
            $_SESSION['errors'] = [
                'emailErr' => $emailErr,
                'passwordErr' => $passwordErr
            ];
            $_SESSION['old_input'] = [
                'email' => $email
            ];
            header("Location: ../views/login.php");
            exit();
        } 
        else {

           $user = authUser($email, $password);
           
           if(!$user) { 
               $passwordErr = "Invalid email or password";
               $_SESSION['errors'] = [
                   'passwordErr' => $passwordErr
               ];
               $_SESSION['old_input'] = [
                   'email' => $email
               ];
               header("Location: ../views/login.php");
               exit(); 
           }
           
            if($user['role'] == 'admin'){
                if($user['status'] == 'active'){
                    $_SESSION['user'] = $user;
                    header("Location: ../views/admin/dashboard.php");
                    exit();
                } 
                else {
                    $passwordErr = "Your account is not active. Please contact support.";
                    $_SESSION['errors'] = [
                        'passwordErr' => $passwordErr
                    ];
                    $_SESSION['old_input'] = [
                        'email' => $email
                    ];
                    header("Location: ../views/login.php");
                    exit();
                }
            }
            if($user['role'] == 'project_owner'){
                    if($user['status'] == 'active'){
                        $_SESSION['user'] = $user;
                        header("Location: ../views/project_owner/dashboard.php");
                        exit();
                    }
                    else {
                        $passwordErr = "Your account is not active. Please contact support.";
                        $_SESSION['errors'] = [
                            'passwordErr' => $passwordErr
                        ];
                        $_SESSION['old_input'] = [
                            'email' => $email
                        ];
                        header("Location: ../views/login.php");
                        exit();
                    }
                }
            if($user['role'] == 'project_applicant'){
                    if($user['status'] == 'active'){
                        $_SESSION['user'] = $user;
                        header("Location: ../views/project_applicant/dashboard.php");
                        exit();
                    }
                    else {
                        $passwordErr = "Your account is not active. Please contact support.";
                        $_SESSION['errors'] = [
                            'passwordErr' => $passwordErr
                        ];
                        $_SESSION['old_input'] = [
                            'email' => $email
                        ];
                        header("Location: ../views/login.php");
                        exit();
                }
                
            }
        }
    }


?>