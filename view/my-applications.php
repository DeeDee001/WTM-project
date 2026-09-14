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
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/my-applications.css' : 'css/my-applications.css'); ?>">
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
