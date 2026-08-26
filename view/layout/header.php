<?php
// $nav_base tells this shared header where it's being rendered from, so its
// links resolve correctly either way:
//   - '' (default) when included from a page that lives directly in view/
//     (e.g. view/profile.php, view/create-job.php)
//   - 'view/' when included from a page rendered at the project root via
//     index.php's require (e.g. the dashboards)
// Every including page should set $nav_base BEFORE including this file if it
// falls into the second case.
$nav_base = $nav_base ?? '';
$root_base = ($nav_base === 'view/') ? '' : '../';
?>
<header style="background: #667eea; color: white; padding: 15px 30px;">
	<h2 style="margin: 0;">CareerBridge</h2>
</header>
<nav style="background: #5568d3; padding: 10px 30px;">
	<?php
	// Check if user is logged in
	if (isset($_SESSION['username'])) {
		$role = $_SESSION['role'];
		echo '<a href="' . $root_base . 'index.php" style="color: white; margin-right: 15px; text-decoration: none;">Dashboard</a>';

		if ($role == 'employer') {
			// The dashboard itself is the job list, so "My Jobs" points there too.
			echo '<a href="' . $root_base . 'index.php" style="color: white; margin-right: 15px; text-decoration: none;">My Jobs</a>';
			echo '<a href="' . $nav_base . 'create-job.php" style="color: white; margin-right: 15px; text-decoration: none;">Post Job</a>';
			echo '<a href="' . $nav_base . 'company-profile.php" style="color: white; margin-right: 15px; text-decoration: none;">Company Profile</a>';
		} else if ($role == 'seeker') {
			echo '<a href="' . $nav_base . 'job-search.php" style="color: white; margin-right: 15px; text-decoration: none;">Search Jobs</a>';
			echo '<a href="' . $nav_base . 'saved-jobs.php" style="color: white; margin-right: 15px; text-decoration: none;">Saved Jobs</a>';
			echo '<a href="' . $nav_base . 'my-applications.php" style="color: white; margin-right: 15px; text-decoration: none;">My Applications</a>';
		} else if ($role == 'recruiter') {
			echo '<a href="' . $nav_base . 'review-applications.php" style="color: white; margin-right: 15px; text-decoration: none;">Review Applications</a>';
			echo '<a href="' . $nav_base . 'analytics.php" style="color: white; margin-right: 15px; text-decoration: none;">Analytics</a>';
		} else if ($role == 'admin') {
			echo '<a href="' . $nav_base . 'manage-categories.php" style="color: white; margin-right: 15px; text-decoration: none;">Manage Categories</a>';
			echo '<a href="' . $nav_base . 'manage-jobs.php" style="color: white; margin-right: 15px; text-decoration: none;">Manage Jobs</a>';
			echo '<a href="' . $nav_base . 'platform-analytics.php" style="color: white; margin-right: 15px; text-decoration: none;">Platform Analytics</a>';
		}

		echo '<a href="' . $nav_base . 'profile.php" style="color: white; margin-right: 15px; text-decoration: none;">Profile</a>';
		echo '<a href="' . $root_base . 'controller/logout-handler.php" style="color: white; text-decoration: none;">Logout</a>';
	} else {
		echo '<a href="' . $nav_base . 'login.php" style="color: white; margin-right: 15px; text-decoration: none;">Login</a>';
		echo '<a href="' . $nav_base . 'register.php" style="color: white; text-decoration: none;">Register</a>';
	}
	?>
</nav>