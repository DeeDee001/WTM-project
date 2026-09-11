<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';
require_once __DIR__ . '/../model/User.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	header("Location: ../view/search-jobs.php");
	exit();
}

$job_id = $_POST['job_id'] ?? null;
$cover_letter = $_POST['cover_letter'] ?? '';
$resume_option = $_POST['resume_option'] ?? 'upload';

if (!$job_id || empty($cover_letter)) {
	$_SESSION['error'] = 'Please fill all required fields.';
	header("Location: ../view/apply-job.php?id=" . $job_id);
	exit();
}

$jobSeeker = new JobSeeker();
$seeker_id = $_SESSION['user_id'];

// Check if already applied
if ($jobSeeker->hasApplied($job_id, $seeker_id)) {
	$_SESSION['error'] = 'You have already applied for this job.';
	header("Location: ../view/search-jobs.php");
	exit();
}

$resume_path = '';

// Handle resume
if ($resume_option === 'profile') {
	// Use profile resume
	$userModel = new User();
	$user = $userModel->getUserById($seeker_id);
	$resume_path = $user['resume_path'];

	if (empty($resume_path)) {
		$_SESSION['error'] = 'No resume found in your profile. Please upload one.';
		header("Location: ../view/apply-job.php?id=" . $job_id);
		exit();
	}
} else {
	// Upload new resume
	if (!isset($_FILES['resume_file']) || $_FILES['resume_file']['error'] != UPLOAD_ERR_OK) {
		$_SESSION['error'] = 'Please upload a resume file.';
		header("Location: ../view/apply-job.php?id=" . $job_id);
		exit();
	}

	$file = $_FILES['resume_file'];
	$allowed_ext = ['pdf'];
	$max_size = 5 * 1024 * 1024; // 5MB

	$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

	if (!in_array($file_ext, $allowed_ext)) {
		$_SESSION['error'] = 'Only PDF files are allowed.';
		header("Location: ../view/apply-job.php?id=" . $job_id);
		exit();
	}

	if ($file['size'] > $max_size) {
		$_SESSION['error'] = 'File size must be less than 5MB.';
		header("Location: ../view/apply-job.php?id=" . $job_id);
		exit();
	}

	// Create uploads directory if it doesn't exist
	$upload_dir = __DIR__ . '/../uploads/resumes/';
	if (!file_exists($upload_dir)) {
		mkdir($upload_dir, 0755, true);
	}

	// Generate unique filename
	$new_filename = time() . '_' . $seeker_id . '_' . basename($file['name']);
	$upload_path = $upload_dir . $new_filename;

	if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
		$_SESSION['error'] = 'Failed to upload resume. Please try again.';
		header("Location: ../view/apply-job.php?id=" . $job_id);
		exit();
	}

	$resume_path = 'uploads/resumes/' . $new_filename;
}

// Submit application
$result = $jobSeeker->applyForJob($job_id, $seeker_id, $cover_letter, $resume_path);

if ($result) {
	$_SESSION['success'] = 'Application submitted successfully!';
	header("Location: ../view/my-applications.php");
} else {
	$_SESSION['error'] = 'Failed to submit application. You may have already applied.';
	header("Location: ../view/apply-job.php?id=" . $job_id);
}
