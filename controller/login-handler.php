<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];

	$user = new User();
	$loginSuccess = $user->loginCheck($username, $password);

	if ($loginSuccess) {
		// Regenerate session ID to prevent session fixation
		session_regenerate_id(true);

		// Set session variables
		$_SESSION['user_id'] = $loginSuccess['id'];
		$_SESSION['username'] = $loginSuccess['username'];
		$_SESSION['email'] = $loginSuccess['email'];
		$_SESSION['role'] = $loginSuccess['role'];
		$_SESSION['profile_pic'] = $loginSuccess['profile_pic'];

		// Store additional info based on role
		if ($loginSuccess['role'] == 'employer') {
			$_SESSION['company_name'] = $loginSuccess['company_name'];
			$_SESSION['company_logo'] = $loginSuccess['company_logo'];
		} else if ($loginSuccess['role'] == 'seeker') {
			$_SESSION['headline'] = $loginSuccess['headline'];
			$_SESSION['resume_path'] = $loginSuccess['resume_path'];
		}

		// Redirect to home (index.php will route to appropriate dashboard)
		header("Location: ../");
	} else {
		$_SESSION['login_error'] = 'Invalid username or password';
		header("Location: ../index.php");
	}
} else {
	header("Location: ../index.php");
}
