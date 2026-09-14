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
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/manage-jobs.css' : 'css/manage-jobs.css'); ?>">
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
