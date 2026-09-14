<?php
// Session already started by index.php
require_once __DIR__ . '/../model/Job.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employer') {
	header("Location: view/login.php");
	exit();
}

$job = new Job();
$jobs = $job->getEmployerJobs($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Employer Dashboard - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/employer-dashboard.css' : 'css/employer-dashboard.css'); ?>">
</head>
<body>
	<?php $nav_base = 'view/'; include __DIR__ . '/layout/header.php'; ?>

	<div class="container">
		<h1>Employer Dashboard</h1>

		<div class="welcome">
			<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
			<p>Company: <strong><?php echo htmlspecialchars($_SESSION['company_name'] ?? 'Not set'); ?></strong></p>
		</div>

		<div class="actions">
			<a href="view/create-job.php" class="btn">Post New Job</a>
			<a href="view/company-profile.php" class="btn">Edit Company Profile</a>
		</div>

		<h3>Your Posted Jobs</h3>

		<?php if (count($jobs) > 0) { ?>
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Job Title</th>
						<th>Category</th>
						<th>Deadline</th>
						<th>Applicants</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($jobs as $job_item) { ?>
						<tr id="job-row-<?php echo $job_item['id']; ?>">
							<td><?php echo $job_item['id']; ?></td>
							<td><?php echo htmlspecialchars($job_item['title']); ?></td>
							<td><?php echo htmlspecialchars($job_item['category']); ?></td>
							<td><?php echo $job_item['deadline']; ?></td>
							<td><?php echo $job_item['applicant_count']; ?></td>
							<td>
								<span class="badge badge-<?php echo $job_item['status']; ?>"
									  id="status-badge-<?php echo $job_item['id']; ?>"
									  onclick="toggleStatus(<?php echo $job_item['id']; ?>, '<?php echo $job_item['status']; ?>')">
									<?php echo ucfirst($job_item['status']); ?>
								</span>
							</td>
							<td class="action-links">
								<a href="view/edit-job.php?id=<?php echo $job_item['id']; ?>">Edit</a>
								<a href="view/view-job.php?id=<?php echo $job_item['id']; ?>">View</a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p style="text-align: center; color: #999; padding: 30px;">No jobs posted yet. <a href="view/create-job.php">Post your first job</a></p>
		<?php } ?>
	</div>

	<script>
		function toggleStatus(jobId, currentStatus) {
			// Toggle between active and closed
			const newStatus = currentStatus === 'active' ? 'closed' : 'active';

			// AJAX request to toggle status
			fetch('controller/toggle-job-status.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'job_id=' + jobId + '&status=' + newStatus
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Update the badge
					const badge = document.getElementById('status-badge-' + jobId);
					badge.className = 'badge badge-' + newStatus;
					badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
					badge.setAttribute('onclick', "toggleStatus(" + jobId + ", '" + newStatus + "')");
				} else {
					alert('Failed to update status');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Error updating status');
			});
		}
	</script>
</body>
</html>