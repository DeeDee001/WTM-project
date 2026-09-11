<?php
session_start();
require_once __DIR__ . '/../model/JobSeeker.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	header("Location: ../view/login.php");
	exit();
}

$jobSeeker = new JobSeeker();
$categories = $jobSeeker->getCategories();
$jobTypes = $jobSeeker->getJobTypes();
$jobs = $jobSeeker->searchJobs(); // Get all active jobs initially
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Search Jobs - CareerBridge</title>
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
		.filter-section {
			background: white;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			margin-bottom: 25px;
		}
		.filter-row {
			display: flex;
			gap: 15px;
			flex-wrap: wrap;
			align-items: center;
		}
		.filter-item {
			flex: 1;
			min-width: 200px;
		}
		.filter-item label {
			display: block;
			margin-bottom: 5px;
			font-weight: bold;
			color: #555;
		}
		.filter-item input, .filter-item select {
			width: 100%;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			box-sizing: border-box;
		}
		.btn-search {
			padding: 10px 30px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			margin-top: 26px;
		}
		.btn-search:hover {
			background: #5568d3;
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
		}
		.bookmark-btn:hover {
			transform: scale(1.2);
		}
		.bookmark-btn.saved {
			color: #e91e63;
		}
		.bookmark-btn.unsaved {
			color: #ddd;
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
		}
		.btn-apply:hover {
			background: #5568d3;
		}
		.no-results {
			text-align: center;
			padding: 50px;
			color: #999;
			font-size: 18px;
		}
		.loading {
			text-align: center;
			padding: 30px;
			color: #667eea;
			display: none;
		}
	</style>
