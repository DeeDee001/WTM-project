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
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/search-jobs.css' : 'css/search-jobs.css'); ?>">
</head>

<body>
	<?php $nav_base = '';
	include __DIR__ . '/layout/header.php'; ?>

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
								<option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?>
								</option>
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
								<option value="<?php echo htmlspecialchars($type); ?>">
									<?php echo htmlspecialchars($type); ?></option>
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
							data-job-id="<?php echo $job['id']; ?>" onclick="toggleBookmark(<?php echo $job['id']; ?>, this)">
							♥
						</button>

						<?php if ($job['company_logo']) { ?>
							<img src="../<?php echo htmlspecialchars($job['company_logo']); ?>" alt="Company Logo"
								class="company-logo">
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
		document.getElementById('search-form').addEventListener('submit', function (e) {
			e.preventDefault();
			performSearch();
		});

		// Also search on input change (debounced)
		let searchTimeout;
		document.querySelectorAll('#search-form input, #search-form select').forEach(input => {
			input.addEventListener('input', function () {
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