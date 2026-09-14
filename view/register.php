<?php
// This page can be opened directly (not only through index.php), so make sure
// the session is started here too.
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// If already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
	header("Location: ../index.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Register - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/register.css' : 'css/register.css'); ?>">
</head>
<body>
	<div class="register-container">
		<h1>Create Account</h1>

		<?php if(isset($_SESSION['register_error'])) {
			echo "<div class='error'>" . htmlspecialchars($_SESSION['register_error']) . "</div>";
			unset($_SESSION['register_error']);
		} ?>

		<form action="../controller/register-handler.php" method="post">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" id="username" name="username" required>
			</div>

			<div class="form-group">
				<label for="email">Email</label>
				<input type="email" id="email" name="email" required>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" required>
			</div>

			<div class="form-group">
				<label for="role">Register as</label>
				<select id="role" name="role" required>
					<option value="">-- Select Role --</option>
					<option value="employer">Employer (Post Jobs)</option>
					<option value="seeker">Job Seeker (Apply for Jobs)</option>
					<option value="recruiter">Recruiter / HR Reviewer</option>
					<option value="admin">Admin (Platform Management)</option>
				</select>
			</div>

			<button type="submit" class="btn">Register</button>
		</form>

		<div class="login-link">
			Already have an account? <a href="../index.php">Login here</a>
		</div>
	</div>
</body>
</html>
