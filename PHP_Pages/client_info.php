<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
$db = connect_to_psql('tmdatabase');

if (!isset($_GET['clientID'])) {
	header('Location: /');
}

if (isset($_POST['submit'])) {
	$submit = $_POST['submit'];
	if ($submit === 'Save') {
		$id = $_POST['id'];
		$companyName = $_POST['company_name'];
		$contactFirstName = $_POST['contact_first_name'];
		$contactLastName = $_POST['contact_last_name'];
		$contactPhoneNumber = $_POST['contact_phone_number'];
		$contactEmail = $_POST['contact_email'];
		$streetAddress = $_POST['street_address'];
		$city = $_POST['city'];
		$country = $_POST['country'];

		$sql = 'UPDATE clients 
			SET client_company_name = ?,
			    client_contact_first_name = ?,
			    client_contact_last_name = ?,
			    client_contact_phone_number = ?,
			    client_contact_email = ?,
			    client_street_address = ?,
			    client_city = ?,
			    client_country = ?
			WHERE client_id = ?';
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
			$id
		];
		$stmt->execute($data);
	} else if ($submit === 'Delete Client') {
		try {
			$id = $_POST['id'];
			$sql = 'DELETE FROM clients WHERE client_id = ?';
			$stmt = $db->prepare($sql);
			$stmt->execute([$id]);
			header('Location: clients.php');
		} catch (PDOException $e) {
			echo '<div class="error">An error occured when trying to delete this client. There may be plants, units, or reactors that depend on this client.</div>';
		}
	} else if ($submit === 'Delete Plant') {
		try {
			$plantID = $_POST['plant_id'];
			$sql = 'DELETE FROM plants WHERE plant_id = ?';
			$stmt = $db->prepare($sql);
			$stmt->execute([$plantID]);
		} catch (PDOException $e) {
			echo '<div class="error">An error occured when trying to delete this plant. There may be units or reactors that depend on this plant.</div>';
		}
	} else if ($submit === 'Add Plant') {
		$clientID = $_POST['client_id'];
		$plantName = $_POST['plant_name'];
		$streetAddress = $_POST['plant_street_address'];
		$city = $_POST['plant_city'];
		$zip = $_POST['plant_zip'];
		$country = $_POST['plant_country'];

		$sql = 'INSERT INTO plants (client_id, plant_name, plant_street_address, plant_city, plant_zip_code, plant_country) VALUES (?, ?, ?, ?, ?, ?)';
		$stmt = $db->prepare($sql);
		$stmt->execute([$clientID, $plantName, $streetAddress, $city, $zip, $country]);
	} else if ($submit === 'Delete Unit') {
		try {
			$unitID = $_POST['unit_id'];
			$sql = 'DELETE FROM units WHERE unit_id = ?';
			$stmt = $db->prepare($sql);
			$stmt->execute([$unitID]);
		} catch (PDOException $e) {
			echo '<div class="error">An error occured when trying to delete this plant. There may be reactors that depend on this unit.</div>';
		}
	} else if ($submit === 'Add Unit') {
		$unitName = $_POST['unit_name'];
		$plantID = $_POST['plant_id'];

		$sql = 'INSERT INTO units (plant_id, unit_name) VALUES (?, ?)';
		$stmt = $db->prepare($sql);
		$stmt->execute([$plantID, $unitName]);
	} else if ($submit === 'Add Reactor') {
		$unitID = $_POST['unit_id'];
		$calibrationOrificeSize = $_POST['calibration_orfice_size'];
		$expectedPressureDrop = $_POST['expected_pressure_drop'];
		$numberOfCoolantTubes = $_POST['number_of_coolant_tubes'];
		$outage = $_POST['outage'];
		$supplyOrificeSize = $_POST['supply_orifice_size'];
		$tubeSpacing = $_POST['tube_spacing'];

		$sql = 'INSERT INTO reactors (unit_id, calibration_orfice_size, expected_pressure_drop, number_of_coolant_tubes, outage, supply_orifice_size, tube_spacing) VALUES (?, ?, ?, ?, ?, ?, ?)';
		$stmt = $db->prepare($sql);
		$stmt->execute([$unitID, $calibrationOrificeSize, $expectedPressureDrop, $numberOfCoolantTubes, $outage, $supplyOrificeSize, $tubeSpacing]);
	}
}

$clientID = $_GET['clientID'];
$stmt = $db->prepare('SELECT * FROM clients WHERE client_id = ?');
$stmt->execute([$clientID]);
if ($stmt->rowCount() === 0) {
	header('Location: clients.php');
}
$client = $stmt->fetch();

# Get all plants that this client has
$sql = 'SELECT * FROM plants NATURAL JOIN clients WHERE client_id = ? ORDER BY plant_id';
$stmt = $db->prepare($sql);
$stmt->execute([$clientID]);
$plants = $stmt->fetchAll();

