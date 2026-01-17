<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {

    header("Location: ../login.php");
    exit();
}



if (isset($_SESSION['user']['status']) && $_SESSION['user']['status'] !== 'active') {
    session_destroy();
    header("Location: ../login.php?error=account_inactive");
    exit();
}
?>
