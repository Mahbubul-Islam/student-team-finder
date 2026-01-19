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

    function createUserWithStatus($name, $email, $hashedPassword, $role, $gender, $status) {
        $conn = dbConnect();
        $query = "INSERT INTO users (name, email, password, role, gender, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $name, $email, $hashedPassword, $role, $gender, $status);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
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

    function getUserById($userId) {
        $conn = dbConnect();
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        } else {
            $user = null;
        }
        
        $stmt->close();
        $conn->close();
        return $user;
    }

    function updateUser($userId, $name, $email, $role, $gender, $status, $profileImage = null) {
        $conn = dbConnect();
        
        if ($profileImage !== null) {
            $query = "UPDATE users SET name = ?, email = ?, role = ?, gender = ?, status = ?, profile_image = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssi", $name, $email, $role, $gender, $status, $profileImage, $userId);
        } else {
            $query = "UPDATE users SET name = ?, email = ?, role = ?, gender = ?, status = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssi", $name, $email, $role, $gender, $status, $userId);
        }
        
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    function updateUserPassword($userId, $newPassword) {
        $conn = dbConnect();
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $hashedPassword, $userId);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    function deleteUser($userId) {
        $conn = dbConnect();
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    function getAllUsers(){
        $conn = dbConnect();
        $query = "SELECT * FROM users";
        $data = mysqli_query($conn, $query);
        $users = [];
        while($row = mysqli_fetch_assoc($data)){
            $users[] = $row;
        }
        mysqli_close($conn);
        return $users;
    }



?>