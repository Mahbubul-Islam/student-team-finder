<?php
    session_start();
    require_once("../models/users.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $email = $password = $confPassword = $role = $gender = "";
        $hasError = false;
        $nameErr = $emailErr = $passwordErr = $confPasswordErr = $roleErr = $genderErr = "";

        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
            $hasError = true;
        } 
        else {
            if(!preg_match("/^[a-zA-Z-' ]*$/", $_POST["name"])) {
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
            }
        }

        if (empty($_POST["pass"])) {
            $passwordErr = "Password is required";
            $hasError = true;
        } 
        else {
            $password = $_POST["pass"];
        }

        if (empty($_POST["confPass"])) {
            $confPasswordErr = "Confirm Password is required";
            $hasError = true;
        } 
        else {
            $confPassword = $_POST["confPass"];
        }

        if( $password !== $confPassword ) {
            $confPasswordErr = "Passwords do not match";
            $hasError = true;
        }

        if (empty($_POST["role"])) {
            $roleErr = "Role is required";
            $hasError = true;
        } 
        else {
            $role = $_POST["role"];
        }

        if (empty($_POST["gender"])) {
            $genderErr = "Gender is required";
            $hasError = true;
        } 
        else {
            $gender = $_POST["gender"];
        }

        if ($hasError) {
            
            $_SESSION['errors'] = [
                'nameErr' => $nameErr,
                'emailErr' => $emailErr,
                'passwordErr' => $passwordErr,
                'confPasswordErr' => $confPasswordErr,
                'roleErr' => $roleErr,
                'genderErr' => $genderErr
            ];
            $_SESSION['old_input'] = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? '',
                'gender' => $_POST['gender'] ?? ''
            ];
            header("Location: ../views/registration.php");
            exit();
        }

        
        if (getUserByEmail($email)) {
            $_SESSION['errors'] = ['emailErr' => 'Email is already registered.'];
            $_SESSION['old_input'] = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role' => $_POST['role'] ?? '',
                'gender' => $_POST['gender'] ?? ''
            ];
            header("Location: ../views/registration.php");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        createUser($name, $email, $hashedPassword, $role, $gender);
        
        
        unset($_SESSION['errors']);
        unset($_SESSION['old_input']);
        
        
        header("Location: ../views/login.php?registered=success");
        exit();
    }
?>
