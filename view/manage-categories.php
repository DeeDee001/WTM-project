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
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/manage-categories.css' : 'css/manage-categories.css'); ?>">
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
