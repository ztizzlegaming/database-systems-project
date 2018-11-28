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

if (!isset($_GET['reactorID'])) {
	header('Location: /');
	return;
}

$reactorID = $_GET['reactorID'];

# Get the client id
$sql = 'SELECT client_id FROM clients NATURAL JOIN plants NATURAL JOIN units NATURAL JOIN reactors WHERE reactor_id = ?';
$stmt = $db->prepare($sql);
$stmt->execute([$reactorID]);
$row = $stmt->fetch();
$clientID = $row['client_id'];

if (isset($_POST['submit'])) {
	$submit = $_POST['submit'];
	if ($submit === 'Save') {
		$reactorHead = 1;
		if (!isset($_POST['reactor_head'])) {
			$reactorHead = 0;
		}
		$sql = 'UPDATE reactors SET
				calibrate_ttd_to = ?,
				calibration_orifice_size = ?,
				catalyst_brand = ?,
				catalyst_change_coordinator = ?,
				catalyst_size = ?,
				chemical = ?,
				comments = ?,
				compressor_pressure = ?,
				expected_pressure_drop = ?,
				flow_rate = ?,
				loaded_tube_length = ?,
				manifold_pressure = ?,
				number_of_plugs = ?,
				number_of_rows = ?,
				number_of_supports = ?,
				number_of_thermocouples = ?,
				number_of_tubes = ?,
				number_of_coolant_tubes = ?,
				outage = ?,
				reactor_head = ?,
				reactor_loading_method = ?,
				reactor_manway_size = ?,
				reactor_name = ?,
				reactor_pitch = ?,
				seal_air_pressure = ?,
				sonic_up_to = ?,
				supply_orifice_size = ?,
				supply_pressure = ?,
				testing_type = ?,
				thermocouple_inner_diameter = ?,
				tube_inner_diameter = ?,
				tube_seal_size = ?,
				tube_spacing = ?
				WHERE reactor_id = ?';

				$data = [
					$_POST['calibrate_ttd_to'],
					$_POST['calibration_orifice_size'],
					$_POST['catalyst_brand'],
					$_POST['catalyst_change_coordinator'],
					$_POST['catalyst_size'],
					$_POST['chemical'],
					$_POST['comments'],
					$_POST['compressor_pressure'],
					$_POST['expected_pressure_drop'],
					$_POST['flow_rate'],
					$_POST['loaded_tube_length'],
					$_POST['manifold_pressure'],
					$_POST['number_of_plugs'],
					$_POST['number_of_rows'],
					$_POST['number_of_supports'],
					$_POST['number_of_thermocouples'],
					$_POST['number_of_tubes'],
					$_POST['number_of_coolant_tubes'],
					$_POST['outage'],
					$reactorHead,
					$_POST['reactor_loading_method'],
					$_POST['reactor_manway_size'],
					$_POST['reactor_name'],
					$_POST['reactor_pitch'],
					$_POST['seal_air_pressure'],
					$_POST['sonic_up_to'],
					$_POST['supply_orifice_size'],
					$_POST['supply_pressure'],
					$_POST['testing_type'],
					$_POST['thermocouple_inner_diameter'],
					$_POST['tube_inner_diameter'],
					$_POST['tube_seal_size'],
					$_POST['tube_spacing'],
					$_POST['reactor_id']
				];

				for ($i1 = 0; $i1 < sizeof($data); $i1++) {
					if ($data[$i1] === '') {
						$data[$i1] = NULL;
					}
				}

				$stmt = $db->prepare($sql);
				try {
					$stmt->execute($data);
				} catch (PDOException $e) {
					echo '<div class="error">The reactor could not be updated. One of the required fields may have been left blank.</div>';
				}
	} else if ($submit === 'Delete Reactor') {
		try {
			$reactorID = $_POST['reactor_id'];

			$sql = 'DELETE FROM reactors WHERE reactor_id = ?';
			$stmt = $db->prepare($sql);
			$stmt->execute([$reactorID]);
			
			header('Location: client_info.php?clientID=' . $clientID);
			return;
		} catch (PDOException $e) {
			echo '<div class="error">There was an error deleting the reactor. There may be reactor zones that depend on it. These must be deleted first.</div>';
		}
	} else if ($submit === 'Delete Zone') {
		$zoneID = $_POST['zone_id'];
		$sql = 'DELETE FROM reactor_zones WHERE reactor_zone_id = ?';
		$stmt = $db->prepare($sql);
		$stmt->execute([$zoneID]);
	} else if ($submit === 'Add Zone') {
		$reactorID = $_POST['reactor_id'];
		$avgPD = $_POST['average_pressure_drop'];
		$equivOrifice = $_POST['equiv_orifice'];
		$outage = $_POST['outage'];

		$sql = 'INSERT INTO reactor_zones
				(reactor_id, reactor_zone_average_pressure_drop, reactor_zone_equiv_orifice, reactor_zone_outage)
				VALUES (?, ?, ?, ?)';
		$stmt = $db->prepare($sql);
		try {
			$stmt->execute([$reactorID, $avgPD, $equivOrifice, $outage]);
		} catch (PDOException $e) {
			echo '<div class="error">There was an error creating the new zone. One of the required fields may have been left blank.</div>';
		}
	}
}

