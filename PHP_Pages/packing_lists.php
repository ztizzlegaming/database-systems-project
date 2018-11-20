<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
$db = connect_to_psql('tmdatabase');

if (isset($_POST['submit'])) {
	# Get all post variables and insert into clients
	$companyName = $_POST['company_name'];
	$contactFirstName = $_POST['contact_first_name'];
	$contactLastName = $_POST['contact_last_name'];
	$contactPhoneNumber = $_POST['contact_phone'];
	$contactEmail = $_POST['contact_email'];
	$streetAddress = $_POST['street_address'];
	$city = $_POST['city'];
	$country = $_POST['country'];
	$zip = $_POST['zip'];

	$sql = '
		INSERT INTO clients
		(client_company_name,
		 client_contact_first_name,
		 client_contact_last_name,
		 client_contact_phone_number,
		 client_contact_email,
		 client_street_address,
		 client_city,
		 client_country,
		 client_zip_code)
		VALUES
		(?, ?, ?, ?, ?, ?, ?, ?, ?)
	';
	$stmt = $db->prepare($sql);
	$data = [
		$companyName,
		$contactFirstName,
		$contactLastName,
		$contactPhoneNumber,
		$contactEmail,
		$streetAddress,
		$city,
		$country,
		$zip
	];
	$stmt->execute($data);
}
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
    
                        echo '<tr>';
						echo '<td>' . $project_id . '</td>';
						echo '<td>' . $revision_num . '</td>';
						echo '<td>' . $client_name . '</td>';
                        echo '<td>' . $reactor_name . '</td>';
                        echo '<td>' . $start_date . '</td>';
						echo '<td><a href="packing_list_info.php?projectID=' . $project_id . '">View/Edit</a></td>';
						echo '</tr>';
					}
					?>
				</table>
				<hr>
				<form method="POST">
					<h2>Add New Packing List</h2>
					<div class="form-group">
                        <label for="project">Project</label>
                        <?php
                            echo '<br>';
                            echo '<select>';
                            $stmt = $db->query('SELECT project_id, client_company_name, reactor_name, project_start_date FROM projects NATURAL JOIN reactors NATURAL JOIN units NATURAL JOIN plants NATURAL JOIN clients');
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