</head>
<body>
	<?php $nav_base = ''; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Search Jobs</h1>

		<div class="filter-section">
			<form id="search-form">
				<div class="filter-row">
					<div class="filter-item">
						<label for="keyword">Keyword</label>
						<input type="text" id="keyword" name="keyword" placeholder="Job title, description...">
					</div>
					<div class="filter-item">
						<label for="category">Category</label>
						<select id="category" name="category">
							<option value="">All Categories</option>
							<?php foreach ($categories as $cat) { ?>
								<option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="filter-item">
						<label for="location">Location</label>
						<input type="text" id="location" name="location" placeholder="City, State...">
					</div>
				</div>
				<div class="filter-row" style="margin-top: 15px;">
					<div class="filter-item">
						<label for="job_type">Job Type</label>
						<select id="job_type" name="job_type">
							<option value="">All Types</option>
							<?php foreach ($jobTypes as $type) { ?>
								<option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="filter-item">
						<label for="salary_min">Min Salary</label>
						<input type="number" id="salary_min" name="salary_min" placeholder="e.g., 50000">
					</div>
					<div class="filter-item">
						<label for="salary_max">Max Salary</label>
						<input type="number" id="salary_max" name="salary_max" placeholder="e.g., 100000">
					</div>
					<button type="submit" class="btn-search">Search</button>
				</div>
			</form>
		</div>

		<div class="loading" id="loading">Searching jobs...</div>
		<div class="jobs-grid" id="jobs-container">
			<?php if (count($jobs) > 0) {
				foreach ($jobs as $job) {
					$isSaved = $jobSeeker->isJobSaved($_SESSION['user_id'], $job['id']);
					$hasApplied = $jobSeeker->hasApplied($job['id'], $_SESSION['user_id']);
			?>
				<div class="job-card">
					<button class="bookmark-btn <?php echo $isSaved ? 'saved' : 'unsaved'; ?>"
							data-job-id="<?php echo $job['id']; ?>"
							onclick="toggleBookmark(<?php echo $job['id']; ?>, this)">
						♥
					</button>

					<?php if ($job['company_logo']) { ?>
						<img src="../<?php echo htmlspecialchars($job['company_logo']); ?>" alt="Company Logo" class="company-logo">
					<?php } ?>

					<div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
					<div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>

					<div class="job-meta">
						<span class="meta-tag">📁 <?php echo htmlspecialchars($job['category']); ?></span>
						<span class="meta-tag">📍 <?php echo htmlspecialchars($job['location']); ?></span>
						<span class="meta-tag">💼 <?php echo htmlspecialchars($job['job_type']); ?></span>
						<span class="meta-tag">💰 <?php echo htmlspecialchars($job['salary_range']); ?></span>
					</div>

					<div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>

					<?php if ($hasApplied) { ?>
						<span style="color: #4caf50; font-weight: bold;">✓ Already Applied</span>
					<?php } else { ?>
						<a href="apply-job.php?id=<?php echo $job['id']; ?>" class="btn-apply">Apply Now</a>
					<?php } ?>
				</div>
			<?php }
			} else { ?>
				<div class="no-results">No jobs found. Try adjusting your filters.</div>
			<?php } ?>
		</div>
	</div>

	<script>
		// Live AJAX search
		document.getElementById('search-form').addEventListener('submit', function(e) {
			e.preventDefault();
			performSearch();
		});

		// Also search on input change (debounced)
		let searchTimeout;
		document.querySelectorAll('#search-form input, #search-form select').forEach(input => {
			input.addEventListener('input', function() {
				clearTimeout(searchTimeout);
				searchTimeout = setTimeout(performSearch, 500);
			});
		});

		function performSearch() {
			const formData = new FormData(document.getElementById('search-form'));
			const params = new URLSearchParams(formData).toString();

			document.getElementById('loading').style.display = 'block';
			document.getElementById('jobs-container').style.opacity = '0.5';

			fetch('../controller/search-jobs-ajax.php?' + params)
				.then(response => response.json())
				.then(data => {
					document.getElementById('loading').style.display = 'none';
					document.getElementById('jobs-container').style.opacity = '1';

					if (data.success) {
						renderJobs(data.jobs);
					} else {
						alert('Error loading jobs');
					}
				})
				.catch(error => {
					console.error('Error:', error);
					document.getElementById('loading').style.display = 'none';
					document.getElementById('jobs-container').style.opacity = '1';
				});
		}

		function renderJobs(jobs) {
			const container = document.getElementById('jobs-container');

			if (jobs.length === 0) {
				container.innerHTML = '<div class="no-results">No jobs found. Try adjusting your filters.</div>';
				return;
			}

			container.innerHTML = jobs.map(job => `
				<div class="job-card">
					<button class="bookmark-btn ${job.is_saved ? 'saved' : 'unsaved'}"
							data-job-id="${job.id}"
							onclick="toggleBookmark(${job.id}, this)">
						♥
					</button>

					${job.company_logo ? `<img src="../${job.company_logo}" alt="Company Logo" class="company-logo">` : ''}

					<div class="job-title">${escapeHtml(job.title)}</div>
					<div class="company-name">${escapeHtml(job.company_name)}</div>

					<div class="job-meta">
						<span class="meta-tag">📁 ${escapeHtml(job.category)}</span>
						<span class="meta-tag">📍 ${escapeHtml(job.location)}</span>
						<span class="meta-tag">💼 ${escapeHtml(job.job_type)}</span>
						<span class="meta-tag">💰 ${escapeHtml(job.salary_range)}</span>
					</div>

					<div class="job-description">${escapeHtml(job.description)}</div>

					${job.has_applied ?
						'<span style="color: #4caf50; font-weight: bold;">✓ Already Applied</span>' :
						`<a href="apply-job.php?id=${job.id}" class="btn-apply">Apply Now</a>`
					}
				</div>
			`).join('');
		}

		function toggleBookmark(jobId, button) {
			const isSaved = button.classList.contains('saved');
			const action = isSaved ? 'unsave' : 'save';

			fetch('../controller/toggle-bookmark.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'job_id=' + jobId + '&action=' + action
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					if (action === 'save') {
						button.classList.remove('unsaved');
						button.classList.add('saved');
					} else {
						button.classList.remove('saved');
						button.classList.add('unsaved');
					}
				} else {
					alert('Failed to update bookmark');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Error updating bookmark');
			});
		}

		function escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	</script>
</body>
</html>
