<?php
session_start();
require_once("../models/projects.php");
require_once("../models/project_applications.php");
require_once("../models/project_members.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'project_owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$ownerId = $_SESSION['user']['user_id'];
$projects = getProjectsByOwnerId($ownerId);
$joinRequests = getJoinRequestsByProjectOwner($ownerId);


foreach ($projects as &$project) {
    $project['member_count'] = getProjectMemberCount($project['project_id']);
}
unset($project);

$totalProjects = count($projects);
$totalActive = count(array_filter($projects, function($project) {
    return $project['status'] === 'active';
}));
$totalClosed = count(array_filter($projects, function($project) {
    return $project['status'] === 'closed';
}));

header('Content-Type: application/json');
echo json_encode([
    'total_projects' => $totalProjects,
    'active_projects' => $totalActive,
    'closed_projects' => $totalClosed,
    'projects' => $projects,
    'join_requests' => $joinRequests
]);
?>
