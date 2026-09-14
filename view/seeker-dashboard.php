<?php
// Session already started by index.php
require_once __DIR__ . '/../model/JobSeeker.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'seeker') {
    header("Location: view/login.php");
    exit();
}

$jobSeeker = new JobSeeker();
$user_id = $_SESSION['user_id'];

// Get stats
$applications = $jobSeeker->getSeekerApplications($user_id);
$savedJobs = $jobSeeker->getSavedJobs($user_id);

$total_applications = count($applications);
$total_saved = count($savedJobs);
$pending_count = count(array_filter($applications, fn($a) => $a['status'] == 'submitted'));
$reviewed_count = count(array_filter($applications, fn($a) => $a['status'] == 'reviewed'));
$shortlisted_count = count(array_filter($applications, fn($a) => $a['status'] == 'shortlisted'));
$rejected_count = count(array_filter($applications, fn($a) => $a['status'] == 'rejected'));
?>
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Job Seeker Dashboard - CareerBridge</title>

    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/seeker-dashboard.css' : 'css/seeker-dashboard.css'); ?>">
</head>

<body>
    <?php $nav_base = 'view/';
    include __DIR__ . '/layout/header.php'; ?>

    <div class="container">
        <h1>Job Seeker Dashboard</h1>

        <div class="welcome">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
            <p>Find your dream job and track your applications all in one place.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_applications; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-number"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-number"><?php echo $reviewed_count; ?></div>
                <div class="stat-label">Reviewed</div>
            </div>
            <div class="stat-card green">
                <div class="stat-number"><?php echo $shortlisted_count; ?></div>
                <div class="stat-label">Shortlisted</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
                <div class="stat-number"><?php echo $rejected_count; ?></div>
                <div class="stat-label">Rejected</div>
            </div>
            <div class="stat-card blue">
                <div class="stat-number"><?php echo $total_saved; ?></div>
                <div class="stat-label">Saved Jobs</div>
            </div>
        </div>

        <h3 class="section-title">Quick Actions</h3>

        <div class="quick-actions">
            <div class="action-card" onclick="window.location.href='view/search-jobs.php'">
                <div class="action-icon">🔍</div>
                <div class="action-title">Search Jobs</div>
                <div class="action-desc">Browse and filter active job listings from top companies</div>
                <a href="view/search-jobs.php" class="btn-action">Start Searching</a>
            </div>

            <div class="action-card" onclick="window.location.href='view/my-applications.php'">
                <div class="action-icon">📋</div>
                <div class="action-title">My Applications</div>
                <div class="action-desc">Track the status of all your job applications</div>
                <a href="view/my-applications.php" class="btn-action">View Applications</a>
            </div>

            <div class="action-card" onclick="window.location.href='view/saved-jobs.php'">
                <div class="action-icon">💼</div>
                <div class="action-title">Saved Jobs</div>
                <div class="action-desc">Review jobs you've bookmarked for later</div>
                <a href="view/saved-jobs.php" class="btn-action">View Saved Jobs</a>
            </div>

            <div class="action-card" onclick="window.location.href='view/profile.php'">
                <div class="action-icon">👤</div>
                <div class="action-title">Update Profile</div>
                <div class="action-desc">Keep your profile and resume up to date</div>
                <a href="view/profile.php" class="btn-action">Edit Profile</a>
            </div>
        </div>

        <?php if (count($applications) > 0) {
            $recent_apps = array_slice($applications, 0, 3);
            ?>
            <div class="recent-section">
                <h3 class="section-title">Recent Applications</h3>
                <?php foreach ($recent_apps as $app) { ?>
                    <div
                        style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong><?php echo htmlspecialchars($app['job_title']); ?></strong>
                            <div style="color: #666; font-size: 14px; margin-top: 5px;">
                                <?php echo htmlspecialchars($app['company_name']); ?> • Applied
                                <?php echo date('M d, Y', strtotime($app['created_at'])); ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold;
background: <?php
                echo $app['status'] == 'submitted' ? '#e3f2fd' : ($app['status'] == 'shortlisted' ? '#e8f5e9' : ($app['status'] == 'reviewed' ? '#fff3e0' : '#ffebee'));
                ?>;
color: <?php
                echo $app['status'] == 'submitted' ? '#1976d2' : ($app['status'] == 'shortlisted' ? '#388e3c' : ($app['status'] == 'reviewed' ? '#f57c00' : '#d32f2f'));
                ?>;">
                                <?php echo ucfirst($app['status']); ?>
                            </span>
                            <?php
                            $updatedAt = $app['updated_at'] ?? null;
                            if (!empty($updatedAt) && $updatedAt != ($app['created_at'] ?? null)) {
                            ?>
                                <div style="font-size: 12px; color: #666; margin-top: 6px;">
                                    Updated <?php echo date('M d, Y', strtotime($updatedAt)); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>

</html>