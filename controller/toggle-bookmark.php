<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	echo json_encode(['success' => false, 'message' => 'Unauthorized']);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	echo json_encode(['success' => false, 'message' => 'Invalid request method']);
	exit();
}

$job_id = $_POST['job_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$job_id || !$action) {
	echo json_encode(['success' => false, 'message' => 'Missing parameters']);
	exit();
}

$jobSeeker = new JobSeeker();
$user_id = $_SESSION['user_id'];

if ($action === 'save') {
	$result = $jobSeeker->saveJob($user_id, $job_id);
} else if ($action === 'unsave') {
	$result = $jobSeeker->unsaveJob($user_id, $job_id);
} else {
	echo json_encode(['success' => false, 'message' => 'Invalid action']);
	exit();
}

if ($result) {
	echo json_encode(['success' => true, 'action' => $action]);
} else {
	echo json_encode(['success' => false, 'message' => 'Database error']);
}
