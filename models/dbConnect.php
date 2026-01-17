<?php

    function dbConnect(){
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db_name = "student_team_finder";
        $port = 3306;

        $conn = mysqli_connect($host, $user, $pass, $db_name, $port);

        if(!$conn){
            die("Connection failed: " . mysqli_connect_error());
        }
        
        return $conn;
    }

?>