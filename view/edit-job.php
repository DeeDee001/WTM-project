<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Job.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employer') {
	header("Location: login.php");
	exit();
}

$job_id = intval($_GET['id']);
$job = new Job();
$job_detail = $job->getJobDetails($job_id);

if (!$job_detail || $job_detail['employer_id'] != $_SESSION['user_id']) {
	$_SESSION['dashboard_error'] = 'Job not found or you do not have permission.';
	header("Location: ../index.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang='en'>

<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Edit Job - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/edit-job.css' : 'css/edit-job.css'); ?>">
</head>

<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Edit Job Post</h1>

		<form action="../controller/edit-job-handler.php" method="post">
			<input type="hidden" name="job_id" value="<?php echo $job_detail['id']; ?>">

			<div class="form-group">
				<label for="title">Job Title *</label>
				<input type="text" id="title" name="title" value="<?php echo htmlspecialchars($job_detail['title']); ?>"
					required>
			</div>

			<div class="form-group">
				<label for="category">Category *</label>
				<input type="text" id="category" name="category"
					value="<?php echo htmlspecialchars($job_detail['category']); ?>" required>
			</div>

			<div class="form-group">
				<label for="description">Job Description *</label>
				<textarea id="description" name="description"
					required><?php echo htmlspecialchars($job_detail['description']); ?></textarea>
			</div>

			<div class="form-group">
				<label for="requirements">Requirements *</label>
				<textarea id="requirements" name="requirements"
					required><?php echo htmlspecialchars($job_detail['requirements']); ?></textarea>
			</div>

			<div class="form-group">
				<label for="salary_range">Salary Range *</label>
				<input type="text" id="salary_range" name="salary_range"
					value="<?php echo htmlspecialchars($job_detail['salary_range']); ?>" required>
			</div>

			<div class="form-group">
				<label for="location">Location *</label>
				<input type="text" id="location" name="location"
					value="<?php echo htmlspecialchars($job_detail['location']); ?>" required>
			</div>

			<div class="form-group">
				<label for="job_type">Job Type *</label>
				<select id="job_type" name="job_type" required>
					<option value="Full-time" <?php echo $job_detail['job_type'] == 'Full-time' ? 'selected' : ''; ?>>
						Full-time</option>
					<option value="Part-time" <?php echo $job_detail['job_type'] == 'Part-time' ? 'selected' : ''; ?>>
						Part-time</option>
					<option value="Remote" <?php echo $job_detail['job_type'] == 'Remote' ? 'selected' : ''; ?>>Remote
					</option>
					<option value="Contract" <?php echo $job_detail['job_type'] == 'Contract' ? 'selected' : ''; ?>>
						Contract</option>
					<option value="Internship" <?php echo $job_detail['job_type'] == 'Internship' ? 'selected' : ''; ?>>
						Internship</option>
				</select>
			</div>

			<div class="form-group">
				<label for="deadline">Application Deadline *</label>
				<input type="date" id="deadline" name="deadline" value="<?php echo $job_detail['deadline']; ?>"
					required>
			</div>

			<button type="submit" class="btn">Update Job</button>
		</form>
	</div>
</body>

</html>