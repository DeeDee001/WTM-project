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
		.jobs-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
			gap: 20px;
			margin-top: 20px;
		}
		.job-card {
			background: white;
			border-radius: 8px;
			padding: 20px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			position: relative;
			transition: transform 0.2s;
		}
		.job-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 4px 20px rgba(0,0,0,0.15);
		}
		.bookmark-btn {
			position: absolute;
			top: 15px;
			right: 15px;
			background: none;
			border: none;
			font-size: 24px;
			cursor: pointer;
			transition: transform 0.2s;
			color: #e91e63;
		}
		.bookmark-btn:hover {
			transform: scale(1.2);
		}
		.company-logo {
			width: 50px;
			height: 50px;
			border-radius: 50%;
			object-fit: cover;
			margin-bottom: 10px;
		}
		.job-title {
			font-size: 20px;
			font-weight: bold;
			color: #333;
			margin: 10px 0;
		}
		.company-name {
			color: #667eea;
			font-size: 14px;
			margin-bottom: 10px;
		}
		.job-meta {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin: 15px 0;
		}
		.meta-tag {
			background: #e8eaf6;
			padding: 5px 10px;
			border-radius: 3px;
			font-size: 12px;
			color: #555;
		}
		.job-description {
			color: #666;
			font-size: 14px;
			line-height: 1.5;
			margin: 10px 0;
			display: -webkit-box;
			-webkit-line-clamp: 3;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
		.btn-apply {
			display: inline-block;
			padding: 8px 20px;
			background: #667eea;
			color: white;
			text-decoration: none;
			border-radius: 4px;
			margin-top: 10px;
			margin-right: 10px;
		}
		.btn-apply:hover {
			background: #5568d3;
		}
		.btn-view {
			display: inline-block;
			padding: 8px 20px;
			background: #4caf50;
			color: white;
			text-decoration: none;
			border-radius: 4px;
			margin-top: 10px;
		}
		.btn-view:hover {
			background: #45a049;
		}
		.saved-date {
			font-size: 12px;
			color: #999;
			margin-top: 10px;
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
		.status-badge {
			display: inline-block;
			padding: 5px 10px;
			border-radius: 3px;
			font-size: 12px;
			font-weight: bold;
		}
		.status-active {
			background: #4caf50;
			color: white;
		}
		.status-closed {
			background: #f44336;
			color: white;
		}
	</style>
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
