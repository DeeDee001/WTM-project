<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once __DIR__ . '/../model/User.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employer') {
	header("Location: login.php");
	exit();
}

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Company Profile - CareerBridge</title>
	<style>
		body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
		.container { max-width: 800px; margin: 30px auto; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 30px; }
		h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
		.form-group { margin-bottom: 20px; }
		label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
		input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
		textarea { resize: vertical; height: 100px; }
		.btn { padding: 12px 30px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
		.btn:hover { background: #5568d3; }
		.alert { padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
		.alert-success { background: #e8f5e9; color: #2e7d32; }
		.alert-error { background: #ffebee; color: #c62828; }
		.logo-preview { width: 120px; height: 120px; border-radius: 8px; object-fit: cover; border: 3px solid #667eea; background: #ddd; margin: 20px 0; }
		.nav-back { margin-bottom: 20px; }
		.nav-back a { color: #667eea; text-decoration: none; font-weight: bold; }
	</style>
</head>
<body>
	<?php include './layout/header.php'; ?>

	<div class="container">
		<div class="nav-back">
			<a href="../index.php">← Back to Dashboard</a>
		</div>

		<h1>Company Profile</h1>

		<?php if (isset($_SESSION['profile_success'])) { ?>
			<div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['profile_success']); unset($_SESSION['profile_success']); ?></div>
		<?php } ?>
		<?php if (isset($_SESSION['profile_error'])) { ?>
			<div class="alert alert-error"><?php echo htmlspecialchars($_SESSION['profile_error']); unset($_SESSION['profile_error']); ?></div>
		<?php } ?>

		<form action="../controller/profile-update-handler.php" method="post">
			<div class="form-group">
				<label for="company_name">Company Name *</label>
				<input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($userData['company_name'] ?? ''); ?>" required>
			</div>

			<div class="form-group">
				<label for="company_industry">Industry *</label>
				<input type="text" id="company_industry" name="company_industry" value="<?php echo htmlspecialchars($userData['company_industry'] ?? ''); ?>" placeholder="e.g. Software, Finance, Healthcare" required>
			</div>

			<div class="form-group">
				<label for="company_website">Website</label>
				<input type="text" id="company_website" name="company_website" value="<?php echo htmlspecialchars($userData['company_website'] ?? ''); ?>" placeholder="e.g. www.company.com">
			</div>

			<div class="form-group">
				<label for="company_description">Company Description</label>
				<textarea id="company_description" name="company_description" placeholder="Tell job seekers about your company..."><?php echo htmlspecialchars($userData['company_description'] ?? ''); ?></textarea>
			</div>

			<div class="form-group">
				<label for="email">Contact Email *</label>
				<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
			</div>

			<button type="submit" class="btn">Save Company Profile</button>
		</form>

		<h3 style="margin-top: 40px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Company Logo</h3>
		<?php
		$logo_src = !empty($userData['company_logo']) ? '../'.$userData['company_logo'] : 'https://via.placeholder.com/120?text=Logo';
		?>
		<img class="logo-preview" src="<?php echo $logo_src; ?>" alt="Company Logo">

		<form action="../controller/profile-pic-upload-handler.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="upload_type" value="logo">
			<div class="form-group">
				<label for="company_logo">Upload New Logo (JPG, PNG)</label>
				<input type="file" name="company_logo" id="company_logo" accept="image/*" required>
			</div>
			<button type="submit" class="btn">Upload Logo</button>
		</form>
	</div>
</body>
</html>
