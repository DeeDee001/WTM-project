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
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		body {
			font-family: Arial, sans-serif;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 20px;
		}
		.register-container {
			background: white;
			padding: 40px;
			border-radius: 10px;
			box-shadow: 0 10px 25px rgba(0,0,0,0.2);
			width: 450px;
			max-width: 90%;
		}
		h1 {
			text-align: center;
			color: #333;
			margin-bottom: 30px;
		}
		.form-group {
			margin-bottom: 20px;
		}
		label {
			display: block;
			margin-bottom: 5px;
			color: #555;
			font-weight: bold;
		}
		input[type="text"],
		input[type="email"],
		input[type="password"],
		select {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			font-size: 14px;
		}
		input:focus,
		select:focus {
			outline: none;
			border-color: #667eea;
		}
		.btn {
			width: 100%;
			padding: 12px;
			background: #667eea;
			color: white;
			border: none;
			border-radius: 5px;
			font-size: 16px;
			cursor: pointer;
			transition: background 0.3s;
		}
		.btn:hover {
			background: #5568d3;
		}
		.error {
			background: #ffebee;
			color: #c62828;
			padding: 10px;
			border-radius: 5px;
			margin-bottom: 15px;
			text-align: center;
		}
		.login-link {
			text-align: center;
			margin-top: 20px;
			color: #666;
		}
		.login-link a {
			color: #667eea;
			text-decoration: none;
			font-weight: bold;
		}
		.login-link a:hover {
			text-decoration: underline;
		}
	</style>
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
