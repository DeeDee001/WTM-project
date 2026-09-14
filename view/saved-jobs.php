<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	header("Location: ../view/login.php");
	exit();
}

$jobSeeker = new JobSeeker();
$savedJobs = $jobSeeker->getSavedJobs($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Saved Jobs - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/saved-jobs.css' : 'css/saved-jobs.css'); ?>">
</head>
<body>
	<?php $nav_base = ''; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Saved Jobs</h1>

		<div class="jobs-grid">
			<?php if (count($savedJobs) > 0) {
				foreach ($savedJobs as $job) {
					$hasApplied = $jobSeeker->hasApplied($job['id'], $_SESSION['user_id']);
			?>
				<div class="job-card" id="job-card-<?php echo $job['id']; ?>">
					<button class="bookmark-btn"
							data-job-id="<?php echo $job['id']; ?>"
							onclick="removeBookmark(<?php echo $job['id']; ?>)"
							title="Remove from saved jobs">
						♥
					</button>

					<?php if ($job['company_logo']) { ?>
						<img src="../<?php echo htmlspecialchars($job['company_logo']); ?>" alt="Company Logo" class="company-logo">
					<?php } ?>

					<div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
					<div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>

					<span class="status-badge status-<?php echo $job['status']; ?>">
						<?php echo ucfirst($job['status']); ?>
					</span>

					<div class="job-meta">
						<span class="meta-tag">📁 <?php echo htmlspecialchars($job['category']); ?></span>
						<span class="meta-tag">📍 <?php echo htmlspecialchars($job['location']); ?></span>
						<span class="meta-tag">💼 <?php echo htmlspecialchars($job['job_type']); ?></span>
						<span class="meta-tag">💰 <?php echo htmlspecialchars($job['salary_range']); ?></span>
					</div>

					<div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>

					<?php if ($job['status'] == 'active') { ?>
						<?php if ($hasApplied) { ?>
							<span style="color: #4caf50; font-weight: bold;">✓ Already Applied</span>
						<?php } else { ?>
							<a href="apply-job.php?id=<?php echo $job['id']; ?>" class="btn-apply">Apply Now</a>
						<?php } ?>
					<?php } else { ?>
						<span style="color: #f44336;">This position is no longer available</span>
					<?php } ?>

					<a href="view-job.php?id=<?php echo $job['id']; ?>" class="btn-view">View Details</a>

					<div class="saved-date">Saved on <?php echo date('M d, Y', strtotime($job['saved_at'])); ?></div>
				</div>
			<?php }
			} else { ?>
				<div class="no-results">
					<p>You haven't saved any jobs yet.</p>
					<p><a href="search-jobs.php" style="color: #667eea;">Start searching for jobs</a></p>
				</div>
			<?php } ?>
		</div>
	</div>

	<script>
		function removeBookmark(jobId) {
			if (!confirm('Remove this job from your saved list?')) {
				return;
			}

			fetch('../controller/toggle-bookmark.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'job_id=' + jobId + '&action=unsave'
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Remove the card from the DOM
					const card = document.getElementById('job-card-' + jobId);
					card.style.transition = 'opacity 0.3s';
					card.style.opacity = '0';
					setTimeout(() => {
						card.remove();

						// Check if there are any jobs left
						const jobsGrid = document.querySelector('.jobs-grid');
						if (jobsGrid.children.length === 0) {
							jobsGrid.innerHTML = `
								<div class="no-results">
									<p>You haven't saved any jobs yet.</p>
									<p><a href="search-jobs.php" style="color: #667eea;">Start searching for jobs</a></p>
								</div>
							`;
						}
					}, 300);
				} else {
					alert('Failed to remove bookmark');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Error removing bookmark');
			});
		}
	</script>
</body>
</html>
