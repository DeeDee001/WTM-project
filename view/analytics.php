<?php
// This page is opened directly (view/analytics.php), not only through
// index.php, so make sure the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Application.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'recruiter') {
	header("Location: login.php");
	exit();
}

$application = new Application();
$jobs = $application->getAllJobsForReview();

// Pre-load counts for the first job (if any) so the chart isn't empty on
// first load; the JS re-fetches via AJAX whenever the dropdown changes.
$initial_job_id = count($jobs) > 0 ? $jobs[0]['id'] : 0;
$initial_counts = $initial_job_id ? $application->getStatusCountsForJob($initial_job_id) : ['submitted' => 0, 'reviewed' => 0, 'shortlisted' => 0, 'rejected' => 0];
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Application Funnel Analytics - CareerBridge</title>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		body {
			font-family: Arial, sans-serif;
			background: #f4f7f6;
			margin: 0;
			padding: 0;
		}
		.container {
			max-width: 900px;
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
		.job-picker {
			background: #e8eaf6;
			padding: 20px;
			border-radius: 5px;
			margin-bottom: 25px;
		}
		.job-picker label {
			font-weight: bold;
			margin-right: 10px;
		}
		.job-picker select {
			padding: 8px 12px;
			border-radius: 4px;
			border: 1px solid #ccc;
			min-width: 300px;
			font-size: 14px;
		}
		.chart-wrap {
			position: relative;
			height: 320px;
			margin-top: 20px;
		}
		.no-jobs {
			text-align: center;
			color: #999;
			padding: 30px;
		}
	</style>
</head>
<body>
	<?php $nav_base = ''; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Application Funnel Analytics</h1>

		<?php if (count($jobs) > 0) { ?>
			<div class="job-picker">
				<label for="job_id">Select a job:</label>
				<select id="job_id" onchange="loadStats(this.value)">
					<?php foreach ($jobs as $job_item) { ?>
						<option value="<?php echo $job_item['id']; ?>" <?php echo ($job_item['id'] == $initial_job_id) ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($job_item['title']) . ' — ' . htmlspecialchars($job_item['company_name']); ?>
						</option>
					<?php } ?>
				</select>
			</div>

			<div class="chart-wrap">
				<canvas id="funnelChart"></canvas>
			</div>
		<?php } else { ?>
			<p class="no-jobs">No jobs have been posted yet, so there's nothing to chart.</p>
		<?php } ?>
	</div>

	<?php if (count($jobs) > 0) { ?>
	<script>
		const statusLabels = ['Submitted', 'Reviewed', 'Shortlisted', 'Rejected'];
		const statusKeys = ['submitted', 'reviewed', 'shortlisted', 'rejected'];
		const statusColors = ['#1976d2', '#f57c00', '#388e3c', '#d32f2f'];

		const ctx = document.getElementById('funnelChart').getContext('2d');
		const funnelChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: statusLabels,
				datasets: [{
					label: 'Applications',
					data: <?php echo json_encode(array_values($initial_counts)); ?>,
					backgroundColor: statusColors
				}]
			},
			options: {
				indexAxis: 'y',
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					title: { display: false }
				},
				scales: {
					x: {
						beginAtZero: true,
						ticks: { precision: 0 }
					}
				}
			}
		});

		function loadStats(jobId) {
			// AJAX re-fetch: chart re-renders whenever a different job is chosen.
			fetch('../controller/get-application-stats.php?job_id=' + jobId)
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						const newData = statusKeys.map(key => data.counts[key] || 0);
						funnelChart.data.datasets[0].data = newData;
						funnelChart.update();
					} else {
						console.error(data.error);
					}
				})
				.catch(error => console.error('Error:', error));
		}
	</script>
	<?php } ?>
</body>
</html>
