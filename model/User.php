<?php
require_once __DIR__ . '/../db/db_connection.php';

class User {
	function establishConnection() {
		$db_connection = new DBConnection();
		$connection = $db_connection->connect();
		return $connection;
	}

	// Register a new user
	function registerUser($username, $email, $password, $role) {
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?);";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssss', $username, $email, $hashed_password, $role);
		$success = $prepared_statement->execute();
		if ($success) {
			return $connection->insert_id;
		} else {
			return $connection->error;
		}
	}

	// Login check
	function loginCheck($username, $password) {
		$sql = "SELECT * FROM users WHERE username = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('s', $username);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			if (password_verify($password, $row['password'])) {
				return $row; // Return user data including role
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	// Get user by ID
	function getUserById($user_id) {
		$sql = "SELECT * FROM users WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $user_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();

		if ($result->num_rows > 0) {
			return $result->fetch_assoc();
		} else {
			return null;
		}
	}

	// Update user profile
	function updateProfile($user_id, $email, $company_name = null, $company_industry = null, $company_description = null, $company_website = null, $headline = null) {
		$sql = "UPDATE users SET email = ?, company_name = ?, company_industry = ?, company_description = ?, company_website = ?, headline = ? WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('ssssssi', $email, $company_name, $company_industry, $company_description, $company_website, $headline, $user_id);
		$success = $prepared_statement->execute();
		return $success;
	}

	// Change password
	function changePassword($user_id, $current_password, $new_password) {
		// First verify current password
		$sql = "SELECT password FROM users WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $user_id);
		$prepared_statement->execute();
		$result = $prepared_statement->get_result();

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			if (password_verify($current_password, $row['password'])) {
				// Update password (hashed)
				$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
				$update_sql = "UPDATE users SET password = ? WHERE id = ?;";
				$update_stmt = $connection->prepare($update_sql);
				$update_stmt->bind_param('si', $hashed_new_password, $user_id);
				return $update_stmt->execute();
			} else {
				return false; // Current password doesn't match
			}
		}
		return false;
	}

	// Update profile picture
	function updateProfilePic($user_id, $pic_path) {
		$sql = "UPDATE users SET profile_pic = ? WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('si', $pic_path, $user_id);
		return $prepared_statement->execute();
	}

	// Update company logo (for employer)
	function updateCompanyLogo($user_id, $logo_path) {
		$sql = "UPDATE users SET company_logo = ? WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('si', $logo_path, $user_id);
		return $prepared_statement->execute();
	}

	// Update resume path (for seekers)
	function updateResumePath($user_id, $resume_path) {
		$sql = "UPDATE users SET resume_path = ? WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('si', $resume_path, $user_id);
		return $prepared_statement->execute();
	}

	// Delete user account
	function deleteUser($user_id) {
		$sql = "DELETE FROM users WHERE id = ?;";
		$connection = $this->establishConnection();
		$prepared_statement = $connection->prepare($sql);
		$prepared_statement->bind_param('i', $user_id);
		return $prepared_statement->execute();
	}
}
