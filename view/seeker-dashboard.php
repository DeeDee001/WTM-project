<?php
// Session already started by index.php
<<<<<<< HEAD
require_once __DIR__ . '/../model/JobSeeker.php';

=======
>>>>>>> 26ffc689ade1d0e3ba660de2ec0d3ce76f306b7a
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
	header("Location: view/login.php");
	exit();
}
<<<<<<< HEAD

$jobSeeker = new JobSeeker();
$user_id = $_SESSION['user_id'];

// Get stats
$applications = $jobSeeker->getSeekerApplications($user_id);
$savedJobs = $jobSeeker->getSavedJobs($user_id);

$total_applications = count($applications);
$total_saved = count($savedJobs);
$pending_count = count(array_filter($applications, fn($a) => $a['status'] == 'submitted'));
$shortlisted_count = count(array_filter($applications, fn($a) => $a['status'] == 'shortlisted'));
=======
>>>>>>> 26ffc689ade1d0e3ba660de2ec0d3ce76f306b7a
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Job Seeker Dashboard - CareerBridge</title>
	<style>
<<<<<<< HEAD
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
		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 20px;
			margin-bottom: 30px;
		}
		.stat-card {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 25px;
			border-radius: 8px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			transition: transform 0.2s;
		}
		.stat-card:hover {
			transform: translateY(-5px);
		}
		.stat-card.green {
			background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
		}
		.stat-card.orange {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
		}
		.stat-card.blue {
			background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
		}
		.stat-number {
			font-size: 48px;
			font-weight: bold;
			margin-bottom: 5px;
		}
		.stat-label {
			font-size: 16px;
			opacity: 0.9;
		}
		.quick-actions {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 20px;
			margin-top: 25px;
		}
		.action-card {
			background: white;
			border: 2px solid #e0e0e0;
			border-radius: 8px;
			padding: 25px;
			text-align: center;
			transition: all 0.3s;
			cursor: pointer;
		}
		.action-card:hover {
			border-color: #667eea;
			transform: translateY(-5px);
			box-shadow: 0 8px 20px rgba(102,126,234,0.2);
		}
		.action-icon {
			font-size: 48px;
			margin-bottom: 15px;
		}
		.action-title {
			font-size: 20px;
			font-weight: bold;
			color: #333;
			margin-bottom: 10px;
		}
		.action-desc {
			color: #666;
			font-size: 14px;
			margin-bottom: 15px;
		}
		.btn-action {
			display: inline-block;
			padding: 10px 25px;
			background: #667eea;
			color: white;
			text-decoration: none;
			border-radius: 4px;
			transition: background 0.3s;
		}
		.btn-action:hover {
			background: #5568d3;
		}
		.recent-section {
			margin-top: 30px;
		}
		.section-title {
			font-size: 22px;
			color: #333;
			margin-bottom: 15px;
			border-left: 4px solid #667eea;
			padding-left: 15px;
		}
	</style>
</head>
<body>
	<?php $nav_base = 'view/'; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Job Seeker Dashboard</h1>

		<div class="welcome">
			<h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
			<p>Find your dream job and track your applications all in one place.</p>
		</div>

		<div class="stats-grid">
			<div class="stat-card">
				<div class="stat-number"><?php echo $total_applications; ?></div>
				<div class="stat-label">Total Applications</div>
			</div>
			<div class="stat-card green">
				<div class="stat-number"><?php echo $shortlisted_count; ?></div>
				<div class="stat-label">Shortlisted</div>
			</div>
			<div class="stat-card orange">
				<div class="stat-number"><?php echo $pending_count; ?></div>
				<div class="stat-label">Pending Review</div>
			</div>
			<div class="stat-card blue">
				<div class="stat-number"><?php echo $total_saved; ?></div>
				<div class="stat-label">Saved Jobs</div>
			</div>
		</div>

		<h3 class="section-title">Quick Actions</h3>

		<div class="quick-actions">
			<div class="action-card" onclick="window.location.href='view/search-jobs.php'">
				<div class="action-icon">🔍</div>
				<div class="action-title">Search Jobs</div>
				<div class="action-desc">Browse and filter active job listings from top companies</div>
				<a href="view/search-jobs.php" class="btn-action">Start Searching</a>
			</div>

			<div class="action-card" onclick="window.location.href='view/my-applications.php'">
				<div class="action-icon">📋</div>
				<div class="action-title">My Applications</div>
				<div class="action-desc">Track the status of all your job applications</div>
				<a href="view/my-applications.php" class="btn-action">View Applications</a>
			</div>

			<div class="action-card" onclick="window.location.href='view/saved-jobs.php'">
				<div class="action-icon">💼</div>
				<div class="action-title">Saved Jobs</div>
				<div class="action-desc">Review jobs you've bookmarked for later</div>
				<a href="view/saved-jobs.php" class="btn-action">View Saved Jobs</a>
			</div>

			<div class="action-card" onclick="window.location.href='view/profile.php'">
				<div class="action-icon">👤</div>
				<div class="action-title">Update Profile</div>
				<div class="action-desc">Keep your profile and resume up to date</div>
				<a href="view/profile.php" class="btn-action">Edit Profile</a>
			</div>
		</div>

		<?php if (count($applications) > 0) {
			// Show recent applications
			$recent_apps = array_slice($applications, 0, 3);
		?>
		<div class="recent-section">
			<h3 class="section-title">Recent Applications</h3>
			<?php foreach ($recent_apps as $app) { ?>
				<div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
					<div>
						<strong><?php echo htmlspecialchars($app['job_title']); ?></strong>
						<div style="color: #666; font-size: 14px; margin-top: 5px;">
							<?php echo htmlspecialchars($app['company_name']); ?> • Applied <?php echo date('M d, Y', strtotime($app['created_at'])); ?>
						</div>
					</div>
					<div>
						<span style="padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold;
							background: <?php
								echo $app['status'] == 'submitted' ? '#e3f2fd' : ($app['status'] == 'shortlisted' ? '#e8f5e9' : ($app['status'] == 'reviewed' ? '#fff3e0' : '#ffebee'));
							?>;
							color: <?php
								echo $app['status'] == 'submitted' ? '#1976d2' : ($app['status'] == 'shortlisted' ? '#388e3c' : ($app['status'] == 'reviewed' ? '#f57c00' : '#d32f2f'));
							?>;">
							<?php echo ucfirst($app['status']); ?>
						</span>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php } ?>
=======
		body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
		.container { max-width: 1200px; margin: 30px auto; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 30px; }
		h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
		.welcome { background: #e8eaf6; padding: 20px; border-radius: 5px; margin-bottom: 25px; }
	</style>
</head>
<body>
	<div style="background: #667eea; color: white; padding: 15px 30px;">
		<h2 style="margin: 0; display: inline;">CareerBridge - Job Seeker</h2>
		<div style="float: right;">
			<a href="index.php" style="color: white; margin-right: 15px; text-decoration: none;">Dashboard</a>
			<a href="view/profile.php" style="color: white; margin-right: 15px; text-decoration: none;">Profile</a>
			<a href="controller/logout-handler.php" style="color: white; text-decoration: none;">Logout</a>
		</div>
		<div style="clear: both;"></div>
	</div>
	<div class="container">
		<h1>Job Seeker Dashboard</h1>
		<div class="welcome">
			<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
			<p>This dashboard is under development by your teammates.</p>
		</div>
>>>>>>> 26ffc689ade1d0e3ba660de2ec0d3ce76f306b7a
	</div>
</body>
</html>
