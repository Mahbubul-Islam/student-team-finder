<?php
    session_start();
    require_once("../models/users.php");

    function registerUser($name, $email, $password, $confPassword, $role, $gender) {

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

            if (!$hasError) {
                if (getUserByEmail($email)) {
                    return "Email is already registered.";
                }

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $userId = createUser($name, $email, $hashedPassword, $role, $gender);

                if ($userId) {
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_role'] = $role;
                    return true;
                } 
                else {
                    return "Registration failed. Please try again.";
                }
            }
        }

        // if ($password !== $confPassword) {
        //     return "Passwords do not match.";
        // }

        // if (getUserByEmail($email)) {
        //     return "Email is already registered.";
        // }

        // $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        // $userId = createUser($name, $email, $hashedPassword, $role, $gender);

        // if ($userId) {
        //     $_SESSION['user_id'] = $userId;
        //     $_SESSION['user_name'] = $name;
        //     $_SESSION['user_role'] = $role;
        //     return true;
        // } else {
        //     return "Registration failed. Please try again.";
        // }
    }
?>