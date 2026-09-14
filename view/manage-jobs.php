<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Job.php';
require_once __DIR__ . '/../model/Category.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	header("Location: login.php");
	exit();
}

$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$job = new Job();
$jobs = $job->getAllJobsAdmin($category_filter ?: null, $status_filter ?: null);

$category_model = new Category();
$categories = $category_model->getAllCategories();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Manage Jobs - CareerBridge</title>
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
		.nav-back {
			margin-bottom: 20px;
		}
		.nav-back a {
			color: #667eea;
			text-decoration: none;
			font-weight: bold;
		}
		.filters {
			display: flex;
			gap: 10px;
			margin-bottom: 20px;
			align-items: end;
		}
		.filters .form-group label {
			display: block;
			font-size: 12px;
			font-weight: bold;
			color: #555;
			margin-bottom: 4px;
		}
		.filters select {
			padding: 8px;
			border: 1px solid #ccc;
			border-radius: 4px;
		}
		.btn {
			padding: 9px 18px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			text-decoration: none;
			display: inline-block;
			cursor: pointer;
			font-size: 14px;
		}
		.btn:hover {
			background: #5568d3;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
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
	</style>
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Manage Jobs (Platform-wide)</h1>

		<form class="filters" method="get">
			<div class="form-group">
				<label for="category">Category</label>
				<select id="category" name="category">
					<option value="">All Categories</option>
					<?php foreach ($categories as $cat) { ?>
						<option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $category_filter === $cat['name'] ? 'selected' : ''; ?>>
							<?php echo htmlspecialchars($cat['name']); ?>
						</option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label for="status">Status</label>
				<select id="status" name="status">
					<option value="">All Statuses</option>
					<option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
					<option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
				</select>
			</div>
			<div class="form-group">
				<button type="submit" class="btn">Filter</button>
			</div>
			<?php if ($category_filter || $status_filter) { ?>
				<div class="form-group">
					<a href="manage-jobs.php" class="btn" style="background:#999;">Clear</a>
				</div>
			<?php } ?>
		</form>

		<?php if (count($jobs) > 0) { ?>
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Title</th>
						<th>Employer</th>
						<th>Category</th>
						<th>Location</th>
						<th>Type</th>
						<th>Deadline</th>
						<th>Applicants</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($jobs as $job_item) { ?>
						<tr id="job-row-<?php echo $job_item['id']; ?>">
							<td><?php echo $job_item['id']; ?></td>
							<td><?php echo htmlspecialchars($job_item['title']); ?></td>
							<td><?php echo htmlspecialchars($job_item['company_name'] ?? '—'); ?></td>
							<td><?php echo htmlspecialchars($job_item['category']); ?></td>
							<td><?php echo htmlspecialchars($job_item['location']); ?></td>
							<td><?php echo htmlspecialchars($job_item['job_type']); ?></td>
							<td><?php echo $job_item['deadline']; ?></td>
							<td><?php echo $job_item['applicant_count']; ?></td>
							<td>
								<span class="badge badge-<?php echo $job_item['status']; ?>"
									  id="status-badge-<?php echo $job_item['id']; ?>"
									  title="Click to toggle — admin can close a listing that violates policy"
									  onclick="toggleStatus(<?php echo $job_item['id']; ?>, '<?php echo $job_item['status']; ?>')">
									<?php echo ucfirst($job_item['status']); ?>
								</span>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p style="text-align: center; color: #999; padding: 30px;">No jobs match this filter.</p>
		<?php } ?>
	</div>

	<script>
		function toggleStatus(jobId, currentStatus) {
			const newStatus = currentStatus === 'active' ? 'closed' : 'active';
			const confirmMsg = newStatus === 'closed'
				? 'Close this listing (e.g. for a policy violation)?'
				: 'Reopen this listing?';
			if (!confirm(confirmMsg)) {
				return;
			}

			fetch('../controller/admin-job-status-handler.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'job_id=' + jobId + '&status=' + newStatus
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					const badge = document.getElementById('status-badge-' + jobId);
					badge.className = 'badge badge-' + newStatus;
					badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
					badge.setAttribute('onclick', "toggleStatus(" + jobId + ", '" + newStatus + "')");
				} else {
					alert(data.error || 'Failed to update status');
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
