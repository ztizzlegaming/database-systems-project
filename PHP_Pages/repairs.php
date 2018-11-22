<?php
	ini_set("display_errors", 1);
   	require("functions.php");
   	head("Repairs");
   	$pdo = connect_to_psql('tmdatabase');

	function queryRepairs()
	{
		$sql = "SELECT equipment_id, repair_id, repair_date, incident_occured".
		       " FROM repairs;";
		$stmt = $GLOBALS['pdo']->query($sql);
		return $stmt;
	}
	function getInputValue($value)
	{
		if(isset($_POST[$value]))
		{
			return $_POST[$value];
		}
		else
		{
			return "";
		}
	}
	function getPersonnelSelected($id)
	{
		if(isset($_POST['personnel_id']) && $_POST['personnel_id'] === $id)
		{
			return "selected";
		}
		else
		{
			return "";
		}
	}
   	function createInputs()
   	{
		$inputs = "<fieldset><label>Equipment ID<span style='color:red'>*</span>".
			  ":</label><input type='number' name='equipment_id' value='".
			  getInputValue('equipment_id')."'></br>".

			  "<label>TM Personnel Overseeing Repair<span style='color:red'>".
			  "*</span>:</label><select name='personnel_id'>";

		$sql = "SELECT personnel_id, personnel_first_name, personnel_last_name ".
		       "FROM TMpersonnels ORDER BY personnel_last_name, personnel_first".
		       "_name;";
		$stmt = $GLOBALS['pdo']->query($sql);
		while($row = $stmt->fetch())
		{
			$option = "<option value='".$row['personnel_id'].
				  getPersonnelSelected($row['personnel_id']).
				  "'>".$row['personnel_first_name']." ".
				  $row['personnel_last_name']."</option>";
			$inputs .= $option;
		}
		$inputs .= "</select></br><label>Incident Occurred:</label><textarea".
			   " style='vertical-align:top;margin-bottom:10px' name=".
			   "'incident_occured' cols='75' rows='10' maxlength='1000'>".
			   getInputValue('incident_occured')."</textarea></br>".

			   "<label>Repair Date<span style='color:red'>*</span>:</label>".
			   "<input type='date' name='repair_date' value='".
			   getInputValue('repair_date')."'></br>".

			   "<label>Repair Notes:</label><textarea style='vertical-align".
			   ":top;margin-bottom:10px' name='repair_notes' cols='75' ".
			   "rows='10' maxlength='1000'>".getInputValue('repair_notes').
			   "</textarea></br>".
			   
			   "<div style='text-align:center;width:87%'>".
			   "<button type='submit' name='add' value='add'>Add Repair Log".
			   "</button></div></fieldset>";
		return $inputs;
	}
	function createTable()
	{
		$sql = "SELECT ";
		$table = "<div id='constrainer'><div class='hscrolltable'><table ".
		       	 "class='header center'>";
	}
	$stmt = queryRepairs();
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
		width:99%;
		margin:0px;
	}
	table
	{
		border: 1px solid black;
		border-collapse: collapse;
		text-align: left;
		table-layout: fixed;
		width: 99%;
	}
	th, td
	{
		border:1px solid black;
		width: 150px;
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
		overflow-x:auto;
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
</style>

<h1>Repair Logs</h1>

<div class="container" style="min-width:1000px">
<div class="inputs" style="min-width:700px;width:60%;float:right">
<form method="post" action="repair.php">
<h3 style="text-align:center">Add Repair Log</h3>
<?php echo createInputs(); ?>
</form>
</div>
<div class="table" style="min-width:500px;width:50%">
<?php echo createTable(); ?>
</div>
</div>

<?php tail(); ?>