<?php

    function loadEnvFile($path) {
        static $loaded = false;

        if ($loaded || !file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
                $value = substr($value, 1, -1);
            }

            if ($key !== '' && getenv($key) === false) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
            }
        }

        $loaded = true;
    }

    function env($key, $default = null) {
        $value = getenv($key);
        return $value === false ? $default : $value;
    }

    function dbConnect(){
        loadEnvFile(__DIR__ . "/../.env");

        $host = env('DB_HOST', 'localhost');
        $user = env('DB_USER', 'root');
        $pass = env('DB_PASS', '');
        $db_name = env('DB_NAME', 'student_team_finder');
        $port = (int) env('DB_PORT', 3306);

        $conn = mysqli_connect($host, $user, $pass, $db_name, $port);

        if(!$conn){
            die("Connection failed: " . mysqli_connect_error());
        }
        
        return $conn;
    }

?>