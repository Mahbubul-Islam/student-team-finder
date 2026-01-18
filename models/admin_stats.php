<?php
    require_once("dbConnect.php");

    
    function getTotalUserCount() {
        $conn = dbConnect();
        $query = "SELECT COUNT(*) as total FROM users";
        $result = $conn->query($query);
        $data = $result->fetch_assoc();
        $conn->close();
        return $data['total'];
    }

   
    function getActiveUserCount() {
        $conn = dbConnect();
        $query = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
        $result = $conn->query($query);
        $data = $result->fetch_assoc();
        $conn->close();
        return $data['total'];
    }

    
    function getInactiveUserCount() {
        $conn = dbConnect();
        $query = "SELECT COUNT(*) as total FROM users WHERE status = 'inactive'";
        $result = $conn->query($query);
        $data = $result->fetch_assoc();
        $conn->close();
        return $data['total'];
    }

    
    function getTotalProjectCount() {
        $conn = dbConnect();
        $query = "SELECT COUNT(*) as total FROM projects";
        $result = $conn->query($query);
        $data = $result->fetch_assoc();
        $conn->close();
        return $data['total'];
    }

    
    function getAdminStats() {
        return [
            'total_users' => getTotalUserCount(),
            'active_users' => getActiveUserCount(),
            'inactive_users' => getInactiveUserCount(),
            'total_projects' => getTotalProjectCount()
        ];
    }

    
    function getAllUsers() {
        $conn = dbConnect();
        $query = "SELECT user_id, name, email, role, status FROM users ORDER BY created_at DESC";
        $result = $conn->query($query);
        
        $users = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        $conn->close();
        return $users;
    }
?>
