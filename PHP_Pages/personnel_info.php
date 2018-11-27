<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
$pdo = connect_to_psql('tmdatabase');

if(isset($_POST['save']))
{
	$sql = 'UPDATE TMPersonnels SET personnel_username = :username, personnel_password=:password, personnel_first_name = :fName, personnel_last_name = :lName, personnel_is_admin = :isAdmin WHERE personnel_id = :id';
	$query = $GLOBALS['pdo']->prepare($sql);
	$query->bindValue(":id", $_POST['id']);
	$query->bindValue(":username", $_POST['username']);
	$query->bindValue(":password", $_POST['password']);
	$query->bindValue(":fName", $_POST['firstName']);
	$query->bindValue(":lName", $_POST['lastName']);
	if(isset($_POST['isAdmin']) && $_POST['isAdmin']=="on")
	{
		$query->bindValue("isAdmin", "TRUE");
	}
	else
	{
		$query->bindValue("isAdmin", "FALSE");
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
<div style='float:left'>
<form method='get' action='personnel.php'>
<button  type='submit' name='home'>Go to Personnel</button>
</form>
</div>

<div style='text-align:center'>
<form method='get' action='home.php'>
<button style='width:125px' type='submit' name='home'>Home</button>
</form>
</div>
</div>


	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>Personnel</h1>
	
		<div class="form">
		<form method="post">
		<table>	
		<?php
		$stmt = $pdo->query('SELECT * FROM TMpersonnels WHERE personnel_id=' . $_POST['id']);
$personnel = $stmt->fetchAll();
echo '<input type="hidden" name="id" value="'.$_POST['id'].'">';
		foreach ($personnel as $person) {			
			echo '<tr>';
			echo '<th><label for="username">Username:</label></th>';
			echo '<td><input type="text" name="username" value="' . $person['personnel_username'] . '"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th><label for="password">Password:</label></th>';
			echo '	<td><input type="text" name="password" value="'.$person['personnel_password'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th><label for="firstName">First Name:</label></th>';
			echo '	<td><input type="text" name="firstName" value="'.$person['personnel_first_name'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th><label for="lastName">Last Name:</label></th>';
			echo '	<td><input type="text" name="lastName" value="'.$person['personnel_last_name'].'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '	<th><label for="isAdmin">Is an Admin:</label></th>';
			if(isset($person['personnel_is_admin']) && $person['personnel_is_admin'] == 1)
			{
				echo '	<td><input type="checkbox" name="isAdmin" checked></td>';
			}
			else
			{
				echo '	<td><input type="checkbox" name="isAdmin"></td>';
			}
			echo '</tr>';
		}
		?>		
		</table>
		<input type="submit" name="save" value="Save">
		</form>
		<br />
	</div>

			<table border="1" width="100%">
                    <tr>
                        <th>Repair ID</th>
                        <th>Equipment ID</th>
			<th>Incident</th>
			<th></th>
		</tr>
		 
		<?php
		$sql = "SELECT * FROM repairs WHERE personnel_id = :id";
		$query = $GLOBALS['pdo']->prepare($sql);
		$query->bindValue(":id", $_POST['id']);
		$query->execute();	
		foreach ($query as $repair) {
                        $id = $repair['repair_id'];
			$equipment = $repair['equipment_id'];
			$incident = $repair['incident_occured'];

    			echo '<form method="post" action="repair_info.php">';
                        echo '<tr>';
			echo '<td>' . $id . '</td>';
			echo '<td>' . $equipment. '</td>';
			echo '<td>' . $incident . '</td>';	
			echo '<td><input type="submit" value="View/Edit" name="loadRepair"></td>';
			echo '<input type="hidden" name="repair" value="'.$id.'">';
			echo '</tr>';
			echo '</form>';
		}
			?>
		</div>
	</div>
</div>
</body>
</html>
