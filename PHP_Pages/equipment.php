<?php
	ini_set('display_errors', 1);
	require("functions.php");
	head("Equipment Inventory");
	$success = false;
	
	##Sets default value of add equipment inputs due to a successful/failed addition
	function getInputValue($postName)
	{
		if(isset($_POST[$postName]) && !$GLOBALS['success'])
		{
			return $_POST[$postName];
		}
		else
		{
			return "";
		}
	}

	function setSuccess($success)
	{
		$GLOBALS['success'] = $success;
	}

	##Same as above, but for drop-down boxes
	function getSelectedValue($postName, $nameValue)
	{
		if(isset($_POST[$postName]) && !$GLOBALS['success'])
		{
			if($_POST[$postName] === $nameValue)
			{
				return "selected";
			}
		}
		return "";
	}

	##Creates text inputs and labels for adding a piece of equipment
	function createAddInputs()
	{
		
		$inputHTML = "<fieldset></br><form name='add_equipment' method='post' action='equipment.php'>".

		"<label>Equipment Name<span style='color:red'>*</span>:</label><input type='text' name=".
		"'equipment_name' value='".getInputValue('equipment_name')."' maxlength='256'>".

		"</br><label>Equipment Serial Number:</label><input type='number' name='equipment_sn' min='0'".
		"value='".getInputValue('equipment_sn')."'>".

		"</br><label>Equipment Quantity<span style='color:red'>*</span>:</label><input type='number' min='0' name='equipment_quantity' ".
		"value='".getInputValue('equipment_quantity')."'>".

		"</br><label>Equipment Notes:</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_notes' ".
		"cols='50' rows='10' maxlength='200'>".getInputValue('equipment_notes')."</textarea>".

		"</br><label>Equipment Tag<span style='color:red'>*</span>:</label><select name='equipment_tag'><option value='Red' ".
		getSelectedValue('equipment_tag', 'Red').">Red</option><option value='Blue' ".getSelectedValue('equipment_tag', 'Blue').
		">Blue</option><option value='Green' ".getSelectedValue('equipment_tag', 'Green').">Green</option><option value='N/A' ".
		getSelectedValue('equipment_tag', 'N/A').">N/A</option></select></br>".

		"<label>Equipment Location<span style='color:red'>*</span>:</label><input type='text' name='equipment_location' value='".
		getInputValue('equipment_location')."' maxlength='100'>".

		"</br><label>Equipment Shelf Location:</label><input type='text'".
		"name='equipment_shelf_location' value='".getInputValue('equipment_shelf_location')."' maxlength='100'>".

		"</br><label>Equipment Updates:</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_updates' cols='50' rows='10' maxlength='256'>".
		getInputValue('equipment_updates')."</textarea></br>".

		"<label>Equipment Inventory Update Date:</label><input type='date' name='equipment_inventory_update_date' value='".
		getInputValue('equipment_inventory_update_date')."'>".

		"</br><label>Equipment Description<span style='color:red'>*</span>:".
		"</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_description'".
		" cols='50' rows='10' maxlength='500'>".getInputValue('equipment_description')."</textarea>".

		"</br><label>Equipment Modifications:</label>".
		"<textarea style='vertical-align:top;margin-bottom:10px' name='equipment_modifications' cols='50' rows='10' maxlength='256'>".
		getInputValue('equipment_modifications')."</textarea>".

		"</br><label>Equipment In/Out Of Service".
		"<span style='color:red'>*</span>:</label><select name='equipment_in_out_service'><option value='In' ".
		getSelectedValue('equipment_in_out_of_service', 'In').">In</option><option value='Out' ".
		getSelectedValue('equipment_in_out_of_service', 'Out').">Out</option></select></br>".
		
		"<label>Equipment Potential Projects:</label><textarea style='vertical-align:top;margin-bottom:10px' name='equipment_potential_projects' cols='50' rows='10' maxlength='1000'>".
		getInputValue('equipment_potential_projects')."</textarea></br>".

		"<label>Equipment TM $ Value<span style='color:red'>*</span>:</label>".
		"<input type='number' name='equipment_tm_value' value='".getInputValue('equipment_tm_value').
		"'min='0.01' step='0.01'></br>".

		"<label>Equipment Shipping $ Value<span style='color:red'>*</span>:</label>".
		"<input type='number' name='equipment_shipping_value' value='".getInputValue('equipment_shipping_value').
		"' min='0.01' step='0.01'></br>".

		"<label>Equipment Client $ Value:</label>".
		"<input type='number' name='equipment_client_value' value='".getInputValue('equipment_client_value').
		"' min='0.01' step='0.01'></br>".

		"<label>Equipment Weight(lbs)<span style='color:red'>*</span>:</label>".
		"<input type='number' name='equipment_weight' value='".getInputValue('equipment_weight').
		"'></br>".

		"<label>Equipment Cost:</label>".
		"<input type='number' name='equipment_cost' value='".getInputValue('equipment_cost').
		"' min='0.01' step='0.01'></br>".

		"<label>Equipment Vendor:</label><input type='text' name='equipment_vendor' value='".
		getInputValue('equipment_vendor')."' maxlength='100'></br>".

		"<label>Equipment Manufacturer<span style='color:red'>*</span>:</label>".
		"<input type='text' name='equipment_manufacturer' value='".getInputValue('equipment_manufacturer').
		"' maxlength='100'></br>".

		"<label>Equipment Date Of Return:</label><input type='date' name='equipment_date_of_return' value='".
		getInputValue('equipment_date_of_return')."'></br>".

		"<label>Equipment Ideal Storage Location:</label>".
		"<input type='text' name='equipment_ideal_storage_location' value='".
		getInputValue('equipment_ideal_storage_location')."' maxlength='100'></br></br>".

		"<button style='margin-left:120px' type='submit' name='add_equipment'>Add New Equipment</button>".
		"</br></br></fieldset>";
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
		width:100%;
		margin:0px;
	}
