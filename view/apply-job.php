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
	<style>
		body {
			font-family: Arial, sans-serif;
			background: #f4f7f6;
			margin: 0;
			padding: 0;
		}
		.container {
			max-width: 800px;
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
		.job-info {
			background: #e8eaf6;
			padding: 20px;
			border-radius: 5px;
			margin-bottom: 25px;
		}
		.job-title {
			font-size: 22px;
			font-weight: bold;
			color: #333;
			margin-bottom: 10px;
		}
		.company-name {
			color: #667eea;
			font-size: 16px;
		}
		.form-group {
			margin-bottom: 20px;
		}
		.form-group label {
			display: block;
			font-weight: bold;
			margin-bottom: 8px;
			color: #555;
		}
		.form-group textarea, .form-group input[type="file"] {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			box-sizing: border-box;
			font-family: Arial, sans-serif;
		}
		.form-group textarea {
			min-height: 150px;
			resize: vertical;
		}
		.resume-options {
			background: #f9f9f9;
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 15px;
		}
		.resume-options label {
			display: block;
			margin-bottom: 10px;
			font-weight: normal;
		}
		.resume-options input[type="radio"] {
			margin-right: 8px;
		}
		.current-resume {
			color: #667eea;
			font-size: 14px;
			margin-left: 24px;
		}
		.btn {
			padding: 12px 30px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 16px;
		}
		.btn:hover {
			background: #5568d3;
		}
		.btn-secondary {
			background: #999;
			margin-left: 10px;
		}
		.btn-secondary:hover {
			background: #777;
		}
		.alert {
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 20px;
		}
		.alert-error {
			background: #ffebee;
			color: #c62828;
			border: 1px solid #ef5350;
		}
		.alert-info {
			background: #e3f2fd;
			color: #1565c0;
			border: 1px solid #42a5f5;
		}
		.note {
			font-size: 14px;
			color: #666;
			margin-top: 5px;
		}
	</style>
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
