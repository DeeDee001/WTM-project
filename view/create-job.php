<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employer') {
	header("Location: login.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Post New Job - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/create-job.css' : 'css/create-job.css'); ?>">
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Post a New Job</h1>

		<?php if (isset($_SESSION['job_error'])) { ?>
			<div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['job_error']); unset($_SESSION['job_error']); ?></div>
		<?php } ?>

		<form action="../controller/create-job-handler.php" method="post">
			<div class="form-group">
				<label for="title">Job Title *</label>
				<input type="text" id="title" name="title" required>
			</div>

			<div class="form-group">
				<label for="category">Category *</label>
				<input type="text" id="category" name="category" placeholder="e.g. IT, Finance, HR, Marketing" required>
			</div>

			<div class="form-group">
				<label for="description">Job Description *</label>
				<textarea id="description" name="description" required></textarea>
			</div>

			<div class="form-group">
				<label for="requirements">Requirements *</label>
				<textarea id="requirements" name="requirements" required></textarea>
			</div>

			<div class="form-group">
				<label for="salary_range">Salary Range *</label>
				<input type="text" id="salary_range" name="salary_range" placeholder="e.g. BDT 30,000 - 50,000" required>
			</div>

			<div class="form-group">
				<label for="location">Location *</label>
				<input type="text" id="location" name="location" placeholder="e.g. Dhaka, Remote" required>
			</div>

			<div class="form-group">
				<label for="job_type">Job Type *</label>
				<select id="job_type" name="job_type" required>
					<option value="">-- Select --</option>
					<option value="Full-time">Full-time</option>
					<option value="Part-time">Part-time</option>
					<option value="Remote">Remote</option>
					<option value="Contract">Contract</option>
					<option value="Internship">Internship</option>
				</select>
			</div>

			<div class="form-group">
				<label for="deadline">Application Deadline *</label>
				<input type="date" id="deadline" name="deadline" required>
			</div>

			<button type="submit" class="btn">Post Job</button>
		</form>
	</div>
</body>
</html>
