<?php
require_once __DIR__ . '/../db/db_connection.php';

class Application {
	// Matches the `status` ENUM on the applications table (see
	// db/setup_seeker_tables.sql). Kept here so the recruiter controllers
	// have one place to validate against instead of trusting client input.
	private $allowed_statuses = ['submitted', 'reviewed', 'shortlisted', 'rejected'];

	function establishConnection() {
		$db_connection = new DBConnection();
		$connection = $db_connection->connect();
		return $connection;
	}

	function isValidStatus($status) {
		return in_array($status, $this->allowed_statuses, true);
	}

	// Every job on the platform, for the recruiter's job-picker dropdown.
	// A recruiter isn't tied to a single employer, so this spans all of them
	// (unlike Job::getEmployerJobs(), which is scoped to one employer_id).
	function getAllJobsForReview() {
		$sql = "SELECT j.id, j.title, u.company_name
				FROM jobs j
				JOIN users u ON j.employer_id = u.id
				ORDER BY j.created_at DESC;";
		$connection = $this->establishConnection();
		$result = $connection->query($sql);
		$jobs = [];
		while ($row = $result->fetch_assoc()) {
			$jobs[] = $row;
		}
		return $jobs;
	}

	// Title + company for the currently selected job, so the review page can
	// show what's being reviewed without re-querying getAllJobsForReview().
	function getJobTitle($job_id) {
		$sql = "SELECT j.id, j.title, u.company_name
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

	// Every application for one job: seeker identity, cover letter, resume.
	function getApplicationsForJob($job_id) {
		$sql = "SELECT a.id, a.status, a.cover_letter, a.resume_path, a.created_at,
					   u.id AS seeker_id, u.username, u.headline
				FROM applications a
				JOIN users u ON a.seeker_id = u.id
				WHERE a.job_id = ?
				ORDER BY a.created_at DESC;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $job_id);
		$stmt->execute();
		$result = $stmt->get_result();
		$applications = [];
		while ($row = $result->fetch_assoc()) {
			$applications[] = $row;
		}
		return $applications;
	}

	// AJAX status update (dropdown -> PUT call on the controller).
	function updateStatus($application_id, $status) {
		if (!$this->isValidStatus($status)) {
			return false;
		}
		$sql = "UPDATE applications SET status = ? WHERE id = ?;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('si', $status, $application_id);
		return $stmt->execute();
	}

	// Count of applications per status for one job, always returning every
	// status key (0 if unused) so the funnel chart has the same 4 bars no
	// matter which job is selected.
	function getStatusCountsForJob($job_id) {
		$counts = ['submitted' => 0, 'reviewed' => 0, 'shortlisted' => 0, 'rejected' => 0];
		$sql = "SELECT status, COUNT(*) AS total FROM applications WHERE job_id = ? GROUP BY status;";
		$connection = $this->establishConnection();
		$stmt = $connection->prepare($sql);
		$stmt->bind_param('i', $job_id);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
			$counts[$row['status']] = (int) $row['total'];
		}
		return $counts;
	}
}
