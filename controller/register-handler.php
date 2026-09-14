<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$role = $_POST['role'];

	// Basic validation
	if (empty($username) || empty($email) || empty($password) || empty($role)) {
		$_SESSION['register_error'] = 'All fields are required';
		header("Location: ../view/register.php");
		exit();
	}

	// Check if role is valid
	$valid_roles = ['employer', 'seeker', 'recruiter', 'admin'];
	if (!in_array($role, $valid_roles)) {
		$_SESSION['register_error'] = 'Invalid role selected';
		header("Location: ../view/register.php");
		exit();
	}

	$user = new User();
	$result = $user->registerUser($username, $email, $password, $role);

	if (is_int($result)) {
		// Registration successful
		$_SESSION['registration_success'] = 'Registration successful! Please login.';
		header('Location: ../index.php');
	} else {
		// Registration failed - likely duplicate username or email
		$_SESSION['register_error'] = 'Registration failed. Username or email already exists.';
		header('Location: ../view/register.php');
	}
} else {
	header("Location: ../view/register.php");
}