# Get all units that this client has
$sql = 'SELECT unit_id, unit_name, plant_name FROM clients NATURAL JOIN plants NATURAL JOIN units WHERE client_id = ? ORDER BY unit_id';
$stmt = $db->prepare($sql);
$stmt->execute([$clientID]);
$units = $stmt->fetchAll();

# Get all the reactors that this client has
$sql = "
SELECT reactor_id, plant_name, unit_name,
	plant_street_address || ', ' || plant_city || ', ' || plant_country || ' ' || plant_zip_code AS plant_location
FROM clients
	NATURAL JOIN plants
	NATURAL JOIN units
	NATURAL JOIN reactors
WHERE client_id = ?
ORDER BY reactor_id
";
$stmt = $db->prepare($sql);
$stmt->execute([$clientID]);
$reactors = $stmt->fetchAll();
?>

<html>
<head>
	<title>Client Info | TubeMaster</title>

	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<style>
		.error {
			color: red;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<a href="/">Home</a>
				<br>
				<a href="clients.php">Back to clients</a>
				<h1>Client Info</h1>

				<form method="post">
					<div class="form-group">
						<label for="client_id">Client ID</label>
						<input type="number" disabled class="form-control" id="client_id" value="<?php echo $client['client_id']; ?>">
						<input type="hidden" name="id" value="<?php echo $client['client_id']; ?>">
					</div>
					<div class="form-group">
						<label for="company_name">Company Name</label>
						<input type="text" name="company_name" class="form-control" id="company_name" value="<?php echo $client['client_company_name']; ?>">
					</div>
					<div class="form-group">
						<label for="contact_first_name">Contact First Name</label>
						<input type="text" name="contact_first_name" class="form-control" id="contact_first_name" value="<?php echo $client['client_contact_first_name']; ?>">
					</div>
					<div class="form-group">
						<label for="contact_last_name">Contact Last Name</label>
						<input type="text" name="contact_last_name" class="form-control" id="contact_last_name" value="<?php echo $client['client_contact_last_name']; ?>">
					</div>
					<div class="form-group">
						<label for="contact_phone_number">Contact Phone Number</label>
						<input type="text" name="contact_phone_number" class="form-control" id="contact_phone_number" value="<?php echo $client['client_contact_phone_number']; ?>">
					</div>
					<div class="form-group">
						<label for="contact_email">Contact Email</label>
						<input type="email" name="contact_email" class="form-control" id="contact_email" value="<?php echo $client['client_contact_email']; ?>">
					</div>
					<div class="form-group">
						<label for="street_address">Street Address</label>
						<input type="text" name="street_address" class="form-control" id="street_address" value="<?php echo $client['client_street_address']; ?>">
					</div>
					<div class="form-group">
						<label for="city">City</label>
						<input type="text" name="city" class="form-control" id="city" value="<?php echo $client['client_city']; ?>">
					</div>
					<div class="form-group">
						<label for="country">Country (2 character code)</label>
						<input type="text" name="country" class="form-control" id="country" value="<?php echo $client['client_country']; ?>" maxlength="2">
					</div>
					<div class="form-group">
						<input type="submit" name="submit" class="form-control btn btn-block btn-success" value="Save">
					</div>
				</form>
				<form method="post">
					<div class="form-group">
						<input type="hidden" name="id" value="<?php echo $client['client_id']; ?>">
						<input type="submit" name="submit" class="form-control btn btn-block btn-danger" value="Delete Client">
					</div>
				</form>
				<hr>
				<h2>Plants</h2>
				<table width="100%" border="1">
					<tr>
						<th>Plant ID</th>
						<th>Plant Name</th>
						<th>Street Address</th>
						<th>City</th>
						<th>ZIP Code</th>
						<th>Country</th>
						<th></th>
					</tr>
					<?php
					foreach ($plants as $plant) {
						$plantID = $plant['plant_id'];
						$plantName = $plant['plant_name'];
						$streetAddress = $plant['plant_street_address'];
						$city = $plant['plant_city'];
						$zip = $plant['plant_zip_code'];
						$country = $plant['plant_country'];

						echo '<tr>';
						echo '<td>' . $plantID . '</td>';
						echo '<td>' . $plantName . '</td>';
						echo '<td>' . $streetAddress . '</td>';
						echo '<td>' . $city . '</td>';
						echo '<td>' . $zip . '</td>';
						echo '<td>' . $country . '</td>';

						echo '<td>';
						echo '<form method="post" style="margin: 0">';
						echo '<input type="hidden" name="plant_id" value="' . $plantID . '">';
						echo '<input type="submit" name="submit" value="Delete Plant" class="btn btn-block btn-danger">';
						echo '</form>';

						echo '</tr>';
					}
					?>
				</table>
				<br>
				<form method="post">
					<h4>New Plant</h4>
					<div class="form-group">
						<label for="plant_name">Plant Name</label>
						<input type="text" name="plant_name" class="form-control" id="plant_name">
					</div>
					<div class="form-group">
						<label for="plant_street_address">Street Address</label>
						<input type="text" name="plant_street_address" class="form-control" id="plant_street_address">
					</div>
					<div class="form-group">
						<label for="plant_city">City</label>
						<input type="text" name="plant_city" class="form-control" id="plant_city">
					</div>
					<div class="form-group">
						<label for="plant_zip">ZIP Code</label>
						<input type="text" name="plant_zip" class="form-control" id="plant_zip">
					</div>
					<div class="form-group">
						<label for="plant_country">Country (2 character code)</label>
						<input type="text" name="plant_country" class="form-control" id="plant_country" maxlength="2">
					</div>
					<div class="form-group">
						<input type="hidden" name="client_id" value="<?php echo $client['client_id']; ?>">
						<input type="submit" name="submit" value="Add Plant" class="btn btn-block btn-success">
					</div>
				</form>
				<hr>
				<h3>Units</h3>
				<table width="100%" border="1">
					<tr>
						<th>Unit ID</th>
						<th>Unit Name</th>
						<th>Plant Name</th>
						<th></th>
					</tr>
					<?php
					foreach ($units as $unit) {
						$unitID = $unit['unit_id'];
						$unitName = $unit['unit_name'];
						$plantName = $unit['plant_name'];

						echo '<tr>';
						echo '<td>' . $unitID . '</td>';
						echo '<td>' . $unitName . '</td>';
						echo '<td>' . $plantName . '</td>';

						echo '<td>';
						echo '<form method="post" style="margin: 0">';
						echo '<input type="hidden" name="unit_id" value="' . $unitID . '">';
						echo '<input type="submit" name="submit" value="Delete Unit" class="btn btn-block btn-danger">';
						echo '</form>';
						echo '</td>';

						echo '</tr>';
					}
					?>
				</table>
				<br>
				<h4>New Unit</h4>
				<form method="post">
					<div class="form-group">
						<label for="unit_name">Unit Name</label>
						<input type="text" name="unit_name" class="form-control" id="unit_name">
					</div>
					<div class="form-group">
						<label for="plant_id">Plant ID</label>
						<input type="number" name="plant_id" class="form-control" id="plant_id" min="1">
					</div>
					<div class="form-group">
						<input type="submit" name="submit" value="Add Unit" class="btn btn-block btn-success">
					</div>
				</form>
				<hr>
				<h2>Reactors</h2>
				<table width="100%" border="1">
					<tr>
						<th>Reactor ID</th>
						<th>Plant Name</th>
						<th>Plant Location</th>
						<th>Unit Name</th>
						<th></th>
					</tr>
					<?php
					foreach ($reactors as $reactor) {
						$reactorID = $reactor['reactor_id'];
						$plantName = $reactor['plant_name'];
						$plantLocation = $reactor['plant_location'];
						$unitName = $reactor['unit_name'];

						echo '<tr>';
						echo '<td>' . $reactorID . '</td>';
						echo '<td>' . $plantName . '</td>';
						echo '<td>' . $plantLocation . '</td>';
						echo '<td>' . $unitName . '</td>';
						echo '<td><a href="reactor_info.php?reactorID=' . $reactorID . '">View/Edit</a></td>';
						echo '</tr>';
					}
					?>
				</table>
				<br>
				<form method="post">
					<h4>New Reactor</h4>
					<div class="form-group">
						<label for="unit_id">Unit ID</label>
						<input type="number" name="unit_id" class="form-control" id="unit_id" min="1">
					</div>
					<div class="form-group">
						<label for="calibration_orfice_size">Calibration Orfice Size</label>
						<input type="number" name="calibration_orfice_size" class="form-control" id="calibration_orfice_size" min="0">
					</div>
					<div class="form-group">
						<label for="expected_pressure_drop">Expected Pressure Drop</label>
						<input type="number" name="expected_pressure_drop" class="form-control" id="expected_pressure_drop" min="0">
					</div>
					<div class="form-group">
						<label for="number_of_coolant_tubes">Number of Coolant Tubes</label>
						<input type="number" name="number_of_coolant_tubes" class="form-control" id="number_of_coolant_tubes" min="1">
					</div>
					<div class="form-group">
						<label for="outage">Outage</label>
						<input type="number" name="outage" class="form-control" id="outage" min="0">
					</div>
					<div class="form-group">
						<label for="supply_orifice_size">Supply Orifice Size</label>
						<input type="number" name="supply_orifice_size" class="form-control" id="supply_orifice_size" min="0">
					</div>
					<div class="form-group">
						<label for="tube_spacing">Tube Spacing</label>
						<input type="number" name="tube_spacing" class="form-control" id="tube_spacing" min="0">
					</div>
					<div class="form-group">
						<input type="submit" name="submit" value="Add Reactor" class="btn btn-block btn-success">
					</div>
					<div>NOTE: to add other reactor values, like 'calibrate_ttd_to', 'flow_rate', etc. create the new reactor, then edit the values.</div>
				</form>
				<br><br>
			</div>
		</div>
	</div>
</body>
</html>
