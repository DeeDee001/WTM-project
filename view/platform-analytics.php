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
	<style>
		body {
			font-family: Arial, sans-serif;
			background: #f4f7f6;
			margin: 0;
			padding: 0;
		}
		.container {
			max-width: 1000px;
			margin: 30px auto;
			background: white;
			border-radius: 8px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			padding: 30px;
		}
		h1 {
			color: #333;
			border-bottom: 2px solid #667eea;
			padding-bottom: 10px;
		}
		.nav-back {
			margin-bottom: 20px;
		}
		.nav-back a {
			color: #667eea;
			text-decoration: none;
			font-weight: bold;
		}
		.summary-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
			gap: 15px;
			margin: 25px 0;
		}
		.summary-card {
			background: #e8eaf6;
			border-radius: 6px;
			padding: 18px;
			text-align: center;
		}
		.summary-card .number {
			font-size: 28px;
			font-weight: bold;
			color: #667eea;
		}
		.summary-card .label {
			font-size: 13px;
			color: #555;
			margin-top: 5px;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
		}
		th, td {
			border: 1px solid #ddd;
			padding: 12px;
			text-align: left;
		}
		th {
			background: #667eea;
			color: white;
		}
		tr:hover {
			background: #f5f5f5;
		}
		h3 {
			margin-top: 35px;
			color: #333;
		}
	</style>
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
