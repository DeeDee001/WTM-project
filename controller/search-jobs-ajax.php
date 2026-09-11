<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
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

// Add is_saved and has_applied flags for each job
foreach ($jobs as &$job) {
	$job['is_saved'] = $jobSeeker->isJobSaved($_SESSION['user_id'], $job['id']);
	$job['has_applied'] = $jobSeeker->hasApplied($job['id'], $_SESSION['user_id']);
}

echo json_encode([
	'success' => true,
	'jobs' => $jobs
]);
