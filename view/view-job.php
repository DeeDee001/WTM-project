<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Job.php';

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
	</style>
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back" style="margin-bottom: 20px;">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1><?php echo htmlspecialchars($job_detail['title']); ?></h1>
		<div class="meta-info">
			📁 <?php echo htmlspecialchars($job_detail['category']); ?> |
			📍 <?php echo htmlspecialchars($job_detail['location']); ?> |
			💼 <?php echo htmlspecialchars($job_detail['job_type']); ?>
		</div>

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
</body>
</html>
