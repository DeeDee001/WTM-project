<?php
require_once __DIR__ . '/../model/Application.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'recruiter') {
	echo json_encode(['success' => false, 'error' => 'Unauthorized']);
	exit();
}

$job_id = intval($_GET['job_id'] ?? 0);

if ($job_id <= 0) {
	echo json_encode(['success' => false, 'error' => 'Invalid job']);
	exit();
}

$application = new Application();
$counts = $application->getStatusCountsForJob($job_id);

echo json_encode(['success' => true, 'counts' => $counts]);
