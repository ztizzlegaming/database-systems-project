<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';

if (!isLoggedIn()) {
	header('Location: /login.php');
	return;
}

$pdo = connect_to_psql('tmdatabase');

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
	<div class="form">
		<form method="post">
		<table>	
		<?php
		/*$stmt = $pdo->query('SELECT * FROM TMpersonnels WHERE personnel_id='$_POST['id']);
		$personnel = $stmt->fetchAll();
		foreach ($personnel as $person) {			echo '<tr>';
			echo '<th><label for="username">Username:</label></th>';
			echo '<td><input type="text" name="username" value="' . $person['personnel_username'] . '"></td>';
			<echo '/tr>';
			<echo 'tr>';
			echo '	<th><label for="password">Password:</label></th>';
			echo '	<td><input type="text" name="password" value="'.$person['personnel_password'].'"></td>';
			<echo '/tr>';
			<echo 'tr>';
			echo '	<th><label for="firstName">First Name:</label></th>';
			echo '	<td><input type="text" name="firstName"> value="'.$person['personnel_first_name'].'"</td>';
			<echo '/tr>';
			<echo 'tr>';
			echo '	<th><label for="lastName">Last Name:</label></th>';
			echo '	<td><input type="text" name="lastName"> value="'.$person['personnel_last_name'].'"</td>';
			<echo '/tr>';
			<echo 'tr>';
			echo '	<th><label for="isAdmin">Is an Admin:</label></th>';
			if(isset($person['personnel_is_admin']) && $person['personnel_is_admin'] == 1)
			{
				echo '	<td><input type="checkbox" name="isAdmin" checked></td>';
			}
			else
			{
				echo '	<td><input type="checkbox" name="isAdmin"></td>';
			}
			<echo '/tr>';*/
		}
		?>		
		</table>
		<input type="submit" name="save" value="Save">
		</form>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>Personnel</h1>
				<table border="1" width="100%">
                    <tr>
                        <th>Repair ID</th>
                        <th>Equipment ID</th>
			<th>Incident</th>
			<th></th>
		</tr>
		 
		<?php
		/*$sql = "SELECT * FROM repairs WHERE personnel_id = :id";
		$query = $GLOBALS['pdo']->prepare($sql);
		$query->bindValue(":id", $_POST['id']);
		$query->execute();
		$repairs = $stmt->fetchAll();
		foreach ($repairs as $repair) {
                        $id = $repair['repair_id'];
			$equipment = $person['equipment_id'];
			$incident = $person['incident_occured'];

    			echo '<form method="post" action="repair_info.php">';
                        echo '<tr>';
			echo '<td>' . $id . '</td>';
			echo '<td>' . $equipment. '</td>';
			echo '<td>' . $incident . '</td>';	
			echo '<td><input type="submit" value="View/Edit" name="loadRepair"></td>';
			echo '<input type="hidden" name="repair" value="'.$id.'">';
			echo '</tr>';
			echo '</form>';
		}*/
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
