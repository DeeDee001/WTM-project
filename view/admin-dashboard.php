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
	<style>
		body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
		.container { max-width: 1200px; margin: 30px auto; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 30px; }
		h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
		.welcome { background: #e8eaf6; padding: 20px; border-radius: 5px; margin-bottom: 25px; }
		.summary-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
			gap: 15px;
			margin-bottom: 30px;
		}
		.summary-card {
			background: #f4f7f6;
			border: 1px solid #e0e0e0;
			border-radius: 6px;
			padding: 18px;
			text-align: center;
		}
		.summary-card .number {
			font-size: 26px;
			font-weight: bold;
			color: #667eea;
		}
		.summary-card .label {
			font-size: 13px;
			color: #555;
			margin-top: 5px;
		}
		.actions {
			display: flex;
			gap: 12px;
			flex-wrap: wrap;
		}
		.btn {
			padding: 12px 22px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			text-decoration: none;
			display: inline-block;
			cursor: pointer;
			font-size: 15px;
		}
		.btn:hover {
			background: #5568d3;
		}
	</style>
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
