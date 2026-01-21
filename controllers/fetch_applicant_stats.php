<?php
session_start();
require_once("../models/project_applications.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_applicant') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$applicantId = $_SESSION['user']['user_id'];
$applications = getApplicationsByApplicant($applicantId);

$totalPending = count(array_filter($applications, function($app) { 
    return $app['status'] === 'pending'; 
}));
$totalAccepted = count(array_filter($applications, function($app) { 
    return $app['status'] === 'accepted'; 
}));
$totalRejected = count(array_filter($applications, function($app) { 
    return $app['status'] === 'rejected'; 
}));

header('Content-Type: application/json');
echo json_encode([
    'pending' => $totalPending,
    'accepted' => $totalAccepted,
    'rejected' => $totalRejected,
    'applications' => $applications
]);
?>
