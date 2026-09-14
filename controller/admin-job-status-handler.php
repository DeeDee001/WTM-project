<?php
require_once __DIR__ . '/../model/Job.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	echo json_encode(['success' => false, 'error' => 'Unauthorized']);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$job_id = intval($_POST['job_id']);
	$status = $_POST['status'];

	if ($status != 'active' && $status != 'closed') {
		echo json_encode(['success' => false, 'error' => 'Invalid status']);
		exit();
	}

	$job = new Job();
	$job_detail = $job->getJobDetails($job_id);

	if (!$job_detail) {
		echo json_encode(['success' => false, 'error' => 'Job not found']);
		exit();
	}

	// Admin can close/reopen any job, regardless of which employer owns it.
	$success = $job->adminSetStatus($job_id, $status);

	if ($success) {
		echo json_encode(['success' => true]);
	} else {
		echo json_encode(['success' => false, 'error' => 'Update failed']);
	}
} else {
	echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
