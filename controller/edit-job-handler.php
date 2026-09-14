<?php
require_once __DIR__ . '/../model/Job.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employer') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$job_id = intval($_POST['job_id']);
	$title = $_POST['title'];
	$category = $_POST['category'];
	$description = $_POST['description'];
	$requirements = $_POST['requirements'];
	$salary_range = $_POST['salary_range'];
	$location = $_POST['location'];
	$job_type = $_POST['job_type'];
	$deadline = $_POST['deadline'];

	$job = new Job();

	// Verify ownership
	$job_detail = $job->getJobDetails($job_id);
	if (!$job_detail || $job_detail['employer_id'] != $_SESSION['user_id']) {
		$_SESSION['job_error'] = 'You do not have permission to edit this job.';
		header("Location: ../index.php");
		exit();
	}

	$success = $job->updateJob($job_id, $title, $category, $description, $requirements, $salary_range, $location, $job_type, $deadline, $_SESSION['user_id']);

	if ($success) {
		$_SESSION['dashboard_success'] = 'Job updated successfully!';
		header("Location: ../index.php");
	} else {
		$_SESSION['job_error'] = 'Failed to update job.';
		header("Location: ../view/edit-job.php?id=" . $job_id);
	}
} else {
	header("Location: ../index.php");
}
