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
		}
		.btn:hover {
			background: #5568d3;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}
		th, td {
			border: 1px solid #ddd;
			padding: 12px;
			text-align: left;
		}
		th {
			background: #667eea;
			color: white;
		}
		tr:hover {
			background: #f5f5f5;
		}
		.badge {
			padding: 5px 10px;
			border-radius: 3px;
			font-size: 12px;
			cursor: pointer;
			display: inline-block;
		}
		.badge-active {
			background: #4caf50;
			color: white;
		}
		.badge-closed {
			background: #f44336;
			color: white;
		}
		.action-links a {
			margin-right: 10px;
			color: #667eea;
			text-decoration: none;
		}
		.action-links a:hover {
			text-decoration: underline;
		}
	</style>
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