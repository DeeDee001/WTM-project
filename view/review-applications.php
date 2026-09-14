<?php
// This page is opened directly (view/review-applications.php), not only
// through index.php, so make sure the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Application.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'recruiter') {
	header("Location: login.php");
	exit();
}

$application = new Application();

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
$jobs = $application->getAllJobsForReview();

$selected_job = null;
$applications = [];

if ($job_id > 0) {
	$selected_job = $application->getJobTitle($job_id);
	if ($selected_job) {
		$applications = $application->getApplicationsForJob($job_id);
	}
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Review Applications - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/review-applications.css' : 'css/review-applications.css'); ?>">
</head>
<body>
	<?php $nav_base = ''; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Review Applications</h1>

		<div class="job-picker">
			<form method="GET" action="review-applications.php">
				<label for="job_id">Select a job:</label>
				<select name="job_id" id="job_id" onchange="this.form.submit()">
					<option value="">-- Choose a job --</option>
					<?php foreach ($jobs as $job_item) { ?>
						<option value="<?php echo $job_item['id']; ?>" <?php echo ($job_item['id'] == $job_id) ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($job_item['title']) . ' — ' . htmlspecialchars($job_item['company_name']); ?>
						</option>
					<?php } ?>
				</select>
			</form>
		</div>

		<?php if ($job_id > 0 && !$selected_job) { ?>
			<p style="text-align: center; color: #d32f2f; padding: 30px;">That job could not be found.</p>
		<?php } elseif ($selected_job) { ?>
			<h3>Applications for: <?php echo htmlspecialchars($selected_job['title']); ?> (<?php echo htmlspecialchars($selected_job['company_name']); ?>)</h3>

			<?php if (count($applications) > 0) { ?>
				<table>
					<thead>
						<tr>
							<th>Seeker</th>
							<th>Cover Letter</th>
							<th>Resume</th>
							<th>Applied</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($applications as $app) { ?>
							<tr id="app-row-<?php echo $app['id']; ?>">
								<td>
									<strong><?php echo htmlspecialchars($app['username']); ?></strong>
									<?php if (!empty($app['headline'])) { ?>
										<div class="headline"><?php echo htmlspecialchars($app['headline']); ?></div>
									<?php } ?>
								</td>
								<td>
									<div class="cover-letter"><?php echo htmlspecialchars($app['cover_letter']); ?></div>
								</td>
								<td>
									<?php if (!empty($app['resume_path'])) { ?>
										<a class="resume-link" href="../<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank">Download</a>
									<?php } else { ?>
										<span style="color: #999;">No resume</span>
									<?php } ?>
								</td>
								<td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
								<td>
									<span class="status-badge status-<?php echo $app['status']; ?>" id="status-badge-<?php echo $app['id']; ?>">
										<?php echo ucfirst($app['status']); ?>
									</span>
									<select class="status-select" id="status-select-<?php echo $app['id']; ?>"
											onchange="updateStatus(<?php echo $app['id']; ?>, this.value)">
										<?php foreach (['submitted', 'reviewed', 'shortlisted', 'rejected'] as $status_option) { ?>
											<option value="<?php echo $status_option; ?>" <?php echo ($status_option == $app['status']) ? 'selected' : ''; ?>>
												<?php echo ucfirst($status_option); ?>
											</option>
										<?php } ?>
									</select>
									<div class="save-note" id="save-note-<?php echo $app['id']; ?>">Saved!</div>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } else { ?>
				<p style="text-align: center; color: #999; padding: 30px;">No applications for this job yet.</p>
			<?php } ?>
		<?php } else { ?>
			<p style="text-align: center; color: #999; padding: 30px;">Choose a job above to see its applications.</p>
		<?php } ?>
	</div>

	<script>
		function updateStatus(applicationId, newStatus) {
			// AJAX PUT request — updates the row's badge in place, no reload.
			fetch('../controller/update-application-status.php', {
				method: 'PUT',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'application_id=' + applicationId + '&status=' + newStatus
			})
			.then(response => response.json())
			.then(data => {
				const badge = document.getElementById('status-badge-' + applicationId);
				const note = document.getElementById('save-note-' + applicationId);
				if (data.success) {
					badge.className = 'status-badge status-' + newStatus;
					badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
					note.style.display = 'block';
					setTimeout(() => { note.style.display = 'none'; }, 1500);
				} else {
					alert('Failed to update status: ' + (data.error || 'Unknown error'));
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Error updating status');
			});
		}
	</script>
</body>
</html>
