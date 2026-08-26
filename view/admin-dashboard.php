<?php
// Session already started by index.php
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
	header("Location: view/login.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1'>
	<title>Admin Dashboard - CareerBridge</title>
	<style>
		body { font-family: Arial, sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
		.container { max-width: 1200px; margin: 30px auto; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 30px; }
		h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
		.welcome { background: #e8eaf6; padding: 20px; border-radius: 5px; margin-bottom: 25px; }
	</style>
</head>
<body>
	<div style="background: #667eea; color: white; padding: 15px 30px;">
		<h2 style="margin: 0; display: inline;">CareerBridge - Admin</h2>
		<div style="float: right;">
			<a href="index.php" style="color: white; margin-right: 15px; text-decoration: none;">Dashboard</a>
			<a href="view/profile.php" style="color: white; margin-right: 15px; text-decoration: none;">Profile</a>
			<a href="controller/logout-handler.php" style="color: white; text-decoration: none;">Logout</a>
		</div>
		<div style="clear: both;"></div>
	</div>
	<div class="container">
		<h1>Admin Dashboard</h1>
		<div class="welcome">
			<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
			<p>This dashboard is under development by your teammates.</p>
		</div>
	</div>
</body>
</html>
