<?php
session_start();

// Router check
if (isset($_SESSION['username'])) {
	$role = $_SESSION['role'];

	// Redirect depending on user role
	if ($role == 'employer') {
		require_once __DIR__ . '/view/employer-dashboard.php';
	} else if ($role == 'seeker') {
		require_once __DIR__ . '/view/seeker-dashboard.php';
	} else if ($role == 'recruiter') {
		require_once __DIR__ . '/view/recruiter-dashboard.php';
	} else if ($role == 'admin') {
		require_once __DIR__ . '/view/admin-dashboard.php';
	} else {
		// Fallback: unrecognized role, treat as logged out
		session_unset();
		session_destroy();
		require_once __DIR__ . '/view/login.php';
	}
} else {
	// If not logged in, show login page
	require_once __DIR__ . '/view/login.php';
}
