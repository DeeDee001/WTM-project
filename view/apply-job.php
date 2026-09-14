<?php
session_start();
require_once __DIR__ . '/../model/Job.php';
require_once __DIR__ . '/../model/JobSeeker.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	header("Location: ../view/login.php");
	exit();
}

$job_id = $_GET['id'] ?? null;

if (!$job_id) {
	header("Location: search-jobs.php");
	exit();
}

$jobModel = new Job();
$jobSeeker = new JobSeeker();

$job = $jobModel->getJobDetails($job_id);

if (!$job || $job['status'] != 'active') {
	$_SESSION['error'] = 'This job is not available for applications.';
	header("Location: search-jobs.php");
	exit();
}

// Check if already applied
if ($jobSeeker->hasApplied($job_id, $_SESSION['user_id'])) {
	$_SESSION['error'] = 'You have already applied for this job.';
	header("Location: view-job.php?id=" . $job_id);
	exit();
}

require_once __DIR__ . '/../model/User.php';
$userModel = new User();
$user = $userModel->getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Apply for Job - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/apply-job.css' : 'css/apply-job.css'); ?>">
</head>
<body>
	<?php $nav_base = ''; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Apply for Job</h1>

		<div class="job-info">
			<div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
			<div class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></div>
			<div style="margin-top: 10px; color: #666;">
				<span>📁 <?php echo htmlspecialchars($job['category']); ?></span> |
				<span>📍 <?php echo htmlspecialchars($job['location']); ?></span> |
				<span>💼 <?php echo htmlspecialchars($job['job_type']); ?></span>
			</div>
		</div>

		<?php if (isset($_SESSION['error'])) { ?>
			<div class="alert alert-error">
				<?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
			</div>
		<?php } ?>

		<?php if (!$user['headline']) { ?>
			<div class="alert alert-info">
				<strong>Tip:</strong> Complete your profile with a headline to make a better impression on employers.
				<a href="profile.php" style="color: #1565c0; text-decoration: underline;">Update Profile</a>
			</div>
		<?php } ?>

		<form action="../controller/submit-application.php" method="POST" enctype="multipart/form-data" id="application-form">
			<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

			<div class="form-group">
				<label for="cover_letter">Cover Letter *</label>
				<textarea id="cover_letter" name="cover_letter" required placeholder="Explain why you're a great fit for this position..."></textarea>
				<div class="note">Highlight your relevant skills and experience</div>
			</div>

			<div class="form-group">
				<label>Resume *</label>
				<div class="resume-options">
					<?php if ($user['resume_path']) { ?>
						<label>
							<input type="radio" name="resume_option" value="profile" checked>
							Use my profile resume
							<div class="current-resume">
								📄 <?php echo basename($user['resume_path']); ?>
							</div>
						</label>
						<label>
							<input type="radio" name="resume_option" value="upload">
							Upload a different resume for this application
						</label>
					<?php } else { ?>
						<label>
							<input type="radio" name="resume_option" value="upload" checked>
							Upload resume (PDF only)
						</label>
					<?php } ?>
				</div>

				<input type="file" id="resume_file" name="resume_file" accept=".pdf"
					   style="display: <?php echo $user['resume_path'] ? 'none' : 'block'; ?>;">
				<div class="note">PDF format only, max 5MB</div>
			</div>

			<div style="margin-top: 30px;">
				<button type="submit" class="btn">Submit Application</button>
				<a href="view-job.php?id=<?php echo $job_id; ?>" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">Cancel</a>
			</div>
		</form>
	</div>

	<script>
		// Show/hide file upload based on radio selection
		document.querySelectorAll('input[name="resume_option"]').forEach(radio => {
			radio.addEventListener('change', function() {
				const fileInput = document.getElementById('resume_file');
				if (this.value === 'upload') {
					fileInput.style.display = 'block';
					fileInput.required = true;
				} else {
					fileInput.style.display = 'none';
					fileInput.required = false;
				}
			});
		});

		// Form validation
		document.getElementById('application-form').addEventListener('submit', function(e) {
			const resumeOption = document.querySelector('input[name="resume_option"]:checked').value;
			const fileInput = document.getElementById('resume_file');

			if (resumeOption === 'upload' && !fileInput.files[0]) {
				e.preventDefault();
				alert('Please select a resume file to upload.');
				return false;
			}

			if (fileInput.files[0]) {
				const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
				if (fileSize > 5) {
					e.preventDefault();
					alert('Resume file size must be less than 5MB.');
					return false;
				}

				const fileName = fileInput.files[0].name;
				if (!fileName.toLowerCase().endsWith('.pdf')) {
					e.preventDefault();
					alert('Only PDF files are allowed.');
					return false;
				}
			}
		});
	</script>
</body>
</html>
