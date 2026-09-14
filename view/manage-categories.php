<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/Category.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	header("Location: login.php");
	exit();
}

$category = new Category();
$categories = $category->getAllCategories();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Manage Categories - CareerBridge</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background: #f4f7f6;
			margin: 0;
			padding: 0;
		}
		.container {
			max-width: 900px;
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
		.alert {
			padding: 12px;
			margin-bottom: 20px;
			border-radius: 4px;
		}
		.alert-error {
			background: #ffebee;
			color: #c62828;
		}
		.alert-success {
			background: #e8f5e9;
			color: #2e7d32;
		}
		.add-form {
			display: flex;
			gap: 10px;
			margin-bottom: 25px;
		}
		.add-form input {
			flex: 1;
			padding: 10px;
			border: 1px solid #ccc;
			border-radius: 4px;
			font-size: 14px;
		}
		.btn {
			padding: 10px 20px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 14px;
		}
		.btn:hover {
			background: #5568d3;
		}
		.btn-danger {
			background: #f44336;
		}
		.btn-danger:hover {
			background: #d32f2f;
		}
		.btn-danger:disabled {
			background: #ccc;
			cursor: not-allowed;
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
			padding: 3px 10px;
			border-radius: 3px;
			font-size: 12px;
			background: #e8eaf6;
			color: #333;
		}
		.rename-form {
			display: flex;
			gap: 6px;
		}
		.rename-form input {
			padding: 6px;
			border: 1px solid #ccc;
			border-radius: 4px;
			width: 140px;
		}
		.rename-form .btn, .action-links button {
			padding: 6px 12px;
			font-size: 13px;
		}
		.action-links button {
			border: none;
			border-radius: 4px;
			color: white;
			cursor: pointer;
		}
	</style>
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Manage Categories</h1>

		<?php if (isset($_SESSION['category_error'])) { ?>
			<div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['category_error']); unset($_SESSION['category_error']); ?></div>
		<?php } ?>
		<?php if (isset($_SESSION['category_success'])) { ?>
			<div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['category_success']); unset($_SESSION['category_success']); ?></div>
		<?php } ?>

		<form class="add-form" action="../controller/category-create-handler.php" method="post">
			<input type="text" name="name" placeholder="New category name, e.g. Design" required>
			<button type="submit" class="btn">Add Category</button>
		</form>

		<?php if (count($categories) > 0) { ?>
			<table>
				<thead>
					<tr>
						<th>Name</th>
						<th>Jobs Using It</th>
						<th>Rename</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($categories as $cat) { ?>
						<tr id="category-row-<?php echo $cat['id']; ?>">
							<td><?php echo htmlspecialchars($cat['name']); ?></td>
							<td><span class="badge"><?php echo $cat['job_count']; ?></span></td>
							<td>
								<form class="rename-form" action="../controller/category-update-handler.php" method="post">
									<input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
									<input type="text" name="name" value="<?php echo htmlspecialchars($cat['name']); ?>" required>
									<button type="submit" class="btn">Save</button>
								</form>
							</td>
							<td class="action-links">
								<button
									style="background: #f44336;"
									onclick="deleteCategory(<?php echo $cat['id']; ?>, <?php echo (int)$cat['job_count']; ?>)"
									<?php echo $cat['job_count'] > 0 ? 'disabled title="In use — cannot delete"' : ''; ?>>
									Delete
								</button>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p style="text-align: center; color: #999; padding: 30px;">No categories yet. Add one above.</p>
		<?php } ?>
	</div>

	<script>
		function deleteCategory(categoryId, jobCount) {
			if (jobCount > 0) {
				alert('This category is still used by ' + jobCount + ' job(s) and cannot be deleted.');
				return;
			}
			if (!confirm('Delete this category? This cannot be undone.')) {
				return;
			}

			fetch('../controller/category-delete-handler.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'category_id=' + categoryId
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					const row = document.getElementById('category-row-' + categoryId);
					row.parentNode.removeChild(row);
				} else {
					alert(data.error || 'Failed to delete category');
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Error deleting category');
			});
		}
	</script>
</body>
</html>
