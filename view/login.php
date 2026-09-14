<?php
// Session is already started by index.php
// If already logged in, redirect to appropriate dashboard
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
	<title>Login - CareerBridge</title>
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
		}
		.login-container {
			background: white;
			padding: 40px;
			border-radius: 10px;
			box-shadow: 0 10px 25px rgba(0,0,0,0.2);
			width: 400px;
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
		input[type="password"] {
			width: 100%;
			padding: 12px;
			border: 1px solid #ddd;
			border-radius: 5px;
			font-size: 14px;
		}
		input[type="text"]:focus,
		input[type="password"]:focus {
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
		.register-link {
			text-align: center;
			margin-top: 20px;
			color: #666;
		}
		.register-link a {
			color: #667eea;
			text-decoration: none;
			font-weight: bold;
		}
		.register-link a:hover {
			text-decoration: underline;
		}
	</style>
</head>
<body>
	<div class="login-container">
		<h1>CareerBridge Login</h1>

		<?php if(isset($_SESSION['login_error'])) {
			echo "<div class='error'>" . htmlspecialchars($_SESSION['login_error']) . "</div>";
			unset($_SESSION['login_error']);
		} ?>

		<?php if(isset($_SESSION['registration_success'])) {
			echo "<div style='background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;'>" . htmlspecialchars($_SESSION['registration_success']) . "</div>";
			unset($_SESSION['registration_success']);
		} ?>

		<form action="controller/login-handler.php" method="post">
			<div class="form-group">
				<label for="username">Username</label>
				<input type="text" id="username" name="username" required>
			</div>

			<div class="form-group">
				<label for="password">Password</label>
				<input type="password" id="password" name="password" required>
			</div>

			<button type="submit" class="btn">Login</button>
		</form>

		<div class="register-link">
			Don't have an account? <a href="view/register.php">Register here</a>
		</div>
	</div>
</body>
</html>
