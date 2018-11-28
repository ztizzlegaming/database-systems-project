<?php
	ini_set("display_errors", 1);
	require("functions.php");

	if (!isLoggedIn()) {
		header('Location: /login.php');
		return;
	}

	head("Packing List Info");
	$pdo = connect_to_psql('tmdatabase');

	if(isset($_POST['add']))
	{
		$project_id = $_POST['project_id'];
		$sql = 'INSERT INTO project_equipment (project_id, equipment_id) VALUES';
		foreach($_POST['equipment_id'] as $equipment_id)
		{
			if($_POST[''.$equipment_id] == 'on')
			{
				$sql .= ' ('.$project_id.','.$equipment_id.')';
			}
		}
		$query = $GLOBALS['pdo']->prepare($sql);
		$query->execute();

	}

	function queryPackingLists()
	{
		if(isset($_POST['project_id']))
		{
			$sql = "SELECT * FROM packing_lists WHERE project_id = :project_id";
			$query = $GLOBALS['pdo']->prepare($sql);
			$query->bindValue(":project_id", $_POST['project_id']);
			$query->execute();
			return $query;
		}
		else
		{
			return array("project_id" => null);
		}
	}

	function queryAvailableEquipment()
	{
		$sql = "SELECT * FROM available_equipment";
		$query = $GLOBALS['pdo']->prepare($sql);
		$query->execute();
		return $query;
	}

	function queryProjectEquipment()
	{
		if(isset($_POST['project_id']))
		{
			$sql = "SELECT * FROM equipment NATURAL JOIN project_equipment NATURAL JOIN projects WHERE project_id = :project_id";
			$query = $GLOBALS['pdo']->prepare($sql);
			$query->bindValue(":project_id", $_POST['project_id']);
			$query->execute();
			return $query;
		}
		else
		{
			return array("project_id" => null);
		}
	}
	
	##Displays the information regarding the reactor for the packing list
	function displayReactorInfo()
	{
		$lists = queryPackingLists();
		$project_id = -1;
		$revisionNum = 1;
		$reactorName = "";
		$plantName = "";
		$clientName = "";
		$startDate = "";
		$shipDate = "";
		$rows = 0;
		$numOfTubes = 0;
		$tubeSealType = "";
		$tubeID = 0.0;
		$pitch = 0.0;
		$expectedPressureDrop = 0;
		$numOfThermocouples = 0;
		$supplyOrificeSize = 0.0;
		$calOrificeSize = 0.0;
		$calibrateTo = 0;
		$supplyPressure = 0;
		$pressureSensor = "";

		foreach($lists as $list)
		{
			if(isset($list['project_id']))
			{
				$revisionNum = $list['revision'];
				$reactorName = $list['reactor_name'];
				$plantName = $list['plant_name'];
				$clientName = $list['client_company_name'];
				$startDate = $list['project_start_date'];
				$shipDate = "";
				$rows = $list['number_of_rows'];
				$numOfTubes = $list['number_of_tubes'];
				$tubeSealType = $list['tube_seal_size'];
				$tubeID = $list['tube_inner_diameter'];
				$pitch = $list['reactor_pitch'] ;
				$expectedPressureDrop = $list['expected_pressure_drop'];
				$numOfThermocouples = $list['number_of_thermocouples'];
				$supplyOrificeSize = $list['supply_orifice_size'];
				$calOrificeSize = $list['calibration_orifice_size'];
				$calibrateTo = $list['calibrate_ttd_to'];
				$supplyPressure = $list['supply_pressure'];
				}
		}
		echo '<h1>Equipment Packing List</h1>';
		echo '<h2>Revision ' . $revisionNum . '</h2>';
		echo '<h3>' . $clientName . '</h3>';
		echo '<h3>' . $plantName . '</h3>';
		echo '<form type="post">';
		echo '<input type="hidden" name="project_id" value="' . $project_id . '">';
		echo '<table>';
		echo '<tr>';
		echo '<td>Reactor: ' . $reactorName .'</td>';
		echo '<td>Tube Seal Type: ' . $tubeSealType . '</td>';
		echo '<td>Supply Orifice Size (in): ' . $supplyOrificeSize . '</td>';
		echo '</tr> <tr>';
		echo '<td>Project Start Date: ' . $startDate . '</td>';
		echo '<td>Tube ID: ' . $tubeID . '</td>';
		echo '<td>Cal Orifice Size (in): ' . $calOrificeSize . '</td>';
		echo '</tr> <tr>';
		echo '<td>Ship Date: ' . $shipDate . '</td>';
		echo '<td>Pitch (in): ' . $pitch . '</td>';
		echo '<td>Pressure Sensor: ' . $pressureSensor . '</td>';
		echo '</tr> <tr>';
		echo '<td>Rows: ' . $rows . '</td>';
		echo '<td>Expected Pressure Drop: ' . $expectedPressureDrop . '</td>';
		echo '<td>Calibrate To:' . $calibrateTo . '</td>';
		echo '</tr> <tr>';
		echo '<td># of Tubes: ' . $numOfTubes . '</td>';
		echo '<td># of ThermoCouples: ' . $numOfThermocouples . '</td>';
		echo '<td>Supply Pressure:' . $supplyPressure . '</td>';
		echo '</tr> </table> </form>';
	}

	function displayAvailableEquipment()
	{
		echo '<h3>Available Equipment</h3>';
		echo '<form method="post">';
		echo '<table>';
		echo '<tr>';
		echo '<th>ID</th>';
		echo '<th>Name</th>';
		echo '<th>Location</th>';
		echo '<th>Add to List</th>';
		echo '</tr>';

		$availableEquipment = queryAvailableEquipment();
		foreach($availableEquipment as $equipment)
		{
			echo '<tr>';
			echo '<td>' . $equipment['equipment_id'] . '</td>';
			echo '<td>' . $equipment['equipment_name'] . '</td>';
			echo '<td>' . $equipment['equipment_location'] . '</td>';
			echo '<input type="hidden" name="equipment_id[]" value="'.$equipment['equipment_id'].'">';
			echo '<td><input type="checkbox" name="' . $equipment['equipment_id'] .'"/></td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<br />';
		echo '<input type="hidden" name="project_id" value="'.$_POST['project_id'].'">';
		echo '<input type="submit" name="add" value="Add">';
		echo '</form>';
	}

	function displayProjectEquipment()
	{
		echo '<h3>Project Equipment</h3>';
		echo '<form method="post">';
		echo '<table>';
		echo '<tr>';
		echo '<th>ID</th>';
		echo '<th>Name</th>';
		echo '<th>Location</th>';
		echo '<th>Remove from List</th>';
		echo '</tr>';

		$availableEquipment = queryProjectEquipment();
		foreach($availableEquipment as $equipment)
		{
			echo '<tr>';
			echo '<td>' . $equipment['equipment_id'] . '</td>';
			echo '<td>' . $equipment['equipment_name'] . '</td>';
			echo '<td>' . $equipment['equipment_location'] . '</td>';
			echo '<input type="hidden" name="equipment_id[]" value="'.$equipment['equipment_id'].'">';
			echo '<td><input type="checkbox" name="' . $equipment['equipment_id'] .'"/></td>';
			echo '</tr>';
		}
		echo '</table>';
		echo '<br />';
		echo '<input type="hidden" name="project_id" value="'.$_POST['project_id'].'">';
		echo '<input type="submit" name="remove" value="Remove">';
		echo '</form>';
	}

	function displayEquipmentInfo()
	{
		displayProjectEquipment();
		displayAvailableEquipment();	
	}	

?>

<style>
        table
	{
		border: 1px solid black;
		border-collapse: collapse;
		text-align: left;
		table-layout: fixed;
		width: 100%;
	}
	th, td
	{
		border:1px solid black;
		width: 200px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	#constrainer
	{
		height: 65%;
		width: 99%;
	}
	.hscrolltable
	{
		overflow-x: scroll;
		height: 100%;
	}

        .hscrolltable > .body
	{
		overflow-y: auto;
		width: -webkit-fit-content;
	}
	.hscrolltable
	{
		display: flex;
		display: -webkit-flex;
		flex-direction: column;
		-webkit-flex-direction: column;
	}
	.hscrolltable > .header {}
	.hscrolltable > .body
	{
		flex: 1;
		-webkit-flex: 1;
	}
	label
	{
		display:inline-block;
		width:200px;
		margin-right:30px;
		margin-bottom:10px;
		text-align:right;
	}
	fieldset
	{
		border:none;
		width:50%;
		margin:0px;
	}					
</style>
									   
<div>
<div style='float:left'>
<form method='get' action='packing_lists.php'>
<button  type='submit' name='home'>Go to Packing Lists</button>
</form>
</div>

<div style='text-align:center'>
<form method='get' action='index.php'>
<button style='width:125px' type='submit' name='home'>Home</button>
</form>
</div>
</div>

<div class="container" style="width: 100%;">
<div class="content">
<?php echo displayReactorInfo();
      displayEquipmentInfo();
?>
</div>
</div>
<?php tail(); ?>