# Get the informationo for this reactor
$stmt = $db->prepare('SELECT * FROM reactors WHERE reactor_id = ?');
$stmt->execute([$reactorID]);
if ($stmt->rowCount() === 0) {
	header('Location: /');
}
$reactor = $stmt->fetch();

# Get the reactor zones for this reactor
$sql = 'SELECT * FROM reactor_zones WHERE reactor_id = ?';
$stmt = $db->prepare($sql);
$stmt->execute([$reactorID]);
$reactorZones = $stmt->fetchAll();
?>

<html>
<head>
	<title>Reactor Info | TubeMaster</title>

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
				<a href="client_info.php?clientID=<?php echo $clientID; ?>">Back to Client Info</a>
				
				<h1>Reactor Info</h1>
				<form method="post">
					<div class="form-group">
						<label for="reactor_id">Reactor ID</label>
						<input type="number" step="any" disabled class="form-control" id="reactor_id" value="<?php echo $reactor['reactor_id']; ?>">
						<input type="hidden" name="reactor_id" value="<?php echo $reactor['reactor_id']; ?>">
					</div>
					<div class="form-group">
						<label for="unit_id">Unit ID</label>
						<input type="number" step="any" disabled class="form-control" id="unit_id" value="<?php echo $reactor['unit_id']; ?>">
					</div>
					<div class="form-group">
						<label for="calibrate_ttd_to">Calibrate TTD To</label>
						<input type="number" step="any" name="calibrate_ttd_to" class="form-control" id="calibrate_ttd_to" value="<?php echo $reactor['calibrate_ttd_to']; ?>">
					</div>
					<div class="form-group">
						<label for="calibration_orifice_size">Calibration Orifice Size (REQUIRED)</label>
						<input type="number" step="any" name="calibration_orifice_size" class="form-control" id="calibration_orifice_size" value="<?php echo $reactor['calibration_orifice_size']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="catalyst_brand">Catalyst Brand</label>
						<input type="text" name="catalyst_brand" class="form-control" id="catalyst_brand" value="<?php echo $reactor['catalyst_brand']; ?>">
					</div>
					<div class="form-group">
						<label for="catalyst_change_coordinator">Catalyst Change Coordinator</label>
						<input type="text" name="catalyst_change_coordinator" class="form-control" id="catalyst_change_coordinator" value="<?php echo $reactor['catalyst_change_coordinator']; ?>">
					</div>
					<div class="form-group">
						<label for="catalyst_size">Catalyst Size</label>
						<input type="text" name="catalyst_size" class="form-control" id="catalyst_size" value="<?php echo $reactor['catalyst_size']; ?>">
					</div>
					<div class="form-group">
						<label for="chemical">Chemical</label>
						<input type="text" name="chemical" class="form-control" id="chemical" value="<?php echo $reactor['chemical']; ?>" maxlength="2">
					</div>
					<div class="form-group">
						<label for="comments">Comments</label>
						<input type="text" name="comments" class="form-control" id="comments" value="<?php echo $reactor['comments']; ?>">
					</div>
					<div class="form-group">
						<label for="compressor_pressure">Compressor Pressure</label>
						<input type="number" step="any" name="compressor_pressure" class="form-control" id="compressor_pressure" value="<?php echo $reactor['compressor_pressure']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="expected_pressure_drop">Expected Pressure Drop (REQUIRED)</label>
						<input type="number" step="any" name="expected_pressure_drop" class="form-control" id="expected_pressure_drop" value="<?php echo $reactor['expected_pressure_drop']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="flow_rate">Flow Rate</label>
						<input type="number" step="any" name="flow_rate" class="form-control" id="flow_rate" value="<?php echo $reactor['flow_rate']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="loaded_tube_length">Loaded Tube Length</label>
						<input type="number" step="any" name="loaded_tube_length" class="form-control" id="loaded_tube_length" value="<?php echo $reactor['loaded_tube_length']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="manifold_pressure">Manifold Pressure</label>
						<input type="number" step="any" name="manifold_pressure" class="form-control" id="manifold_pressure" value="<?php echo $reactor['manifold_pressure']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="number_of_plugs">Number of Plugs</label>
						<input type="number" step="any" name="number_of_plugs" class="form-control" id="number_of_plugs" value="<?php echo $reactor['number_of_plugs']; ?>" min="1">
					</div>
					<div class="form-group">
						<label for="number_of_rows">Number of Rows</label>
						<input type="number" step="any" name="number_of_rows" class="form-control" id="number_of_rows" value="<?php echo $reactor['number_of_rows']; ?>" min="1">
					</div>
					<div class="form-group">
						<label for="number_of_supports">Number of Supports</label>
						<input type="number" step="any" name="number_of_supports" class="form-control" id="number_of_supports" value="<?php echo $reactor['number_of_supports']; ?>" min="1">
					</div>
					<div class="form-group">
						<label for="number_of_thermocouples">Number of Thermocouples</label>
						<input type="number" step="any" name="number_of_thermocouples" class="form-control" id="number_of_thermocouples" value="<?php echo $reactor['number_of_thermocouples']; ?>" min="1">
					</div>
					<div class="form-group">
						<label for="number_of_tubes">Number of Tubes</label>
						<input type="number" step="any" name="number_of_tubes" class="form-control" id="number_of_tubes" value="<?php echo $reactor['number_of_tubes']; ?>" min="1">
					</div>
					<div class="form-group">
						<label for="number_of_coolant_tubes">Number of Coolant Tubes (REQUIRED)</label>
						<input type="number" step="any" name="number_of_coolant_tubes" class="form-control" id="number_of_coolant_tubes" value="<?php echo $reactor['number_of_coolant_tubes']; ?>" min="1">
					</div>
					<div class="form-group">
						<label for="outage">Outage (REQUIRED)</label>
						<input type="number" step="any" name="outage" class="form-control" id="outage" value="<?php echo $reactor['outage']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="reactor_head">Reactor Head</label>
						<input type="checkbox" name="reactor_head" class="form-control" id="reactor_head" <?php if ($reactor['reactor_head']) { echo 'checked'; } ?>>
					</div>
					<div class="form-group">
						<label for="reactor_loading_method">Reactor Loading Method</label>
						<input type="text" name="reactor_loading_method" class="form-control" id="reactor_loading_method" value="<?php echo $reactor['reactor_loading_method']; ?>">
					</div>
					<div class="form-group">
						<label for="reactor_manway_size">Reactor Manway Size</label>
						<input type="number" step="any" name="reactor_manway_size" class="form-control" id="reactor_manway_size" value="<?php echo $reactor['reactor_manway_size']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="reactor_name">Reactor Name</label>
						<input type="text" name="reactor_name" class="form-control" id="reactor_name" value="<?php echo $reactor['reactor_name']; ?>">
					</div>
					<div class="form-group">
						<label for="reactor_pitch">Reactor Pitch</label>
						<input type="number" step="any" name="reactor_pitch" class="form-control" id="reactor_pitch" value="<?php echo $reactor['reactor_pitch']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="seal_air_pressure">Seal Air Pressure</label>
						<input type="number" step="any" name="seal_air_pressure" class="form-control" id="seal_air_pressure" value="<?php echo $reactor['seal_air_pressure']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="sonic_up_to">Sonic Up To</label>
						<input type="number" step="any" name="sonic_up_to" class="form-control" id="sonic_up_to" value="<?php echo $reactor['sonic_up_to']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="supply_orifice_size">Supply Orifice Size (REQUIRED)</label>
						<input type="number" step="any" name="supply_orifice_size" class="form-control" id="supply_orifice_size" value="<?php echo $reactor['supply_orifice_size']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="supply_pressure">Supply Pressure</label>
						<input type="number" step="any" name="supply_pressure" class="form-control" id="supply_pressure" value="<?php echo $reactor['supply_pressure']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="testing_type">Testing Type</label>
						<input type="text" name="testing_type" class="form-control" id="testing_type" value="<?php echo $reactor['testing_type']; ?>" maxlength="2">
					</div>
					<div class="form-group">
						<label for="thermocouple_inner_diameter">Thermocouple Inner Diameter</label>
						<input type="number" step="any" name="thermocouple_inner_diameter" class="form-control" id="thermocouple_inner_diameter" value="<?php echo $reactor['thermocouple_inner_diameter']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="tube_inner_diameter">Tube Inner Diameter</label>
						<input type="number" step="any" name="tube_inner_diameter" class="form-control" id="tube_inner_diameter" value="<?php echo $reactor['tube_inner_diameter']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="tube_seal_size">Tube Seal Size</label>
						<input type="number" step="any" name="tube_seal_size" class="form-control" id="tube_seal_size" value="<?php echo $reactor['tube_seal_size']; ?>" min="0">
					</div>
					<div class="form-group">
						<label for="tube_spacing">Tube Spacing (REQUIRED)</label>
						<input type="number" step="any" name="tube_spacing" class="form-control" id="tube_spacing" value="<?php echo $reactor['tube_spacing']; ?>" min="0">
					</div>
					<div class="form-group">
						<input type="submit" name="submit" value="Save" class="btn btn-block btn-success">
					</div>
				</form>
				<form method="post">
					<div class="form-group">
						<input type="hidden" name="reactor_id" value="<?php echo $reactor['reactor_id']; ?>">
						<input type="submit" name="submit" value="Delete Reactor" class="btn btn-block btn-danger">
					</div>
				</form>
				<hr>
				<h3>Reactor Zones</h3>
				<table width="100%" border="1">
					<tr>
						<th>Reactor Zone ID</th>
						<th>Average Pressure Drop</th>
						<th>Equiv Orifice</th>
						<th>Outage</th>
						<th></th>
					</tr>
					<?php
					foreach ($reactorZones as $zone) {
						$zoneID = $zone['reactor_zone_id'];
						$avgPD = $zone['reactor_zone_average_pressure_drop'];
						$equivOrifice = $zone['reactor_zone_equiv_orifice'];
						$outage = $zone['reactor_zone_outage'];

						echo '<tr>';
						echo '<td>' . $zoneID . '</td>';
						echo '<td>' . $avgPD . '</td>';
						echo '<td>' . $equivOrifice . '</td>';
						echo '<td>' . $outage . '</td>';

						echo '<td>';
						echo '<form method="post" style="margin: 0">';
						echo '<input type="hidden" name="zone_id" value="' . $zoneID . '">';
						echo '<input type="submit" name="submit" value="Delete Zone" class="btn btn-block btn-danger">';
						echo '</form>';

						echo '</tr>';
					}
					?>
				</table>
				<br>
				<form method="post">
					<h4>Add Reactor Zone</h4>
					<div class="form-group">
						<label for="z_average_pressure_drop">Average Pressure Drop</label>
						<input type="number" step="any" name="average_pressure_drop" class="form-control" id="z_average_pressure_drop">
					</div>
					<div class="form-group">
						<label for="z_equiv_orifice">Equiv Orifice (REQUIRED)</label>
						<input type="number" step="any" name="equiv_orifice" class="form-control" id="z_equiv_orifice" min="0">
					</div>
					<div class="form-group">
						<label for="z_outage">Outage</label>
						<input type="number" step="any" name="outage" class="form-control" id="z_outage" min="0">
					</div>
					<div class="form-group">
						<input type="hidden" name="reactor_id" value="<?php echo $reactor['reactor_id']; ?>">
						<input type="submit" name="submit" value="Add Zone" class="btn btn-block btn-success">
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
