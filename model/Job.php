<?php
require_once __DIR__ . '/../db/db_connection.php';

class Job {
	function establishConnection() {
		$db_connection = new DBConnection();
		$connection = $db_connection->connect();
		return $connection;
	}

	function createJob($employer_id, $title, $category, $description, $requirements, $salary_range, $location, $job_type, $deadline) {
		$sql = "INSERT INTO jobs (employer_id, title, category, description, requirements, salary_range, location, job_type, deadline, status)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active');";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('issssssss', $employer_id, $title, $category, $description, $requirements, $salary_range, $location, $job_type, $deadline);
		return $stmt->execute();
	}

	function updateJob($job_id, $title, $category, $description, $requirements, $salary_range, $location, $job_type, $deadline, $employer_id) {
		$sql = "UPDATE jobs SET title = ?, category = ?, description = ?, requirements = ?, salary_range = ?, location = ?, job_type = ?, deadline = ? WHERE id = ? AND employer_id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('ssssssssii', $title, $category, $description, $requirements, $salary_range, $location, $job_type, $deadline, $job_id, $employer_id);
		return $stmt->execute();
	}

	function toggleStatus($job_id, $status, $employer_id) {
		$sql = "UPDATE jobs SET status = ? WHERE id = ? AND employer_id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('sii', $status, $job_id, $employer_id);
		return $stmt->execute();
	}

	function getJobDetails($job_id) {
		$sql = "SELECT j.*, u.company_name, u.company_industry, u.company_description, u.company_website, u.company_logo
				FROM jobs j
				JOIN users u ON j.employer_id = u.id
				WHERE j.id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $job_id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows > 0 ? $result->fetch_assoc() : null;
	}

	function getEmployerJobs($employer_id) {
		$sql = "SELECT j.id, j.title, j.category, j.deadline, j.status, COUNT(a.id) AS applicant_count
				FROM jobs j
				LEFT JOIN applications a ON j.id = a.job_id
				WHERE j.employer_id = ?
				GROUP BY j.id
				ORDER BY j.created_at DESC;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $employer_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$jobs = [];
		while ($row = $result->fetch_assoc()) {
			$jobs[] = $row;
		}
		return $jobs;
	}

	function getActiveJobs() {
		$sql = "SELECT j.*, u.company_name, u.company_logo
				FROM jobs j
				JOIN users u ON j.employer_id = u.id
				WHERE j.status = 'active'
				ORDER BY j.created_at DESC;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		$jobs = [];
		while ($row = $result->fetch_assoc()) {
			$jobs[] = $row;
		}
		return $jobs;
	}

	// ---- Admin: platform-wide job oversight ----

	// Browse every job across every employer, with optional category/status
	// filters. Unlike getEmployerJobs(), this is not scoped to one employer.
	function getAllJobsAdmin($category = null, $status = null) {
		$sql = "SELECT j.id, j.title, j.category, j.location, j.job_type, j.deadline, j.status, j.created_at,
					   u.company_name, u.id AS employer_id,
					   COUNT(a.id) AS applicant_count
				FROM jobs j
				JOIN users u ON j.employer_id = u.id
				LEFT JOIN applications a ON a.job_id = j.id
				WHERE 1=1";
		$types = '';
		$params = [];

		if (!empty($category)) {
			$sql .= " AND j.category = ?";
			$types .= 's';
			$params[] = $category;
		}
		if (!empty($status)) {
			$sql .= " AND j.status = ?";
			$types .= 's';
			$params[] = $status;
		}

		$sql .= " GROUP BY j.id ORDER BY j.created_at DESC;";

		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		if ($types !== '') {
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

	// Admin soft-delete / reinstate: close (or reopen) any listing regardless
	// of which employer owns it, e.g. for a policy violation. This is
	// intentionally not scoped by employer_id, unlike toggleStatus().
	function adminSetStatus($job_id, $status) {
		$sql = "UPDATE jobs SET status = ? WHERE id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('si', $status, $job_id);
		return $stmt->execute();
	}

	function countAllJobs() {
		$sql = "SELECT COUNT(*) AS total FROM jobs;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		return $result->fetch_assoc()['total'];
	}

	function countAllApplications() {
		$sql = "SELECT COUNT(*) AS total FROM applications;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		return $result->fetch_assoc()['total'];
	}

	function countActiveJobs() {
		$sql = "SELECT COUNT(*) AS total FROM jobs WHERE status = 'active';";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		return $result->fetch_assoc()['total'];
	}

	function countTotalEmployers() {
		$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'employer';";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		return $result->fetch_assoc()['total'];
	}

	function countTotalSeekers() {
		$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'seeker';";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		return $result->fetch_assoc()['total'];
	}
}
