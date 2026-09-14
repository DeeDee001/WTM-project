<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Job.php';
require_once __DIR__ . '/../model/Category.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	header("Location: login.php");
	exit();
}

$job = new Job();
$total_jobs = $job->countAllJobs();
$total_applications = $job->countAllApplications();
$active_jobs = $job->countActiveJobs();
$total_employers = $job->countTotalEmployers();
$total_seekers = $job->countTotalSeekers();

$category_model = new Category();
$breakdown = $category_model->getApplicationBreakdownByCategory();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Platform Analytics - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/platform-analytics.css' : 'css/platform-analytics.css'); ?>">
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Platform Analytics</h1>

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

		<h3>Per-Category Breakdown</h3>

		<?php if (count($breakdown) > 0) { ?>
			<table>
				<thead>
					<tr>
						<th>Category</th>
						<th>Jobs Posted</th>
						<th>Applications Received</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($breakdown as $row) { ?>
						<tr>
							<td><?php echo htmlspecialchars($row['category']); ?></td>
							<td><?php echo $row['job_count']; ?></td>
							<td><?php echo $row['application_count']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p style="text-align: center; color: #999; padding: 30px;">No categories to report on yet.</p>
		<?php } ?>
	</div>
</body>
</html>
