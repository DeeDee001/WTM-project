<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$user_id = $_SESSION['user_id'];
	$user = new User();

	// Determine upload type (profile pic, company logo, or resume)
	$upload_type = $_POST['upload_type'] ?? 'profile_pic';

	if ($upload_type == 'logo') {
		// Company logo upload
		$file_key = 'company_logo';
		$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
		$upload_dir = 'images/';
		$field_to_update = 'company_logo';

	} else if ($upload_type == 'resume') {
		// Resume upload (PDF only)
		$file_key = 'resume_file';
		$allowed_types = ['application/pdf'];
		$upload_dir = 'resumes/';
		$field_to_update = 'resume';

	} else {
		// Profile picture upload (default)
		$file_key = 'profile_pic';
		$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
		$upload_dir = 'images/';
		$field_to_update = 'profile_pic';
	}

	// Make sure a file was actually sent and uploaded without error
	if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
		$_SESSION['profile_error'] = 'No file was uploaded, or the upload failed.';
		header("Location: ../view/profile.php");
		exit();
	}

	$file = $_FILES[$file_key];

	// Validate file type
	if (!in_array($file['type'], $allowed_types)) {
		$_SESSION['profile_error'] = 'Invalid file type. Only JPG, PNG' . ($upload_type == 'resume' ? ', PDF' : '') . ' allowed.';
		header("Location: ../view/profile.php");
		exit();
	}

	// Validate file size (max 5MB)
	if ($file['size'] > 5000000) {
		$_SESSION['profile_error'] = 'File too large. Maximum 5MB allowed.';
		header("Location: ../view/profile.php");
		exit();
	}

	// Generate unique filename
	$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
	$unique_name = time() . '_' . $_SESSION['username'] . '.' . $file_extension;
	$target_path = __DIR__ . '/../' . $upload_dir . $unique_name;
	$db_path = $upload_dir . $unique_name;

	// Move uploaded file
	if (move_uploaded_file($file['tmp_name'], $target_path)) {
		// Update database based on field type
		if ($field_to_update == 'profile_pic') {
			$success = $user->updateProfilePic($user_id, $db_path);
			if ($success) $_SESSION['profile_pic'] = $db_path;
		} else if ($field_to_update == 'company_logo') {
			$success = $user->updateCompanyLogo($user_id, $db_path);
			if ($success) $_SESSION['company_logo'] = $db_path;
		} else if ($field_to_update == 'resume') {
			$success = $user->updateResumePath($user_id, $db_path);
			if ($success) $_SESSION['resume_path'] = $db_path;
		}

		if ($success) {
			$_SESSION['profile_success'] = ucfirst($upload_type) . ' uploaded successfully';
		} else {
			$_SESSION['profile_error'] = 'Failed to update database';
		}
	} else {
		$_SESSION['profile_error'] = 'Failed to upload file';
	}

	header("Location: ../view/profile.php");
} else {
	header("Location: ../view/profile.php");
}