</style>
				
<h1 style='font: 20px'>Equipment</h1>
<?php
	$pdo = connect_to_psql('tmdatabase');

	//Handles addition of equipment
	if(isset($_POST['add_equipment']))
	{
		$sn = null;
		$notes = null;
		$shelfLocation = null;
		$updates = null;
		$update_date = null;
		$modification = null;
		$projects = null;
		$clientValue = null;
		$cost = null;
		$vendor = null;
		$returnDate = null;
		$ideal = null;
		if(!empty($_POST['equipment_name']))
		{
			$name = $_POST['equipment_name'];
			if(!empty($_POST['equipment_quantity']))
			{
				$quantity = $_POST['equipment_quantity'];
				if(!empty($_POST['equipment_location']))
				{
					$location = $_POST['equipment_location'];
					if(!empty($_POST['equipment_description']))
					{
						$description = $_POST['equipment_description'];
						if(!empty($_POST['equipment_tm_value']))
						{
							$tmValue = $_POST['equipment_tm_value'];
							if(!empty($_POST['equipment_shipping_value']))
							{
								$shippingValue = $_POST['equipment_shipping_value'];
								if(!empty($_POST['equipment_weight']))
								{
									$weight = $_POST['equipment_weight'];
									if(!empty($_POST['equipment_manufacturer']))
									{
										$manufacturer = $_POST['equipment_manufacturer'];
										if(!empty($_POST['equipment_sn']))
										{
											$sn = $_POST['equipment_sn'];
										}
										if(!empty($_POST['equipment_notes']))
										{
											$notes = $_POST['equipment_notes'];
										}
										if(!empty($_POST['equipment_shelf_location']))
										{
											$shelfLocation = $_POST['equipment_shelf_location'];
										}
										if(!empty($_POST['equipment_updates']))
										{
											$updates = $_POST['equipment_updates'];
										}
										if(!empty($_POST['equipment_inventory_update_date']))
										{
											$update_date = $_POST['equipment_inventory_update_date'];
										}
										if(!empty($_POST['equipment_modifications']))
										{
											$modification = $_POST['equipment_modifications'];
										}
										if(!empty($_POST['equipment_potential_projects']))
										{
											$projects = $_POST['equipment_potential_projects'];
										}
										if(!empty($_POST['equipment_client_value']))
										{
											$clientValue = $_POST['equipment_client_value'];
										}
										if(!empty($_POST['equipment_cost']))
										{
											$cost = $_POST['equipment_cost'];
										}
										if(!empty($_POST['equipment_vendor']))
										{
											$vendor = $_POST['equipment_vendor'];
										}
										if(!empty($_POST['equipment_date_of_return']))
										{
											$returnDate = $_POST['equipment_date_of_return'];
										}
										if(!empty($_POST['equipment_ideal_storage_location']))
										{
											$ideal = $_POST['equipment_ideal_storage_location'];
										}
										$sql = "INSERT INTO equipment (equipment_name,equipment_sn,".
										"equipment_quantity,equipment_notes,equipment_tag,equipment_location,".
										"equipment_shelf_location,equipment_updates,equipment_inventory_update_date,".
										"equipment_description,equipment_modifications,equipment_in_out_of_service,".
										"equipment_potential_projects,equipment_tubemaster_value,equipment_shipping_value,".
										"equipment_client_value,equipment_weight,equipment_cost,equipment_vendor,".
										"equipment_manufacturer,equipment_date_of_return,equipment_ideal_storage_location)VALUES".
										"(:name,:sn,:quantity,:notes,:tag,:location,:shelf,:updates,".
										":updateDate,:description,:modifications,:inOut,:projects,".
										":TMValue,:shipping,:client,:weight,:cost,:vendor,".
										":manufacturer,:returnDate,:ideal);";

										$stmt = $pdo->prepare($sql);
										$stmt->bindValue(":name",$name);
										$stmt->bindValue(":sn",$sn);
										$stmt->bindValue(":quantity",$quantity);
										$stmt->bindValue(":notes",$notes);
										$stmt->bindValue(":tag",$_POST['equipment_tag']);
										$stmt->bindValue(":location",$location);
										$stmt->bindValue(":shelf",$shelfLocation);
										$stmt->bindValue(":updates",$updates);
										$stmt->bindValue(":updateDate",$update_date);
										$stmt->bindValue(":description",$description);
										$stmt->bindValue(":modifications",$modification);
										if($_POST['equipment_in_out_service'] === 'In')
										{
											$stmt->bindValue(":inOut",1);
										}
										else
										{
											$stmt->bindValue(":inOut",0);
										}
										$stmt->bindValue(":projects",$projects);
										$stmt->bindValue(":TMValue",$tmValue);
										$stmt->bindValue(":shipping",$shippingValue);
										$stmt->bindValue(":client",$clientValue);
										$stmt->bindValue(":weight",$weight);
										$stmt->bindValue(":cost",$cost);
										$stmt->bindValue(":vendor",$vendor);
										$stmt->bindValue(":manufacturer",$manufacturer);
										$stmt->bindValue(":returnDate",$returnDate);
										$stmt->bindValue(":ideal",$ideal);
										$stmt->execute();
										setSuccess(true);
										header('location: equipment.php');
									}
									else
									{
										debug_message("Equipment not added, no manufacturer given");
										setSuccess(false);
									}
								}
								else
								{
									debug_message("Equipment not added, no weight given");
									setSuccess(false);
								}
							}
							else
							{
								debug_message("Equipment not added, no shipping value given");
								setSuccess(false);
							}
						}
						else
						{
							debug_message("Equipment not added, no TM value was given");
							setSuccess(false);
						}
					}
					else
					{
						debug_message("Equipment not added, no description was given");
						setSuccess(false);
					}
				}
				else
				{
					debug_message("Equipment not added, no location was given");
					setSuccess(false);
				}
			}
			else
			{
				debug_message("Equipment not added, no quantity was given");
				setSuccess(false);
			}
		}
		else
		{
			debug_message("Equipment not added, no name was given");
			setSuccess(false);
		}
	}
	
	//Handles deletion of equipment
	if(isset($_POST['delete']))
	{
		if(isset($_POST['DELETE_IDS']))
		{
			$id_array = $_POST['DELETE_IDS'];
			$curr_id = 0;
			try
			{
				$sql = "DELETE FROM equipment WHERE equipment_id = :id;";
				foreach($id_array as $key => $id)
				{
					$curr_id = $id;
					$stmt = $pdo->prepare($sql);
					$stmt->bindValue(':id', $id);
					$stmt->execute();
				}
				header('location: equipment.php');
			}
			catch(PDOException $e)
			{
				if($e->getCode() === "23503")
				{
					echo "Equipment with ID ".$curr_id." was not deleted. Make sure this equipment is also deleted from the following tables:";
					echo "<ul>Repairs</ul>";
				}
				else
				{
					throw $e;
				}
			}
		}
		else
		{
			debug_message("No equipment was selected for deletion.");
		}
	}

	$sql = "SELECT * FROM equipment;";
	$stmt = $pdo->query($sql);
	echo "<form method='post' action='equipment.php'>";
	echo "<div id='constrainer'>";
	echo "<div class='hscrolltable'>";
	echo "<table class='header center'>";
	echo "<thead>";
	echo "<th style='width:125px'>Equipment Info</th><th style='width:75px'>Delete?</th><th>ID</th><th>Name</th><th>Serial Number</th>";
	echo "<th>Quantity</th><th>Notes</th><th>Tag</th><th>Location</th>";
	echo "<th>Shelf Location</th><th>Updates</th>";
	echo "<th>Inventory Update Date</th><th>Description</th>";
	echo "<th>Modifications</th><th>In/Out Of Service</th>";
	echo "<th>Potential Projects</th><th>TM $ Value</th>";
	echo "<th>Shipping $ Value</th><th>Client $ Value</th>";
	echo "<th>Weight</th><th>Cost</th><th>Vendor</th><th>Manufacturer</th>";
	echo "<th>Date Of Return</th><th>Ideal Storage Location</th>";
	echo "</thead>";
	echo "</table>";
	
	echo "<div class='body'>";
	echo "<table class='center'>";
     	echo "<tbody>";
	while ($row = $stmt->fetch())
	{
		echo "<tr><td style='width:125px'>";
		echo "<button style='width: 100%' formaction='equipment_info.php' type='submit' name='edit' value='".$row['equipment_id']."'>";
		echo "View/Edit</button></td>";
		
		echo "<td style='width:75px;text-align:center'>";
		echo "<input type='checkbox' name='DELETE_IDS[]' value='".$row['equipment_id']."'>";
		
		echo "<td>".$row['equipment_id']."</td>";
		echo "<td>".$row['equipment_name']."</td>";
		echo "<td>".$row['equipment_sn']."</td>";
		echo "<td>".$row['equipment_quantity']."</td>";
		echo "<td>".$row['equipment_notes']."</td>";
		echo "<td>".$row['equipment_tag']."</td>";
		echo "<td>".$row['equipment_location']."</td>";
		echo "<td>".$row['equipment_shelf_location']."</td>";
		echo "<td>".$row['equipment_updates']."</td>";
		echo "<td>".$row['equipment_inventory_update_date']."</td>";
		echo "<td>".$row['equipment_description']."</td>";
		echo "<td>".$row['equipment_modifications']."</td>";
		if($row['equipment_in_out_of_service'] === 1)
		{
			echo "<td>In</td>";
		}
		else
		{
			echo "<td>Out</td>";
		}
		echo "<td>".$row['equipment_potential_projects']."</td>";
		echo "<td>".$row['equipment_tubemaster_value']."</td>";
		echo "<td>".$row['equipment_shipping_value']."</td>";
		echo "<td>".$row['equipment_client_value']."</td>";
		echo "<td>".$row['equipment_weight']."</td>";
		echo "<td>".$row['equipment_cost']."</td>";
		echo "<td>".$row['equipment_vendor']."</td>";
		echo "<td>".$row['equipment_manufacturer']."</td>";
		echo "<td>".$row['equipment_date_of_return']."</td>";
		echo "<td>".$row['equipment_ideal_storage_location']."</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
?>
</br>
<button name='delete' value='delete'>Delete Equipment</button>
</form>
<hr>
<h2 style='font: 20px'>Add New Equipment</h2>
<form method='post' action='equipment.php'>
<?php echo createAddInputs();?>
</form>
<?php
	tail();
?>