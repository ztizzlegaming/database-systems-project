<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
$db = connect_to_psql('tmdatabase');

if (!isset($_GET['reactorID'])) {
	header('Location: /');
}

# Get the informationo for this reactor
$reactorID = $_GET['reactorID'];
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
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<h1>Reactor Info</h1>
				<form method="post">
					<div class="form-group">
						<label for="reactor_id">Reactor ID</label>
						<input type="number" disabled class="form-control" id="reactor_id" value="<?php echo $reactor['reactor_id']; ?>">
					</div>
					<div class="form-group">
						<label for="unit_id">Unit ID</label>
						<input type="number" disabled class="form-control" id="unit_id" value="<?php echo $reactor['unit_id']; ?>">
					</div>
					<div class="form-group">
						<label for="calibrate_ttd_to">Calibrate TTD To</label>
						<input type="number" name="calibrate_ttd_to" class="form-control" id="calibrate_ttd_to" value="<?php echo $reactor['calibrate_ttd_to']; ?>">
					</div>
					<div class="form-group">
						<label for="calibration_orfice_size">Calibration Orifice Size</label>
					</div>
					<div class="form-group">
						<label for="catalyst_brand">Catalyst Brand</label>
					</div>
					<div class="form-group">
						<label for="catalyst_change_coordinator">Catalyst Change Coordinator</label>
					</div>
					<div class="form-group">
						<label for="catalyst_size">Catalyst Size</label>
					</div>
					<div class="form-group">
						<label for="chemical">Chemical</label>
					</div>
					<div class="form-group">
						<label for="comments">Comments</label>
					</div>
					<div class="form-group">
						<label for="compressor_pressure">Compressor Pressure</label>
					</div>
					<div class="form-group">
						<label for="expected_pressure_drop">Expected Pressure Drop</label>
					</div>
					<div class="form-group">
						<label for="flow_rate">Flow Rate</label>
					</div>
					<div class="form-group">
						<label for="loaded_tube_length">Loaded Tube Length</label>
					</div>
					<div class="form-group">
						<label for="manifold_pressure">Manifold Pressure</label>
					</div>
					<div class="form-group">
						<label for="number_of_plugs">Number of Plugs</label>
					</div>
					<div class="form-group">
						<label for="number_of_rows">Number of Rows</label>
					</div>
					<div class="form-group">
						<label for="number_of_supports">Number of Supports</label>
					</div>
					<div class="form-group">
						<label for="number_of_thermocouples">Number of Thermocouples</label>
					</div>
					<div class="form-group">
						<label for="number_of_tubes">Number of Tubes</label>
					</div>
					<div class="form-group">
						<label for="number_of_coolant_tubes">Number of Coolant Tubes</label>
					</div>
					<div class="form-group">
						<label for="outage">Outage</label>
					</div>
					<div class="form-group">
						<label for="reactor_head">Reactor Head</label>
					</div>
					<div class="form-group">
						<label for="reactor_loading_method">Reactor Loading Method</label>
					</div>
					<div class="form-group">
						<label for="reactor_manway_size">Reactor Manway Size</label>
					</div>
					<div class="form-group">
						<label for="reactor_name">Reactor Name</label>
					</div>
					<div class="form-group">
						<label for="reactor_pitch">Reactor Pitch</label>
					</div>
					<div class="form-group">
						<label for="seal_air_pressure">Seal Air Pressure</label>
					</div>
					<div class="form-group">
						<label for="sonic_up_to">Sonic Up To</label>
					</div>
					<div class="form-group">
						<label for="supply_orifice_size">Supply Orifice Size</label>
					</div>
					<div class="form-group">
						<label for="supply_pressure">Supply Pressure</label>
					</div>
					<div class="form-group">
						<label for="testing_type">Testing Type</label>
					</div>
					<div class="form-group">
						<label for="thermocouple_inner_diameter">Thermocouple Inner Diameter</label>
					</div>
					<div class="form-group">
						<label for="tube_inner_diameter">Tube Inner Diameter</label>
					</div>
					<div class="form-group">
						<label for="tube_seal_size">Tube Seal Size</label>
					</div>
					<div class="form-group">
						<label for="tube_spacing">Tube Spacing</label>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
