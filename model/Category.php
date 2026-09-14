<?php
require_once __DIR__ . '/../db/db_connection.php';

class Category {
	function establishConnection() {
		$db_connection = new DBConnection();
		$connection = $db_connection->connect();
		return $connection;
	}

	// Get all categories, each with a live count of jobs currently using that
	// category name (matched against jobs.category, since jobs.category is
	// free text rather than a foreign key).
	function getAllCategories() {
		$sql = "SELECT c.*, COUNT(j.id) AS job_count
				FROM categories c
				LEFT JOIN jobs j ON j.category = c.name
				GROUP BY c.id
				ORDER BY c.name ASC;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		$categories = [];
		while ($row = $result->fetch_assoc()) {
			$categories[] = $row;
		}
		return $categories;
	}

	function getCategoryById($category_id) {
		$sql = "SELECT * FROM categories WHERE id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $category_id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows > 0 ? $result->fetch_assoc() : null;
	}

	function createCategory($name) {
		$sql = "INSERT INTO categories (name) VALUES (?);";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('s', $name);
		$success = $stmt->execute();
		if ($success) {
			return $connection->insert_id;
		} else {
			return false;
		}
	}

	// Renaming a category updates every job currently posted under the old
	// name too, so the "in use" count and job listings stay consistent.
	function updateCategory($category_id, $new_name) {
		$connection = $this->establishConnection();

		$existing = $this->getCategoryById($category_id);
		if (!$existing) {
			return false;
		}

		$connection->begin_transaction();
		try {
			$sql = "UPDATE categories SET name = ? WHERE id = ?;";
			$stmt = $connection->prepare($sql);
			$stmt->bind_param('si', $new_name, $category_id);
			$stmt->execute();

			$sql2 = "UPDATE jobs SET category = ? WHERE category = ?;";
			$stmt2 = $connection->prepare($sql2);
			$stmt2->bind_param('ss', $new_name, $existing['name']);
			$stmt2->execute();

			$connection->commit();
			return true;
		} catch (Exception $e) {
			$connection->rollback();
			return false;
		}
	}

	// Whether any job currently references this category (by name).
	function isCategoryInUse($category_id) {
		$category = $this->getCategoryById($category_id);
		if (!$category) {
			return false;
		}
		$sql = "SELECT COUNT(*) AS total FROM jobs WHERE category = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('s', $category['name']);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		return $row['total'] > 0;
	}

	function nameExists($name, $exclude_id = null) {
		if ($exclude_id !== null) {
			$sql = "SELECT id FROM categories WHERE name = ? AND id != ?;";
			$connection = $this->establishConnection();
			$stmt = $connection->prepare($sql);
			$stmt->bind_param('si', $name, $exclude_id);
		} else {
			$sql = "SELECT id FROM categories WHERE name = ?;";
			$connection = $this->establishConnection();
			$stmt = $connection->prepare($sql);
			$stmt->bind_param('s', $name);
		}
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows > 0;
	}

	// Deletion is blocked while any job still references this category.
	function deleteCategory($category_id) {
		if ($this->isCategoryInUse($category_id)) {
			return false;
		}
		$sql = "DELETE FROM categories WHERE id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $category_id);
		return $stmt->execute();
	}

	// Per-category application breakdown for the admin analytics summary.
	function getApplicationBreakdownByCategory() {
		$sql = "SELECT c.name AS category, COUNT(DISTINCT j.id) AS job_count, COUNT(a.id) AS application_count
				FROM categories c
				LEFT JOIN jobs j ON j.category = c.name
				LEFT JOIN applications a ON a.job_id = j.id
				GROUP BY c.id
				ORDER BY c.name ASC;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		$rows = [];
		while ($row = $result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}
}
