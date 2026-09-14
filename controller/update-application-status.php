<?php
require_once __DIR__ . '/../model/Application.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'recruiter') {
	echo json_encode(['success' => false, 'error' => 'Unauthorized']);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
	// PHP doesn't populate $_POST for PUT requests, so the AJAX call's
	// body (application_id=...&status=...) has to be read manually.
	parse_str(file_get_contents('php://input'), $data);

	$application_id = intval($data['application_id'] ?? 0);
	$status = $data['status'] ?? '';

	$application = new Application();

	if (!$application_id || !$application->isValidStatus($status)) {
		echo json_encode(['success' => false, 'error' => 'Invalid status']);
		exit();
	}

	$success = $application->updateStatus($application_id, $status);

	if ($success) {
		echo json_encode(['success' => true, 'status' => $status]);
	} else {
		echo json_encode(['success' => false, 'error' => 'Update failed']);
	}
} else {
	echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
