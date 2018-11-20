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
	<title>Clients | TubeMaster</title>

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
				<h1>Clients</h1>
				<table border="1" width="100%">
					<tr>
						<th>Client ID</th>
						<th>Company Name</th>
						<th>Contact Name</th>
						<th>Contact Phone Number</th>
						<th>Contact Email</th>
						<th>Street Address</th>
						<th>City</th>
						<th>Country</th>
						<th>ZIP Code</th>
						<th></th>
					</tr>

					<?php
					$stmt = $db->query('SELECT * FROM clients');
					$clients = $stmt->fetchAll();
					foreach ($clients as $client) {
						$id = $client['client_id'];
						$city = $client['client_city'];
						$companyName = $client['client_company_name'];
						$contactEmail = $client['client_contact_email'];
						$contactFName = $client['client_contact_first_name'];
						$contactLName = $client['client_contact_last_name'];
						$contactPhone = $client['client_contact_phone_number'];
						$country = $client['client_country'];
						$streetAddr = $client['client_street_address'];
						$zip = $client['client_zip_code'];

						$contactName = $contactFName . ' ' . $contactLName;

						echo '<tr>';
						echo '<td>' . $id . '</td>';
						echo '<td>' . $companyName . '</td>';
						echo '<td>' . $contactName . '</td>';
						echo '<td>' . $contactPhone . '</td>';
						echo '<td>' . $contactEmail . '</td>';
						echo '<td>' . $streetAddr . '</td>';
						echo '<td>' . $city . '</td>';
						echo '<td>' . $country . '</td>';
						echo '<td>' . $zip . '</td>';
						echo '<td><a href="client_info.php?clientID=' . $id . '">View/Edit</a></td>';
						echo '</tr>';
					}
					?>
				</table>
				<hr>
				<form method="POST">
					<h2>Add New Client</h2>
					<div class="form-group">
						<label for="company_name">Company Name</label>
						<input type="text" name="company_name" class="form-control" id="company_name" placeholder="abcabcabc">
					</div>
					<div class="form-group">
						<label for="first_name">Contact First Name</label>
						<input type="text" name="contact_first_name" class="form-control" id="first_name" placeholder="john">
					</div>
					<div class="form-group">
						<label for="last_name">Contact Last Name</label>
						<input type="text" name="contact_last_name" class="form-control" id="last_name" placeholder="smith">
					</div>
					<div class="form-group">
						<label for="phone">Contact Phone Number</label>
						<input type="text" name="contact_phone" class="form-control" id="phone" placeholder="(859) 555-1234">
					</div>
					<div class="form-group">
						<label for="contact_email">Contact Email</label>
						<input type="email" name="contact_email" class="form-control" id="contact_email" placeholder="abc@example.com">
					</div>
					<div class="form-group">
						<label for="street_address">Street Address</label>
						<input type="text" name="street_address" class="form-control" id="street_address" placeholder="123 Example Road">
					</div>
					<div class="form-group">
						<label for="city">City</label>
						<input type="text" name="city" class="form-control" id="city" placeholder="louisville">
					</div>
					<div class="form-group">
						<label for="country">Country (2 character country code)</label>
						<input type="text" name="country" class="form-control" id="country" placeholder="US" maxlength="2">
					</div>
					<div class="form-group">
						<label for="zip">ZIP Code</label>
						<input type="text" name="zip" class="form-control" id="zip" placeholder="40511">
					</div>
					<div class="form-group">
						<input type="submit" name="submit" value="Add new client" class="form-control btn btn-block btn-success">
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>