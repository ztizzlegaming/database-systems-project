<?php
	ini_set("display_errors", 1);
	require("functions.php");
	head("Equipment Info");
	$pdo = connect_to_psql('tmdatabase');
	echo "<form method='get' action='equipment.php'><button type='submit' name='back'>Go Back to Equipment</button></form>";

	function buildRepairTable()
	{
		return "I will build the equipment's repair logs here";
	}

	function queryEquipment()
	{
		$sql = "SELECT * FROM equipment WHERE equipment_id = :id;";
		$query = $GLOBALS['pdo']->prepare($sql);
		$query->bindValue(":id", $_POST['edit']);
		$query->execute();
		return $query;
	}

	function getSelectedValue($value, $choice)
	{
		if($value === $choice)
		{
			return "selected";
		}
		else
		{
			return "";
		}
	}
	
	##Creates text inputs and labels for adding a piece of equipment
	function createInputs()
	{
		$stmt = queryEquipment();
		$row = $stmt->fetch();

                $inputHTML = "<fieldset></br><form  method='post' action='equipment.php'>".
		"<label>Equipment ID:</label>#<strong>".$row['equipment_id']."</strong></br><label>Equipment Name<span style='color:red'>*</span>:</label><input type='text' name=".
                "'equipment_name' value='".$row['equipment_name']."' maxlength='256'>".

                "</br><label>Equipment Serial Number:</label><input type='number' name='equipment_sn' min='0'".
                "value='".$row['equipment_sn']."'>".

                "</br><label>Equipment Quantity<span style='color:red'>*</span>:</label><input type='number' min='0' name='equipment_quantity' ".
                "value='".$row['equipment_quantity']."'>".

                "</br><label>Equipment Notes:</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_notes' ".
                "cols='50' rows='10' maxlength='200'>".$row['equipment_notes']."</textarea>".

		"</br><label>Equipment Tag<span style='color:red'>*</span>:</label><select name='equipment_tag'><option value='Red' ".
                getSelectedValue($row['equipment_tag'], 'Red').">Red</option><option value='Blue'".getSelectedValue($row['equipment_tag'], 'Blue').
                ">Blue</option><option value='Green' ".getSelectedValue($row['equipment_tag'], 'Green').">Green</option><option value='N/A' ".
		getSelectedValue($row['equipment_tag'], 'N/A').">N/A</option></select></br>".

                "<label>Equipment Location<span style='color:red'>*</span>:</label><input type='text' name='equipment_location' value='".
                $row['equipment_location']."' maxlength='100'>".

                "</br><label>Equipment Shelf Location:</label><input type='text'".
		"name='equipment_shelf_location' value='".$row['equipment_shelf_location']."' maxlength='100'>".

                "</br><label>Equipment Updates:</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_updates' cols='50' rows='10' maxlength='256'>".
                $row['equipment_updates']."</textarea></br>".

                "<label>Equipment Inventory Update Date:</label><input type='date' name='equipment_inventory_update_date' value='".
                $row['equipment_inventory_update_date']."'>".

                "</br><label>Equipment Description<span style='color:red'>*</span>:".
		"</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_description'".
		" cols='50' rows='10' maxlength='500'>".$row['equipment_description']."</textarea>".

                "</br><label>Equipment Modifications:</label>".
		"<textarea style='vertical-align:top;margin-bottom:10px' name='equipment_modifications' cols='50' rows='10' maxlength='256'>".
                $row['equipment_modifications']."</textarea>".

                "</br><label>Equipment In/Out Of Service".
		"<span style='color:red'>*</span>:</label><select name='equipment_in_out_service'><option value='In' ".
                getSelectedValue($row['equipment_in_out_of_service'], 'In').">In</option><option value='Out' ".
                getSelectedValue($row['equipment_in_out_of_service'], 'Out').">Out</option></select></br>".

                "<label>Equipment Potential Projects:</label><textarea style='vertical-align:top;margin-bottom:10px'".
		" name='equipment_potential_projects' cols='50' rows='10' maxlength='1000'>".
                $row['equipment_potential_projects']."</textarea></br>".

                "<label>Equipment TM $ Value<span style='color:red'>*</span>:</label>".
		"<input type='number' name='equipment_tm_value' value='".$row['equipment_tubemaster_value'].
		"'min='0.01' step='0.01'></br>".

                "<label>Equipment Shipping $ Value<span style='color:red'>*</span>:</label>".
		"<input type='number' name='equipment_shipping_value' value='".$row['equipment_shipping_value'].
		"' min='0.01' step='0.01'></br>".

                "<label>Equipment Client $ Value:</label>".
		"<input type='number' name='equipment_client_value' value='".$row['equipment_client_value'].
		"' min='0.01' step='0.01'></br>".

                "<label>Equipment Weight(lbs)<span style='color:red'>*</span>:</label>".
		"<input type='number' name='equipment_weight' value='".$row['equipment_weight'].
		"'></br>".

                "<label>Equipment Cost:</label>".
		"<input type='number' name='equipment_cost' value='".$row['equipment_cost'].
		"' min='0.01' step='0.01'></br>".

                "<label>Equipment Vendor:</label><input type='text' name='equipment_vendor' value='".
                $row['equipment_vendor']."' maxlength='100'></br>".

                "<label>Equipment Manufacturer<span style='color:red'>*</span>:</label>".
		"<input type='text' name='equipment_manufacturer' value='".$row['equipment_manufacturer'].
		"' maxlength='100'></br>".

                "<label>Equipment Date Of Return:</label><input type='date' name='equipment_date_of_return' value='".
                $row['equipment_date_of_return']."'></br>".

                "<label>Equipment Ideal Storage Location:</label>".
		"<input type='text' name='equipment_ideal_storage_location' value='".
		$row['equipment_ideal_storage_location']."' maxlength='100'></br></br>".

                "<button style='margin-left:120px' type='submit' name='edit_equipment' value='".$row['equipment_id']."'>Save Equipment Info</button>".
		"</br></br></fieldset></form>";
		return $inputHTML;
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
																																   


<h1>Equipment Info</h1>
<div class="container" style="width: 100%;">
<div class="table" style="width:45%;float:right">
<?php echo buildRepairTable()?>
</div>
<div class="content">
<?php echo createInputs();?>
</div>
</div>
<?php tail(); ?>