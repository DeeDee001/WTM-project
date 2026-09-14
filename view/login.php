<?php
// Session is already started by index.php
// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['username'])) {
	header("Location: ../index.php");
	exit();
}

$requested_uri = $_SERVER['REQUEST_URI'] ?? '';
$base_path = str_contains($requested_uri, '/view/login.php') ? '..' : '.';
$login_action = $base_path === '..' ? '../controller/login-handler.php' : 'controller/login-handler.php';
$register_href = $base_path === '..' ? 'register.php' : 'view/register.php';
?>
<!DOCTYPE html>
<html lang='en'>

<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Login - CareerBridge</title>
	<link rel="stylesheet" href="<?php echo (strpos($_SERVER['REQUEST_URI'] ?? '', '/view/') !== false ? '../css/login.css' : 'css/login.css'); ?>">
</head>

<body>
	<div class="login-container">
		<h1>CareerBridge Login</h1>

		<?php if (isset($_SESSION['login_error'])) {
			echo "<div class='error'>" . htmlspecialchars($_SESSION['login_error']) . "</div>";
			unset($_SESSION['login_error']);
		} ?>

		<?php if (isset($_SESSION['registration_success'])) {
			echo "<div style='background: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;'>" . htmlspecialchars($_SESSION['registration_success']) . "</div>";
			unset($_SESSION['registration_success']);
		} ?>

		<form action="<?php echo htmlspecialchars($login_action); ?>" method="post">
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
			Don't have an account? <a href="<?php echo htmlspecialchars($register_href); ?>">Register here</a>
		</div>
	</div>
</body>

</html>