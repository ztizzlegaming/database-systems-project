<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
$db = connect_to_psql('tmdatabase');

if (isLoggedIn()) {
	header('Location: /');
}

if (isset($_POST['submit'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];

	$stmt = $db->prepare('SELECT personnel_password FROM TMpersonnels WHERE personnel_username = ?');
	$stmt->execute([$username]);
	$row = $stmt->fetch();
	$passwordHash = $row['personnel_password'];

	if (password_verify($password, $passwordHash)) {
		$_SESSION['username'] = $username;
		header('Location: /');
	} else {
		echo '<div class="error">The username or password was incorrect</div>';
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login | TubeMaster</title>

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<style type="text/css">
		.error {
			color: red;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="jumbotron">
					<form method="post">
						<div class="form-group">
							<label for="username">Username</label>
							<input type="text" name="username" class="form-control" id="username">
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" name="password" class="form-control" id="password">
						</div>
						<div class="form-group">
							<input type="submit" name="submit" value="Login" class="btn btn-block btn-success">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>