<?php

require_once 'functions.php';

if (!isLoggedIn()) {
	header('Location: /login.php');
	return;
}

$pdo = connect_to_psql('tmdatabase');



if(isset($_POST['newUser']))
{
	$password = $_POST['password'];
	$passwordHash = password_hash($password, PASSWORD_DEFAULT);

	$sql = "INSERT INTO tmpersonnels (personnel_first_name, personnel_last_name, personnel_username, personnel_password, personnel_is_admin) VALUES (:fName, :lName, :username, :pass, :admin)";
	$query = $GLOBALS['pdo']->prepare($sql);
	$query->bindValue(":fName", $_POST['firstName']);
	$query->bindValue(":lName", $_POST['lastName']);
	$query->bindValue(":username", $_POST['username']);
	$query->bindValue(":pass", $passwordHash);
	
	if(isset($_POST['isAdmin']) && $_POST['isAdmin'] == 'on')
	{
		$query->bindValue(":admin", 'TRUE');
	}
	else
	{
		$query->bindValue(":admin", 'FALSE');
	}
	$query->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Personnel | TubeMaster</title>

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<style type="text/css">
		th {
			text-align: center;
		}
		td {
			padding: 0.1em;
		}
	</style>
</head>
<body>
<div>
<div style='text-align:center'>
<form method='get' action='index.php'>
<button style='width:125px' type='submit' name='home'>Home</button>
</form>
</div>
</div>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>Personnel</h1>
				<table border="1" width="100%">
                    <tr>
                        <th>Personnel ID</th>
                        <th>First Name</th>
			<th>Last Name</th>
                        <th>Is an Admin</th>
			<th></th>
		</tr>

					<?php
					$stmt = $pdo->query('SELECT * FROM TMpersonnels');
					$personnel = $stmt->fetchAll();
					foreach ($personnel as $person) {
                        $id = $person['personnel_id'];
			$firstName = $person['personnel_first_name'];
			$lastName = $person['personnel_last_name'];
			$admin = $person['personnel_is_admin'];

    			echo '<form method="post" action="personnel_info.php">';
                        echo '<tr>';
			echo '<td>' . $id . '</td>';
			echo '<td>' . $firstName. '</td>';
			echo '<td>' . $lastName . '</td>';
			if($admin == 1)
			{
				echo '<td>Yes</td>';
			}
			else
			{
				echo '<td>No</td>';
			}
			echo '<td><input type="submit" value="View/Edit" name="submit"></td>';
			echo '<input type="hidden" name="id" value="'.$id.'">';
			echo '</tr>';
			echo '</form>';
			}
			?>
			</table>
			<hr>
			<form method="POST">
			<h2>Add New Personnel</h2>
			<div class="form-group">
			<table>
			<tr>
				<th><label for="username">Username:</label></th>
				<td><input type="text" name="username"></td>
			</tr>
			<tr>
				<th><label for="password">Password:</label></th>
				<td><input type="text" name="password"></td>
			</tr>
			<tr>
				<th><label for="firstName">First Name:</label></th>
				<td><input type="text" name="firstName"></td>
			</tr>
			<tr>
				<th><label for="lastName">Last Name:</label></th>
				<td><input type="text" name="lastName"></td>
			</tr>
			<tr>
				<th><label for="isAdmin">Is an Admin:</label></th>
				<td><input type="checkbox" name="isAdmin"></td>
			</tr>
			</table>
                    	</div>
			<div class="form-group">
				<input type="submit" name="newUser" value="Add New Personnel" class="form-control btn btn-block btn-success">
			</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>
