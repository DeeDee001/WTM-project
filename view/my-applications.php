<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	header("Location: ../view/login.php");
	exit();
}

$jobSeeker = new JobSeeker();
$applications = $jobSeeker->getSeekerApplications($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>My Applications - CareerBridge</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background: #f4f7f6;
			margin: 0;
			padding: 0;
		}
		.container {
			max-width: 1200px;
			margin: 30px auto;
			padding: 0 20px;
		}
		h1 {
			color: #333;
			border-bottom: 2px solid #667eea;
			padding-bottom: 10px;
		}
		.alert {
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 20px;
		}
		.alert-success {
			background: #e8f5e9;
			color: #2e7d32;
			border: 1px solid #4caf50;
		}
		.applications-list {
			margin-top: 20px;
		}
		.application-card {
			background: white;
			border-radius: 8px;
			padding: 20px;
			margin-bottom: 15px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			display: flex;
			justify-content: space-between;
			align-items: center;
			transition: transform 0.2s;
		}
		.application-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(0,0,0,0.15);
		}
		.app-main {
			flex: 1;
		}
		.job-title {
			font-size: 20px;
			font-weight: bold;
			color: #333;
			margin-bottom: 8px;
		}
		.company-name {
			color: #667eea;
			font-size: 14px;
			margin-bottom: 10px;
		}
		.app-meta {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin-top: 10px;
		}
		.meta-tag {
			background: #e8eaf6;
			padding: 5px 10px;
			border-radius: 3px;
			font-size: 12px;
			color: #555;
		}
		.app-status {
			text-align: right;
			padding-left: 20px;
		}
		.status-badge {
			display: inline-block;
			padding: 8px 16px;
			border-radius: 20px;
			font-size: 14px;
			font-weight: bold;
			margin-bottom: 10px;
		}
		.status-submitted {
			background: #e3f2fd;
			color: #1976d2;
		}
		.status-reviewed {
			background: #fff3e0;
			color: #f57c00;
		}
		.status-shortlisted {
			background: #e8f5e9;
			color: #388e3c;
		}
		.status-rejected {
			background: #ffebee;
			color: #d32f2f;
		}
		.applied-date {
			font-size: 12px;
			color: #999;
		}
		.btn-view {
			display: inline-block;
			padding: 6px 15px;
			background: #667eea;
			color: white;
			text-decoration: none;
			border-radius: 4px;
			font-size: 14px;
			margin-top: 5px;
		}
		.btn-view:hover {
			background: #5568d3;
		}
		.no-results {
			text-align: center;
			padding: 50px;
			color: #999;
			font-size: 18px;
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		.stats {
			display: flex;
			gap: 20px;
			margin-bottom: 25px;
			flex-wrap: wrap;
		}
		.stat-card {
			background: white;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			flex: 1;
			min-width: 200px;
		}
		.stat-number {
			font-size: 32px;
			font-weight: bold;
			color: #667eea;
		}
		.stat-label {
			color: #666;
			font-size: 14px;
			margin-top: 5px;
		}
		.company-logo {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			object-fit: cover;
			margin-right: 15px;
			float: left;
		}
	</style>
</head>
<body>
	<?php $nav_base = ''; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>My Applications</h1>

		<?php if (isset($_SESSION['success'])) { ?>
			<div class="alert alert-success">
				<?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
			</div>
		<?php } ?>

		<?php if (count($applications) > 0) {
			// Calculate stats
			$total = count($applications);
			$submitted = count(array_filter($applications, fn($a) => $a['status'] == 'submitted'));
			$reviewed = count(array_filter($applications, fn($a) => $a['status'] == 'reviewed'));
			$shortlisted = count(array_filter($applications, fn($a) => $a['status'] == 'shortlisted'));
			$rejected = count(array_filter($applications, fn($a) => $a['status'] == 'rejected'));
		?>

		<div class="stats">
			<div class="stat-card">
				<div class="stat-number"><?php echo $total; ?></div>
				<div class="stat-label">Total Applications</div>
			</div>
			<div class="stat-card">
				<div class="stat-number"><?php echo $submitted; ?></div>
				<div class="stat-label">Submitted</div>
			</div>
			<div class="stat-card">
				<div class="stat-number"><?php echo $reviewed; ?></div>
				<div class="stat-label">Reviewed</div>
			</div>
			<div class="stat-card">
				<div class="stat-number"><?php echo $shortlisted; ?></div>
				<div class="stat-label">Shortlisted</div>
			</div>
			<div class="stat-card">
				<div class="stat-number"><?php echo $rejected; ?></div>
				<div class="stat-label">Rejected</div>
			</div>
		</div>

		<div class="applications-list">
			<?php foreach ($applications as $app) { ?>
				<div class="application-card">
					<div class="app-main">
						<?php if ($app['company_logo']) { ?>
							<img src="../<?php echo htmlspecialchars($app['company_logo']); ?>" alt="Company Logo" class="company-logo">
						<?php } ?>

						<div class="job-title"><?php echo htmlspecialchars($app['job_title']); ?></div>
						<div class="company-name"><?php echo htmlspecialchars($app['company_name']); ?></div>

						<div class="app-meta">
							<span class="meta-tag">📁 <?php echo htmlspecialchars($app['category']); ?></span>
							<span class="meta-tag">📍 <?php echo htmlspecialchars($app['location']); ?></span>
							<span class="meta-tag">💼 <?php echo htmlspecialchars($app['job_type']); ?></span>
						</div>

						<div style="margin-top: 10px; font-size: 14px; color: #666;">
							<strong>Cover Letter:</strong> <?php echo substr(htmlspecialchars($app['cover_letter']), 0, 100) . '...'; ?>
						</div>
					</div>

					<div class="app-status">
						<span class="status-badge status-<?php echo $app['status']; ?>">
							<?php echo ucfirst($app['status']); ?>
						</span>
						<div class="applied-date">
							Applied on<br>
							<?php echo date('M d, Y', strtotime($app['created_at'])); ?>
						</div>
						<?php
						$updatedAt = $app['updated_at'] ?? null;
						if (!empty($updatedAt) && $updatedAt != ($app['created_at'] ?? null)) {
						?>
							<div class="applied-date" style="margin-top: 5px;">
								Updated on<br>
								<?php echo date('M d, Y', strtotime($updatedAt)); ?>
							</div>
						<?php } ?>
						<a href="view-job.php?id=<?php echo $app['job_id']; ?>" class="btn-view">View Job</a>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php } else { ?>
			<div class="no-results">
				<p>You haven't applied to any jobs yet.</p>
				<p><a href="search-jobs.php" style="color: #667eea;">Start searching for jobs</a></p>
			</div>
		<?php } ?>
	</div>
</body>
</html>
