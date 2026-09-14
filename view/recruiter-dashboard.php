<?php
// Session already started by index.php
require_once __DIR__ . '/../model/Application.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'recruiter') {
	header("Location: view/login.php");
	exit();
}

$application = new Application();
$jobs = $application->getAllJobsForReview();
$open_jobs_count = count($jobs);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Recruiter Dashboard - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/recruiter-dashboard.css' : 'css/recruiter-dashboard.css'); ?>">
</head>
<body>
	<?php $nav_base = 'view/'; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Recruiter Dashboard</h1>

		<div class="welcome">
			<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
			<p>Review applications across every job posted on CareerBridge.</p>
		</div>

		<div class="summary-grid">
			<div class="summary-card">
				<div class="number"><?php echo $open_jobs_count; ?></div>
				<div class="label">Jobs on Platform</div>
			</div>
		</div>

		<div class="actions">
			<a href="view/review-applications.php" class="btn">Review Applications</a>
			<a href="view/analytics.php" class="btn">Application Funnel Analytics</a>
		</div>
	</div>
</body>
</html>
