<?php
	ini_set("display_errors", 1);
   	require("functions.php");
   	head("Repairs");
   	$pdo = connect_to_psql('tmdatabase');

	if(isset($_POST['delete']))
	{
		if(isset($_POST['DELETE_LOGS']))
		{
			$id_array = $_POST['DELETE_LOGS'];
			$curr_id = 0;
			$sql = "DELETE FROM repairs WHERE repair_id = :id;";
			foreach($id_array as $key => $id)
			{
				$curr_id = $id;
				$stmt = $GLOBALS['pdo']->prepare($sql);
				$stmt->bindValue(":id", $id);
				$stmt->execute();
			}
			header("location: repairs.php");
		}
		else
		{
			debug_message("No repair logs were selected for deletion");
		}
	}
	elseif(isset($_POST['add']))
	{
		$sql = "INSERT INTO repairs(equipment_id,personnel_id,repair_date,".
		       "incident_occured,repair_notes) VALUES(:eid,:pid,:date,:incident,".
		       ":notes);";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$id = "SELECT * FROM equipment WHERE equipment_id = :id;";
		if(!empty($_POST['equipment_id']))
		{
			$id_stmt = $GLOBALS['pdo']->prepare($id);
			$id_stmt->bindValue(":id",$_POST['equipment_id']);
			$id_stmt->execute();
			if($id_stmt->rowCount() > 0)
			{
				$stmt->bindValue(":eid",$_POST['equipment_id']);
				$stmt->bindValue(":pid",$_POST['personnel_id']);
				if(!empty($_POST['repair_date']))
				{
					$stmt->bindValue(":date",$_POST['repair_date']);
					if(!empty($_POST['incident_occured']))
					{
						$stmt->bindValue(":incident",$_POST['incident_occured']);
					}
					else
					{
						$stmt->bindValue(":incident",null);
					}
					if(!empty($_POST['repair_notes']))
					{
						$stmt->bindValue(":notes",$_POST['repair_notes']);
					}
					else
					{
						$stmt->bindValue(":notes",null);
					}
					$stmt->execute();
					header("location: repairs.php");
				}
				else
				{
					debug_message("Repair log not added, repair date had no value");
				}
			}
			else
			{
				debug_message("Repair log not added, Equipment ID not an".
				" existing ID in inventory");
			}
		}
		else
		{
			debug_message("Repair log not added, Equipment ID had no value");
		}
	}			

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
		$sql = "SELECT repair_id, repair_date, equipment_id, incident_occured FROM".
		       " repairs;";
		$table = "<div id='constrainer'><div class='hscrolltable'><table ".
		       	 "class='header center'><thead><th style='width:125px'>".
			 "Repair Info</th><th style='width:75px'>Delete?</th><th>".
			 "Equipment ID</th><th>Equipment Name</th>".
			 "<th>Repair Date</th><th>Incident Occurred</th></thead>".
			 "</table>";
		$data = "<div class='body'><table class='center'><tbody>";
		$stmt = $GLOBALS['pdo']->query($sql);
		while($row = $stmt->fetch())
		{
			$data .= "<tr><td style='width:125px'><button style='width:100%'".
			      	 " type='submit' name='repair' value='".$row['repair_id'].
				 "' formaction='repair_info.php'>View/Edit</button></td>";
			$data .= "<td style='width:75px;text-align:center'><input type='".
			      	 "checkbox' name='DELETE_LOGS[]' value='".
				 $row['repair_id']."'>"; 
			$data .= "<td>".$row['equipment_id']."</td>";
			$sql = "SELECT equipment_name FROM equipment WHERE equipment_id ".
			       "= :id;";
			$name_stmt = $GLOBALS['pdo']->prepare($sql);
			$name_stmt->bindValue(":id",$row['equipment_id']);
			$name_stmt->execute();
			$name = $name_stmt->fetch();
			$data .= "<td>".$name['equipment_name']."</td>";
			$data .= "<td>".$row['repair_date']."</td>";
			$data .= "<td>".$row['incident_occured']."</td></tr>";
		}
		$data .= "</tbody></table></div></div></div>";
		return $table.$data;
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
		min-width:700px;
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
<div style='text-align:center'>
<form method='get' action='home.php'>
<button style='width:125px' type='submit' name='home'>Home</button>
</form>
</div>

<h1>Repair Logs</h1>

<div class="container" style="min-width:1000px">
<div class="inputs" style="width:55%;float:right">
<form method="post" action="repairs.php">
<h3 style="text-align:center">Add Repair Log</h3>
<?php echo createInputs(); ?>
</form>
</div>
<form method="post" action="repair_info.php">
<div class="table" style="min-width:1000px;width:50%">
<?php echo createTable(); ?>
</br>
<div style='text-align:center'>
<button type='submit' name='delete' value='delete' formaction='repairs.php'>
Delete Repair Logs
</button>
</div>
</div>
</form>
</div>

<?php tail(); ?>