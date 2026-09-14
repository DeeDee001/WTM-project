<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/User.php';

// If not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

if (!$userData) {
	echo "User data not found.";
	exit();
}

$role = $userData['role'];
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Profile Settings - CareerBridge</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background: #f4f7f6;
			margin: 0;
			padding: 0;
		}
		.container {
			max-width: 900px;
			margin: 30px auto;
			background: white;
			border-radius: 8px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			padding: 30px;
		}
		h1 {
			margin-bottom: 20px;
			color: #333;
			border-bottom: 2px solid #667eea;
			padding-bottom: 10px;
		}
		h3 {
			margin-top: 30px;
			margin-bottom: 15px;
			color: #444;
			border-bottom: 1px solid #ddd;
			padding-bottom: 5px;
		}
		.profile-pic-container {
			display: flex;
			align-items: center;
			gap: 20px;
			margin-bottom: 25px;
		}
		.profile-pic, .company-logo {
			width: 120px;
			height: 120px;
			border-radius: 50%;
			object-fit: cover;
			border: 3px solid #667eea;
			background: #ddd;
		}
		.company-logo {
			border-radius: 8px;
		}
		.form-group {
			margin-bottom: 15px;
		}
		label {
			display: block;
			font-weight: bold;
			margin-bottom: 5px;
			color: #555;
		}
		input[type="text"],
		input[type="email"],
		input[type="password"],
		textarea {
			width: 100%;
			padding: 10px;
			border: 1px solid #ccc;
			border-radius: 4px;
			font-size: 14px;
		}
		textarea {
			resize: vertical;
			height: 100px;
		}
		input:focus, textarea:focus {
			border-color: #667eea;
			outline: none;
		}
		.btn {
			padding: 10px 20px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 4px;
			font-size: 14px;
			cursor: pointer;
			transition: background 0.2s;
		}
		.btn:hover {
			background: #5568d3;
		}
		.danger-btn {
			background: #e53935;
		}
		.danger-btn:hover {
			background: #d32f2f;
		}
		.alert {
			padding: 12px;
			margin-bottom: 20px;
			border-radius: 4px;
			text-align: center;
		}
		.alert-success {
			background: #e8f5e9;
			color: #2e7d32;
		}
		.alert-error {
			background: #ffebee;
			color: #c62828;
		}
		.nav-back {
			margin-bottom: 20px;
		}
		.nav-back a {
			color: #667eea;
			text-decoration: none;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Account Settings</h1>

		<?php if (isset($_SESSION['profile_success'])) { ?>
			<div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['profile_success']); unset($_SESSION['profile_success']); ?></div>
		<?php } ?>
		<?php if (isset($_SESSION['profile_error'])) { ?>
			<div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['profile_error']); unset($_SESSION['profile_error']); ?></div>
		<?php } ?>

		<div class="profile-pic-container">
			<?php
			$pic_src = !empty($userData['profile_pic']) ? '../'.$userData['profile_pic'] : 'https://via.placeholder.com/120';
			?>
			<img class="profile-pic" src="<?php echo $pic_src; ?>" alt="Profile Picture">

			<form action="../controller/profile-pic-upload-handler.php" method="post" enctype="multipart/form-data">
				<label for="profile_pic">Change Profile Picture</label>
				<input type="file" name="profile_pic" id="profile_pic" accept="image/*" required style="margin-bottom: 10px;"><br>
				<button type="submit" class="btn">Upload Photo</button>
			</form>
		</div>

		<?php if ($role == 'employer') { ?>
			<div class="profile-pic-container">
				<?php
				$logo_src = !empty($userData['company_logo']) ? '../'.$userData['company_logo'] : 'https://via.placeholder.com/120?text=Logo';
				?>
				<img class="company-logo" src="<?php echo $logo_src; ?>" alt="Company Logo">

				<form action="../controller/profile-pic-upload-handler.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="upload_type" value="logo">
					<label for="company_logo">Change Company Logo</label>
					<input type="file" name="company_logo" id="company_logo" accept="image/*" required style="margin-bottom: 10px;"><br>
					<button type="submit" class="btn">Upload Logo</button>
				</form>
			</div>
		<?php } ?>

		<form action="../controller/profile-update-handler.php" method="post">
			<h3>Basic Details</h3>

			<div class="form-group">
				<label>Username</label>
				<input type="text" value="<?php echo htmlspecialchars($userData['username']); ?>" disabled>
			</div>

			<div class="form-group">
				<label for="email">Email Address</label>
				<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
			</div>

			<?php if ($role == 'employer') { ?>
				<h3>Company Information</h3>

				<div class="form-group">
					<label for="company_name">Company Name</label>
					<input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($userData['company_name'] ?? ''); ?>">
				</div>

				<div class="form-group">
					<label for="company_industry">Industry</label>
					<input type="text" id="company_industry" name="company_industry" value="<?php echo htmlspecialchars($userData['company_industry'] ?? ''); ?>">
				</div>

				<div class="form-group">
					<label for="company_website">Website</label>
					<input type="text" id="company_website" name="company_website" value="<?php echo htmlspecialchars($userData['company_website'] ?? ''); ?>">
				</div>

				<div class="form-group">
					<label for="company_description">Description</label>
					<textarea id="company_description" name="company_description"><?php echo htmlspecialchars($userData['company_description'] ?? ''); ?></textarea>
				</div>
			<?php } else if ($role == 'seeker') { ?>
				<h3>Job Seeker Information</h3>

				<div class="form-group">
					<label for="headline">Professional Headline</label>
					<input type="text" id="headline" name="headline" value="<?php echo htmlspecialchars($userData['headline'] ?? ''); ?>">
				</div>

				<div class="form-group">
					<label>Current Resume</label>
					<?php if(!empty($userData['resume_path'])) { ?>
						<p>📄 <a href="../<?php echo $userData['resume_path']; ?>" target="_blank">View Resume</a></p>
					<?php } else { ?>
						<p style="color: #c62828;">No resume uploaded</p>
					<?php } ?>
				</div>
			<?php } ?>

			<button type="submit" class="btn">Save Profile</button>
		</form>

		<?php if ($role == 'seeker') { ?>
			<form action="../controller/profile-pic-upload-handler.php" method="post" enctype="multipart/form-data" style="margin-top: 20px;">
				<input type="hidden" name="upload_type" value="resume">
				<div class="form-group">
					<label for="resume_file">Upload Resume (PDF)</label>
					<input type="file" name="resume_file" id="resume_file" accept=".pdf" required>
				</div>
				<button type="submit" class="btn">Upload Resume</button>
			</form>
		<?php } ?>

		<form action="../controller/password-change-handler.php" method="post">
			<h3>Change Password</h3>

			<div class="form-group">
				<label for="current_password">Current Password</label>
				<input type="password" id="current_password" name="current_password" required>
			</div>

			<div class="form-group">
				<label for="new_password">New Password</label>
				<input type="password" id="new_password" name="new_password" required>
			</div>

			<button type="submit" class="btn">Update Password</button>
		</form>

		<div style="margin-top: 40px; border-top: 2px solid #ffebee; padding-top: 20px;">
			<h3>Danger Zone</h3>
			<p style="color: #666;">Deleting your account is permanent and cannot be undone.</p>

			<form action="../controller/profile-update-handler.php" method="post" onsubmit="return confirm('Delete account permanently?');">
				<input type="hidden" name="action_type" value="delete_account">
				<button type="submit" class="btn danger-btn">Delete Account</button>
			</form>
		</div>

	</div>
</body>
</html>
