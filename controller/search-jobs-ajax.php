<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';
require_once __DIR__ . '/../model/Job.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
	echo json_encode(['success' => false, 'message' => 'Unauthorized']);
	exit();
}

if ($_SESSION['role'] == 'employer') {
	$jobModel = new Job();
	$keyword = $_GET['keyword'] ?? '';
	$jobs = $jobModel->getEmployerJobs($_SESSION['user_id'], $keyword);

	echo json_encode([
		'success' => true,
		'jobs' => $jobs
	]);
	exit();
}

if ($_SESSION['role'] != 'seeker') {
	echo json_encode(['success' => false, 'message' => 'Unauthorized']);
	exit();
}

$jobSeeker = new JobSeeker();

$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$job_type = $_GET['job_type'] ?? '';
$salary_min = $_GET['salary_min'] ?? '';
$salary_max = $_GET['salary_max'] ?? '';

$jobs = $jobSeeker->searchJobs($keyword, $category, $location, $job_type, $salary_min, $salary_max);

foreach ($jobs as &$job) {
	$job['is_saved'] = $jobSeeker->isJobSaved($_SESSION['user_id'], $job['id']);
	$job['has_applied'] = $jobSeeker->hasApplied($job['id'], $_SESSION['user_id']);
}

echo json_encode([
	'success' => true,
	'jobs' => $jobs
]);


