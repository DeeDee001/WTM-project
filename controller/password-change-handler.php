<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user_id = $_SESSION['user_id'];
	$current_password = $_POST['current_password'];
	$new_password = $_POST['new_password'];

	$user = new User();
	$success = $user->changePassword($user_id, $current_password, $new_password);

	if ($success) {
		$_SESSION['profile_success'] = 'Password changed successfully';
	} else {
		$_SESSION['profile_error'] = 'Failed to change password. Current password may be incorrect.';
	}

	header("Location: ../view/profile.php");
} else {
	header("Location: ../view/profile.php");
}
