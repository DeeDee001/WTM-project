<?php
require_once __DIR__ . '/../model/Job.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employer') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$employer_id = $_SESSION['user_id'];
	$title = $_POST['title'];
	$category = $_POST['category'];
	$description = $_POST['description'];
	$requirements = $_POST['requirements'];
	$salary_range = $_POST['salary_range'];
	$location = $_POST['location'];
	$job_type = $_POST['job_type'];
	$deadline = $_POST['deadline'];

	$job = new Job();
	$success = $job->createJob($employer_id, $title, $category, $description, $requirements, $salary_range, $location, $job_type, $deadline);

	if ($success) {
		$_SESSION['dashboard_success'] = 'Job posted successfully!';
		header("Location: ../index.php");
	} else {
		$_SESSION['job_error'] = 'Failed to post job. Please try again.';
		header("Location: ../view/create-job.php");
	}
} else {
	header("Location: ../view/create-job.php");
}
