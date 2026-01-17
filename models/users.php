<?php
    require_once("dbConnect.php");

    function getUserByEmail($email) {
        $conn = dbConnect();
        $query = "SELECT * FROM users WHERE email = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $data = $stmt->get_result();
        if ($data->num_rows > 0) {
            $user = $data->fetch_assoc();
        } else {
            $user = null;
        }
        $stmt->close();
        $conn->close();
        return $user;
    }

    function createUser($name, $email, $hashedPassword, $role, $gender) {
        $conn = dbConnect();
        $query = "INSERT INTO users (name, email, password, role, gender) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $role, $gender);
        $stmt->execute();
        // $userRole 
        $stmt->close();
        $conn->close();
        // return $userRole;
    }

    // function getRoleByEmail($email) {
    //     $conn = dbConnect();
    //     $query = "SELECT * FROM users WHERE email = ?";

    //     $stmt = $conn->prepare($query);
    //     $stmt->bind_param("s", $email);
    //     $stmt->execute();
    //     $data = $stmt->get_result();
    //     if ($data->num_rows > 0) {
    //         $user = $data->fetch_assoc();
    //     } else {
    //         $user = null;
    //     }
    //     $stmt->close();
    //     $conn->close();
    //     return $user;
    // }

    function authUser($email, $password){
        $conn = dbConnect();
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $data = $stmt->get_result(); 
        
        if ($data->num_rows > 0) {
            $user = $data->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $stmt->close();
                $conn->close();
                return $user;
            }
        }
        
        $stmt->close();
        $conn->close();
        return null;
    }



?>