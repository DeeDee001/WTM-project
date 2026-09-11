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
}
