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
		.welcome {
			background: #e8eaf6;
			padding: 20px;
			border-radius: 5px;
			margin-bottom: 25px;
		}
		.actions {
			margin-bottom: 20px;
		}
		.btn {
			padding: 10px 20px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			text-decoration: none;
			display: inline-block;
			cursor: pointer;
			margin-right: 10px;
		}
		.btn:hover {
			background: #5568d3;
		}
		.summary-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
			gap: 15px;
			margin: 25px 0;
		}
		.summary-card {
			background: #e8eaf6;
			border-radius: 6px;
			padding: 18px;
			text-align: center;
		}
		.summary-card .number {
			font-size: 28px;
			font-weight: bold;
			color: #667eea;
		}
		.summary-card .label {
			font-size: 13px;
			color: #555;
			margin-top: 5px;
		}
	</style>
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
