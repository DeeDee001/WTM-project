<?php
require_once __DIR__ . '/../model/Category.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$category_id = intval($_POST['category_id']);
	$name = trim($_POST['name']);
	$category = new Category();

	if ($name === '') {
		$_SESSION['category_error'] = 'Category name cannot be empty.';
	} else if ($category->nameExists($name, $category_id)) {
		$_SESSION['category_error'] = 'Another category already has that name.';
	} else {
		$success = $category->updateCategory($category_id, $name);
		if ($success) {
			$_SESSION['category_success'] = 'Category updated successfully!';
		} else {
			$_SESSION['category_error'] = 'Failed to update category. Please try again.';
		}
	}
}

header("Location: ../view/manage-categories.php");
