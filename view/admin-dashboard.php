<?php
// Session already started by index.php
require_once __DIR__ . '/../model/Job.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	header("Location: view/login.php");
	exit();
}

$job = new Job();
$total_jobs = $job->countAllJobs();
$active_jobs = $job->countActiveJobs();
$total_applications = $job->countAllApplications();
$total_employers = $job->countTotalEmployers();
$total_seekers = $job->countTotalSeekers();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Admin Dashboard - CareerBridge</title>
	<link rel="stylesheet" href="css/admin-dashboard.css">
</head>
<body>
	<?php $nav_base = 'view/'; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Admin Dashboard</h1>
		<div class="welcome">
			<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
			<p>Here's a snapshot of the whole platform.</p>
		</div>

		<div class="summary-grid">
			<div class="summary-card">
				<div class="number"><?php echo $total_jobs; ?></div>
				<div class="label">Total Jobs</div>
			</div>
			<div class="summary-card">
				<div class="number"><?php echo $active_jobs; ?></div>
				<div class="label">Active Jobs</div>
			</div>
			<div class="summary-card">
				<div class="number"><?php echo $total_applications; ?></div>
				<div class="label">Total Applications</div>
			</div>
			<div class="summary-card">
				<div class="number"><?php echo $total_employers; ?></div>
				<div class="label">Employers</div>
			</div>
			<div class="summary-card">
				<div class="number"><?php echo $total_seekers; ?></div>
				<div class="label">Job Seekers</div>
			</div>
		</div>

		<div class="actions">
			<a href="view/manage-categories.php" class="btn">Manage Categories</a>
			<a href="view/manage-jobs.php" class="btn">Manage Jobs</a>
			<a href="view/platform-analytics.php" class="btn">Platform Analytics</a>
		</div>
	</div>
</body>
</html>
