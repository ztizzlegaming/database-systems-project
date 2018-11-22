<?php
	ini_set("display_errors", 1);
	require("functions.php");
	head("Repair Info");
	$pdo = connect_to_psql('tmdatabase');

	if(isset($_POST['save']))
	{
		$sql = "UPDATE repairs SET equipment_id = :eid,personnel_id = :pid,incident_occured = :incident,".
		       "repair_date = :date,repair_notes = :notes WHERE repair_id = :id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id",$_POST['repair']);
		if(!empty($_POST['equipment_id']))
		{
			$stmt->bindValue(":eid", $_POST['equipment_id']);
			$stmt->bindValue(":pid", $_POST['personnel_id']);
			if(!empty($_POST['incident_occured']))
			{
				$stmt->bindValue(":incident", $_POST['incident_occured']);
			}
			else
			{
				$stmt->bindValue(":incident", null);
			}
			if(!empty($_POST['repair_date']))
			{
				$stmt->bindValue(":date", $_POST['repair_date']);
				if(!empty($_POST['repair_notes']))
				{
					$stmt->bindValue(":notes", $_POST['repair_notes']);
				}
				else
				{
					$stmt->bindValue(":notes", null);
				}
				$stmt->execute();
				header("location: repairs.php");
			}
			else
			{
				debug_message("Repair log not edited, Repair Date had no value");
			}
		}
		else
		{
			debug_message("Repair log not edited, Equipment ID had no value");
		}
	}

	function queryRepair()
	{
		$sql = "SELECT * FROM repairs WHERE repair_id = :id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['repair']);
		$stmt->execute();
		return $stmt->fetch();
	}
	function queryEquipment($id)
	{
		$sql = "SELECT equipment_name, equipment_sn FROM equipment WHERE equipment_id = :id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id",$id);
		$stmt->execute();
		return $stmt->fetch();
	}
	$repair = queryRepair();
	$equipment = queryEquipment($repair['equipment_id']);
	function getPersonnelSelected($id)
	{
		if($GLOBALS['repair']['personnel_id'] === $id)
		{
			return "selected";
		}
		else
		{
			return "";
		}
	}
	
?>

<style>
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
		min-width:400px;
		width:75%;
		margin:auto;
	}		
</style>
<div>
<div style='min-width:100px;float:right'>
<form method='post' action='repairs.php'>
<button type='submit'>Go to Repairs</button>
</form>
</div>
<div style='min-width:100px;width:40%'>
<form method='post' action='equipment_info.php'>
<button type='submit' name='edit' value='<?php echo $GLOBALS['repair']['equipment_id']; ?>'>Go to Equipment Info</button>
</form>
</div>
</div>
<h1>Repair Info</h1>
<form method='post' action='repairs.php'>
<input type='hidden' name='repair' value='<?php echo $_POST['repair']; ?>'>
<fieldset>
<label>Equipment ID<span style='color:red'>*</span>:</label><input type='number' name='equipment_id' value='<?php echo $GLOBALS['repair']['equipment_id']; ?>'>
</br>
<label>Equipment Name:</label><span><?php echo $GLOBALS['equipment']['equipment_name']; ?></span>
</br>
<label>Equipment Serial Number:</label><span><?php echo $GLOBALS['equipment']['equipment_sn']; ?></span>
</br>
<label>TM Personnel Overseeing Repair<span style='color:red'>*</span>:</label><select name='personnel_id'>
<?php
	$sql = "SELECT personnel_id, personnel_first_name, personnel_last_name FROM TMpersonnels ORDER BY personnel_last_name, personnel_first_name;";
	$stmt = $GLOBALS['pdo']->query($sql);
	while($row = $stmt->fetch())
	{
		$option = "<option value='".$row['personnel_id']."' ".getPersonnelSelected($row['personnel_id']).
			  ">".$row['personnel_first_name']." ".$row['personnel_last_name']."</option>";
		echo $option;
	}
?>
</select>
</br>
<label>Incident Occurred:</label><textarea style='vertical-align:top;margin-bottom:10px' name='incident_occured' cols='75' rows='10' maxlength='1000'>
<?php echo $GLOBALS['repair']['incident_occured']; ?>
</textarea>
</br>
<label>Repair Date<span style='color:red'>*</span>:</label><input type='date' name='repair_date' value='<?php echo $GLOBALS['repair']['repair_date']; ?>'>
</br>
<label>Repair Notes:</label><textarea style='vertical-align:top;margin-bottom:10px' name='repair_notes' cols='75' rows='10' maxlength='1000'>
<?php echo $GLOBALS['repair']['repair_notes']; ?>
</textarea>
</br>
<div style='text-align:center;width:87%'>
<button type='submit' name='save' value='save'>Save Repair Info</button>
</div>
</fieldset>
</form>

<?php tail(); ?>