<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Job.php';
require_once __DIR__ . '/../model/JobSeeker.php';

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

$job_id = intval($_GET['id']);
$job = new Job();
$job_detail = $job->getJobDetails($job_id);

if (!$job_detail) {
	echo "Job not found.";
	exit();
}

// Check if job seeker has saved or applied to this job
$isSaved = false;
$hasApplied = false;
if ($_SESSION['role'] == 'seeker') {
	$jobSeeker = new JobSeeker();
	$isSaved = $jobSeeker->isJobSaved($_SESSION['user_id'], $job_id);
	$hasApplied = $jobSeeker->hasApplied($job_id, $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title><?php echo htmlspecialchars($job_detail['title']); ?> - CareerBridge</title>
	<style>
		body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
		.container { max-width: 900px; margin: 30px auto; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 30px; }
		h1 { margin: 0 0 10px 0; color: #333; }
		.meta-info { color: #666; font-size: 14px; }
		.content-section { margin-bottom: 25px; }
		.content-section h3 { border-bottom: 1px solid #ddd; padding-bottom: 5px; color: #444; }
		.company-card { background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 6px; padding: 20px; display: flex; gap: 20px; margin-top: 30px; }
		.company-logo { width: 80px; height: 80px; border-radius: 6px; object-fit: cover; background: #ccc; }
		.nav-back a { color: #667eea; text-decoration: none; font-weight: bold; }
		.btn { padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; margin-right: 10px; }
		.btn-primary { background: #667eea; color: white; }
		.btn-primary:hover { background: #5568d3; }
		.btn-secondary { background: #f5f5f5; color: #333; border: 1px solid #ddd; }
		.btn-secondary:hover { background: #e0e0e0; }
		.btn-success { background: #4caf50; color: white; }
		.bookmark-btn { background: none; border: none; font-size: 28px; cursor: pointer; transition: transform 0.2s; }
		.bookmark-btn:hover { transform: scale(1.2); }
		.bookmark-btn.saved { color: #e91e63; }
		.bookmark-btn.unsaved { color: #ddd; }
		.action-bar { display: flex; align-items: center; gap: 15px; margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 6px; }
		.status-badge { padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px; }
		.status-active { background: #e8f5e9; color: #2e7d32; }
		.status-closed { background: #ffebee; color: #c62828; }
		.alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
		.alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #4caf50; }
		.alert-error { background: #ffebee; color: #c62828; border: 1px solid #f44336; }
	</style>
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back" style="margin-bottom: 20px;">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<?php if (isset($_SESSION['success'])) { ?>
			<div class="alert alert-success">
				<?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
			</div>
		<?php } ?>
		<?php if (isset($_SESSION['error'])) { ?>
			<div class="alert alert-error">
				<?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
			</div>
		<?php } ?>

		<div style="display: flex; justify-content: space-between; align-items: start;">
			<h1 style="margin: 0;"><?php echo htmlspecialchars($job_detail['title']); ?></h1>
			<?php if ($_SESSION['role'] == 'seeker') { ?>
				<button class="bookmark-btn <?php echo $isSaved ? 'saved' : 'unsaved'; ?>"
						id="bookmark-btn"
						onclick="toggleBookmark(<?php echo $job_id; ?>)"
						title="<?php echo $isSaved ? 'Remove from saved jobs' : 'Save this job'; ?>">
					♥
				</button>
			<?php } ?>
		</div>
		<div class="meta-info" style="margin-top: 10px;">
			<span class="status-badge status-<?php echo $job_detail['status']; ?>">
				<?php echo ucfirst($job_detail['status']); ?>
			</span>
			<span style="margin-left: 10px;">
				📁 <?php echo htmlspecialchars($job_detail['category']); ?> |
				📍 <?php echo htmlspecialchars($job_detail['location']); ?> |
				💼 <?php echo htmlspecialchars($job_detail['job_type']); ?>
			</span>
		</div>

		<?php if ($_SESSION['role'] == 'seeker' && $job_detail['status'] == 'active') { ?>
			<div class="action-bar">
				<?php if ($hasApplied) { ?>
					<span class="btn btn-success" style="cursor: default;">✓ Already Applied</span>
					<a href="my-applications.php" class="btn btn-secondary">View My Applications</a>
				<?php } else { ?>
					<a href="apply-job.php?id=<?php echo $job_id; ?>" class="btn btn-primary">Apply for this Job</a>
					<span style="color: #666;">Don't miss this opportunity!</span>
				<?php } ?>
			</div>
		<?php } elseif ($_SESSION['role'] == 'seeker' && $job_detail['status'] == 'closed') { ?>
			<div class="action-bar">
				<span style="color: #c62828; font-weight: bold;">This position is no longer accepting applications</span>
			</div>
		<?php } ?>

		<div class="content-section" style="margin-top: 25px;">
			<h3>Job Description</h3>
			<p><?php echo nl2br(htmlspecialchars($job_detail['description'])); ?></p>
		</div>

		<div class="content-section">
			<h3>Requirements</h3>
			<p><?php echo nl2br(htmlspecialchars($job_detail['requirements'])); ?></p>
		</div>

		<div class="content-section" style="background: #fff8e1; padding: 15px; border-radius: 4px;">
			<p>💰 Salary: <strong><?php echo htmlspecialchars($job_detail['salary_range']); ?></strong></p>
			<p>📅 Deadline: <strong><?php echo $job_detail['deadline']; ?></strong></p>
		</div>

		<div class="company-card">
			<?php $logo_src = !empty($job_detail['company_logo']) ? '../'.$job_detail['company_logo'] : 'https://via.placeholder.com/80?text=Logo'; ?>
			<img class="company-logo" src="<?php echo $logo_src; ?>" alt="Logo">
			<div>
				<h4 style="margin:0 0 5px 0"><?php echo htmlspecialchars($job_detail['company_name'] ?? 'Company'); ?></h4>
				<p style="margin:0 0 5px 0; color:#666; font-size:14px">Industry: <?php echo htmlspecialchars($job_detail['company_industry'] ?? 'N/A'); ?></p>
				<p style="margin:0 0 5px 0; color:#666; font-size:14px"><?php echo htmlspecialchars($job_detail['company_description'] ?? ''); ?></p>
				<?php if(!empty($job_detail['company_website'])) { ?>
					<p style="margin:0; font-size:14px">🌐 <a href="http://<?php echo htmlspecialchars($job_detail['company_website']); ?>" target="_blank"><?php echo htmlspecialchars($job_detail['company_website']); ?></a></p>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php if ($_SESSION['role'] == 'seeker') { ?>
	<script>
		function toggleBookmark(jobId) {
			const button = document.getElementById('bookmark-btn');
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
						button.title = 'Remove from saved jobs';
					} else {
						button.classList.remove('saved');
						button.classList.add('unsaved');
						button.title = 'Save this job';
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
	</script>
	<?php } ?>
</body>
</html>
