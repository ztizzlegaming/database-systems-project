<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';

if (!isLoggedIn()) {
	header('Location: /login.php');
	return;
}

$db = connect_to_psql('tmdatabase');
?>

<!DOCTYPE html>

<html>
<head>
	<title>Packing Lists | TubeMaster</title>

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
				<h1>Packing Lists</h1>
				<table border="1" width="100%">
                    <tr>
                        <th>Project ID</th>
                        <th>Revision Number</th>
						<th>Client Name</th>
                        <th>Reactor Name</th>
                        <th>Start Date</th>
						<th></th>
					</tr>

					<?php
					$stmt = $db->query('SELECT * FROM packing_lists');
					$packing_lists = $stmt->fetchAll();
					foreach ($packing_lists as $list) {
                        $project_id = $list['project_id'];
                        $revision_num = $list['revision'];
						$client_name = $list['client_company_name'];
                        $reactor_name = $list['reactor_name'];
                        $start_date = $list['project_start_date'];
    			echo '<form method="post" action="packing_list_info.php">';
                        echo '<tr>';
						echo '<td>' . $project_id . '</td>';
						echo '<td>' . $revision_num . '</td>';
						echo '<td>' . $client_name . '</td>';
                        echo '<td>' . $reactor_name . '</td>';
                        echo '<td>' . $start_date . '</td>';
			echo '<td><input type="submit" value="View/Edit" name="submit"></td>';
			echo '<input type="hidden" name="project_id" value="'.$project_id.'">';
			echo '</tr>';
			echo '</form>';
					}
					?>
				</table>
				<hr>
				<form method="POST" action="packing_list_info.php">
					<h2>Add New Packing List</h2>
					<div class="form-group">
                        <label for="project">Project</label>
                        <?php
                            echo '<br>';
                            echo '<select name="project_id">';
                            $stmt = $db->query('SELECT project_id, client_company_name, reactor_name, project_start_date FROM projects NATURAL JOIN reactors NATURAL JOIN units NATURAL JOIN plants NATURAL JOIN clients WHERE project_is_active = TRUE');
					        $projects = $stmt->fetchAll();
                            foreach ($projects as $project) {
                                echo '<option value="' . $project['project_id'] . '">' . $project['client_company_name'] . ' - ' . $project['reactor_name']  . ' - ' . $project['project_start_date'];
                            }
                        ?>
						<input type="hidden" name="company_name" class="form-control" id="company_name" placeholder="abcabcabc">
                    </div>
					<div class="form-group">
						<input type="submit" name="submit" value="Add New Packing List" class="form-control btn btn-block btn-success">
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
