<?php
require_once('../../controllers/authCheck.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Project Applicant Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</h1>
    <p>Role: <?php echo htmlspecialchars($_SESSION['user']['role']); ?></p>
</body>
</html>