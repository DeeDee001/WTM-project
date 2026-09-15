<?php
require_once __DIR__ . '/../db/db_connection.php';

class JobSeeker {
	function establishConnection() {
		$db_connection = new DBConnection();
		$connection = $db_connection->connect();
		return $connection;
	}

	// Search and filter jobs with AJAX support
	function searchJobs($keyword = '', $category = '', $location = '', $job_type = '', $salary_min = '', $salary_max = '') {
		$sql = "SELECT j.*, u.company_name, u.company_logo
				FROM jobs j
				JOIN users u ON j.employer_id = u.id
				WHERE j.status = 'active'";

		$params = [];
		$types = '';

		if (!empty($keyword)) {
			$sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.requirements LIKE ?)";
			$keywordParam = '%' . $keyword . '%';
			$params[] = $keywordParam;
			$params[] = $keywordParam;
			$params[] = $keywordParam;
			$types .= 'sss';
		}

		if (!empty($category)) {
			$sql .= " AND j.category = ?";
			$params[] = $category;
			$types .= 's';
		}

		if (!empty($location)) {
			$sql .= " AND j.location LIKE ?";
			$params[] = '%' . $location . '%';
			$types .= 's';
		}

		if (!empty($job_type)) {
			$sql .= " AND j.job_type = ?";
			$params[] = $job_type;
			$types .= 's';
		}

		// Salary ranges are stored as display text, for example "BDT 30,000 - 50,000".
		if (!empty($salary_min)) {
			$sql .= " AND CAST(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(j.salary_range, '-', -1), 'BDT', ''), ',', ''), '$', ''), ' ', '') AS UNSIGNED) >= ?";
			$params[] = $salary_min;
			$types .= 'i';
		}

		if (!empty($salary_max)) {
			$sql .= " AND CAST(REPLACE(REPLACE(REPLACE(REPLACE(SUBSTRING_INDEX(j.salary_range, '-', 1), 'BDT', ''), ',', ''), '$', ''), ' ', '') AS UNSIGNED) <= ?";
			$params[] = $salary_max;
			$types .= 'i';
		}

		$sql .= " ORDER BY j.created_at DESC";

		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);

		if (!empty($params)) {
			$stmt->bind_param($types, ...$params);
		}

		$stmt->execute();
		$result = $stmt->get_result();

		$jobs = [];
		while ($row = $result->fetch_assoc()) {
			$jobs[] = $row;
		}
		return $jobs;
	}

	// Check if job is saved by user
	function isJobSaved($user_id, $job_id) {
		$sql = "SELECT id FROM saved_jobs WHERE seeker_id = ? AND job_id = ? LIMIT 1";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('ii', $user_id, $job_id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows > 0;
	}

	// Save/bookmark a job
	function saveJob($user_id, $job_id) {
		$sql = "INSERT INTO saved_jobs (seeker_id, job_id) VALUES (?, ?)";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('ii', $user_id, $job_id);
		return $stmt->execute();
	}

	// Unsave/unbookmark a job
	function unsaveJob($user_id, $job_id) {
		$sql = "DELETE FROM saved_jobs WHERE seeker_id = ? AND job_id = ?";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('ii', $user_id, $job_id);
		return $stmt->execute();
	}

	// Get all saved jobs for a user
	function getSavedJobs($user_id) {
		$sql = "SELECT j.*, u.company_name, u.company_logo, sj.created_at as saved_at
				FROM saved_jobs sj
				JOIN jobs j ON sj.job_id = j.id
				JOIN users u ON j.employer_id = u.id
				WHERE sj.seeker_id = ?
				ORDER BY sj.created_at DESC";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $user_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$jobs = [];
		while ($row = $result->fetch_assoc()) {
			$jobs[] = $row;
		}
		return $jobs;
	}

	// Apply for a job
	function applyForJob($job_id, $seeker_id, $cover_letter, $resume_path) {
		$sql = "INSERT INTO applications (job_id, seeker_id, cover_letter, resume_path, status)
				VALUES (?, ?, ?, ?, 'submitted')";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('iiss', $job_id, $seeker_id, $cover_letter, $resume_path);
		return $stmt->execute();
	}

	// Check if user already applied for a job
	function hasApplied($job_id, $seeker_id) {
		$sql = "SELECT id FROM applications WHERE job_id = ? AND seeker_id = ? LIMIT 1";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('ii', $job_id, $seeker_id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows > 0;
	}

	// Get all applications by a seeker
	function getSeekerApplications($seeker_id) {
		$sql = "SELECT a.*, j.title as job_title, j.category, j.location, j.job_type,
				u.company_name, u.company_logo
				FROM applications a
				JOIN jobs j ON a.job_id = j.id
				JOIN users u ON j.employer_id = u.id
				WHERE a.seeker_id = ?
				ORDER BY a.created_at DESC";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $seeker_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$applications = [];
		while ($row = $result->fetch_assoc()) {
			$applications[] = $row;
		}
		return $applications;
	}

	// Get distinct categories for filter dropdown
	function getCategories() {
		$sql = "SELECT DISTINCT category FROM jobs WHERE status = 'active' ORDER BY category";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);

		$categories = [];
		while ($row = $result->fetch_assoc()) {
			$categories[] = $row['category'];
		}
		return $categories;
	}

	// Get distinct job types for filter dropdown
	function getJobTypes() {
		$sql = "SELECT DISTINCT job_type FROM jobs WHERE status = 'active' ORDER BY job_type";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);

		$types = [];
		while ($row = $result->fetch_assoc()) {
			$types[] = $row['job_type'];
		}
		return $types;
	}
}
