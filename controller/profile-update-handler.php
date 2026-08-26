<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user_id = $_SESSION['user_id'];
	$user = new User();

	// Check if this is a delete account action
	if (isset($_POST['action_type']) && $_POST['action_type'] == 'delete_account') {
		$success = $user->deleteUser($user_id);
		if ($success) {
			// Destroy session and redirect to homepage
			session_unset();
			session_destroy();
			header("Location: ../index.php");
		} else {
			$_SESSION['profile_error'] = 'Failed to delete account';
			header("Location: ../view/profile.php");
		}
		exit();
	}

	// Regular profile update
	$email = $_POST['email'];
	$company_name = $_POST['company_name'] ?? null;
	$company_industry = $_POST['company_industry'] ?? null;
	$company_description = $_POST['company_description'] ?? null;
	$company_website = $_POST['company_website'] ?? null;
	$headline = $_POST['headline'] ?? null;

	$success = $user->updateProfile(
		$user_id,
		$email,
		$company_name,
		$company_industry,
		$company_description,
		$company_website,
		$headline
	);

	if ($success) {
		// Update session variables
		$_SESSION['email'] = $email;
		if ($company_name) $_SESSION['company_name'] = $company_name;
		if ($headline) $_SESSION['headline'] = $headline;

		$_SESSION['profile_success'] = 'Profile updated successfully';
	} else {
		$_SESSION['profile_error'] = 'Failed to update profile';
	}

	header("Location: ../view/profile.php");
} else {
	header("Location: ../view/profile.php");
}
