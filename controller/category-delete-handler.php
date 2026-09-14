<?php
require_once __DIR__ . '/../model/Category.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	echo json_encode(['success' => false, 'error' => 'Unauthorized']);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$category_id = intval($_POST['category_id']);
	$category = new Category();

	if ($category->isCategoryInUse($category_id)) {
		echo json_encode(['success' => false, 'error' => 'This category is still used by one or more jobs and cannot be deleted.']);
		exit();
	}

	$success = $category->deleteCategory($category_id);

	if ($success) {
		echo json_encode(['success' => true]);
	} else {
		echo json_encode(['success' => false, 'error' => 'Delete failed']);
	}
} else {
	echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
