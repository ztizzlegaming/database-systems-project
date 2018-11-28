<?php
	ini_set("display_errors", 1);
	require("functions.php");

	if (!isLoggedIn()) {
		header('Location: /login.php');
		return;
	}

	head("Equipment Info");
	$pdo = connect_to_psql('tmdatabase');
	echo "<div style='float:left'><form method='get' action='equipment.php'><button type='submit' name='back'>Go Back to Equipment</button></form>";
	echo "</div><div style='width:55%;text-align:left;float:right'><form method='get' action='/'><button style='width:125px' type='submit' name='home'>Home</button>".
	     "</form></div></br>";

	if(isset($_POST['save']))
	{
		$sql = "UPDATE equipment SET equipment_name = :name,".
		       "equipment_sn = :sn,".
		       "equipment_notes = :notes,equipment_tag = :tag,".
		       "equipment_location = :location,equipment_shelf_location = :shelf,".
		       "equipment_updates = :updates,equipment_inventory_update_date".
		       " = :updateDate,equipment_description = :description,".
		       "equipment_modifications = :modifications,equipment_in_out_of_".
		       "service = :inOut,equipment_potential_projects = :projects,".
		       "equipment_tubemaster_value = :tm,equipment_shipping_value = ".
		       ":shipping,equipment_client_value = :client,equipment_weight = ".
		       ":weight,equipment_cost = :cost,equipment_vendor = :vendor,".
		       "equipment_manufacturer = :manufacturer,equipment_date_of_return =".
		       ":dateReturn,equipment_ideal_storage_location = :ideal WHERE ".
		       "equipment_id = ".$_POST['save'].";";
		$stmt = $pdo->prepare($sql);
		if(!empty($_POST['equipment_name']))
		{
			$stmt->bindValue(":name",$_POST['equipment_name']);
			if(!empty($_POST['equipment_sn']))
			{
				$stmt->bindValue(":sn",$_POST['equipment_sn']);
			}
			else
			{
				$stmt->bindValue(":sn",null);
			}
			if(!empty($_POST['equipment_notes']))
			{
				$stmt->bindValue(":notes",$_POST['equipment_notes']);
			}
			else
			{
				$stmt->bindValue(":notes",null);
			}
			$stmt->bindValue(":tag",$_POST['equipment_tag']);
			if(!empty($_POST['equipment_location']))
			{
				$stmt->bindValue(":location",$_POST['equipment_location']);
				if(!empty($_POST['equipment_shelf_location']))
				{
					$stmt->bindValue(":shelf",$_POST['equipment_shelf_location']);
				}
				else
				{
					$stmt->bindValue(":shelf",null);
				}
				if(!empty($_POST['equipment_updates']))
				{
					$stmt->bindValue(":updates",$_POST['equipment_updates']);
				}
				else
				{
					$stmt->bindValue(":updates",null);
				}
				if(!empty($_POST['equipment_inventory_update_date']))
				{
					$stmt->bindValue(":updateDate",$_POST['equipment_inventory_update_date']);
				}
				else
				{
					$stmt->bindValue(":updateDate",null);
				}
				if(!empty($_POST['equipment_description']))
				{
					$stmt->bindValue(":description",$_POST['equipment_description']);
					if(!empty($_POST['equipment_modifications']))
					{
						$stmt->bindValue(":modifications",$_POST['equipment_modifications']);
					}
					else
					{
						$stmt->bindValue(":modifications",null);
					}
					if($_POST['equipment_in_out_service'] === 'In')
					{
						$stmt->bindValue(":inOut",1);
					}
					else
					{
						$stmt->bindValue(":inOut",0);
					}
					if(!empty($_POST['equipment_potential_projects']))
					{
						$stmt->bindValue(":projects",$_POST['equipment_potential_projects']);
					}
					else
					{
						$stmt->bindValue(":projects",null);
					}
					if(!empty($_POST['equipment_tm_value']))
					{
						$stmt->bindValue(":tm",$_POST['equipment_tm_value']);
						if(!empty($_POST['equipment_shipping_value']))
						{
							$stmt->bindValue(":shipping",$_POST['equipment_shipping_value']);
							if(!empty($_POST['equipment_client_value']))
							{
								$stmt->bindValue(":client",$_POST['equipment_client_value']);
							}
							else
							{
								$stmt->bindValue(":client",null);
							}
							if(!empty($_POST['equipment_weight']))
							{
								$stmt->bindValue(":weight",$_POST['equipment_weight']);
								if(!empty($_POST['equipment_cost']))
								{
									$stmt->bindValue(":cost",$_POST['equipment_cost']);
								}
								else
								{
									$stmt->bindValue(":cost",null);
								}
								if(!empty($_POST['equipment_vendor']))
								{
									$stmt->bindValue(":vendor",$_POST['equipment_vendor']);
								}
								else
								{
									$stmt->bindValue(":vendor",null);
								}
								if(!empty($_POST['equipment_manufacturer']))
								{
									$stmt->bindValue(":manufacturer",$_POST['equipment_manufacturer']);
									if(!empty($_POST['equipment_date_of_return']))
									{
										$stmt->bindValue(":dateReturn",$_POST['equipment_date_of_return']);
									}
									else
									{
										$stmt->bindValue(":dateReturn",null);
									}
									if(!empty($_POST['equipment_ideal_storage_location']))
									{
										$stmt->bindValue(":ideal",$_POST['equipment_ideal_storage_location']);
									}
									else
									{
										$stmt->bindValue(":ideal",$_POST['equipment_ideal_storage_location']);
									}
									$stmt->execute();
									if(isset($_POST['subset']))
									{
										if($_POST['subset'] === 'cal_rack')
										{
											$sql = "UPDATE cal_racks SET cal_rack_size = :size WHERE ".
											       "cal_rack_id = :id;";
											$stmt = $pdo->prepare($sql);
											if(!empty($_POST['cal_rack_size']))
											{
												$stmt->bindValue(":size",$_POST['cal_rack_size']);
												$stmt->bindValue(":id",$_POST['save']);
												$stmt->execute();
												header("location: equipment.php");
											}
											else
											{
												debug_message("Assembly/Subassembly info not edited, Cal Rack Size has no value");
											}
										}
										elseif($_POST['subset'] === 'bdd')
										{
											$sql = "UPDATE bdd_racks SET bdd_tube_rack_size = :size WHERE ".
											       "bdd_rack_id = :id;";
											$stmt = $pdo->prepare($sql);
											if(!empty($_POST['bdd_rack_size']))
											{
												$stmt->bindValue(":size",$_POST['bdd_rack_size']);
												$stmt->bindValue(":id",$_POST['save']);
												$stmt->execute();
												header("location: equipment.php");
											}
											else
											{
												debug_message("Assembly/Subassembly info not edited, BDD Tube Rack Size has no value");
											}
										}
										elseif($_POST['subset'] === 'ttd')
										{
											$sql = "UPDATE ttd_racks SET ttd_tube_rack_size = :size WHERE ".
											       "ttd_rack_id = :id;";
											$stmt = $pdo->prepare($sql);
											if(!empty($_POST['ttd_rack_size']))
											{
												$stmt->bindValue(":size",$_POST['ttd_rack_size']);
												$stmt->bindValue(":id",$_POST['save']);
												$stmt->execute();
												header("location: equipment.php");
											}
											else
											{
												debug_message("Assembly/Subassembly info not edited, TTD Tube Rack Size had no value");
											}
										}
										elseif($_POST['subset'] === 'so_set')
										{
											$sql = "UPDATE so_sets SET so_case_number = :case,".
											       "so_size = :size,so_set_label = :label,".
											       "so_number_in_set = :number,so_notes = :notes ".
											       "WHERE so_set_id = :id;";
											$stmt = $pdo->prepare($sql);
											if(!empty($_POST['so_case_number']))
											{
												$stmt->bindValue(":case",$_POST['so_case_number']);
												if(!empty($_POST['so_size']))
												{
													$stmt->bindValue(":size",$_POST['so_size']);
													if(!empty($_POST['so_set_label']))
													{
														$stmt->bindValue(":label",$_POST['so_set_label']);
														if(!empty($_POST['so_number_in_set']))
														{
															$stmt->bindValue(":number",$_POST['so_number_in_set']);
															if(!empty($_POST['so_notes']))
															{
																$stmt->bindValue(":notes",$_POST['so_notes']);
															}
															else
															{
																$stmt->bindValue(":notes",null);
															}
															$stmt->bindValue(":id",$_POST['save']);
															$stmt->execute();
															header("location: equipment.php");
														}
														else
														{
															debug_message("Assembly/Subassembly info not edited, SO Number In Set had no value");
														}
													}
													else
													{
														debug_message("Assembly/Subassembly info not edited, SO Set Label had no value");
													}
												}
												else
												{
													debug_message("Assembly/Subassembly info not edited, SO Size had no value");
												}
											}
											else
											{
												debug_message("Assembly/Subassembly info not edited, SO Case Number had no value");
											}
										}
										elseif($_POST['subset'] === 'cal_or')
										{
											$sql = "UPDATE cal_or_sets SET cal_or_size = :size,cal_or_set_label = :label,".
											       "cal_or_total_number_of_or = :total WHERE cal_or_set_id = :id;";
											$stmt = $pdo->prepare($sql);
											if(!empty($_POST['cal_or_size']))
											{
												$stmt->bindValue(":size",$_POST['cal_or_size']);
												if(!empty($_POST['cal_or_set_label']))
												{
													$stmt->bindValue(":label",$_POST['cal_or_set_label']);
													if(!empty($_POST['cal_or_total_number_of_or']))
													{
														$stmt->bindValue(":total",$_POST['cal_or_total_number_of_or']);
														$stmt->bindValue(":id",$_POST['save']);
														$stmt->execute();
														header("location: equipment.php");
													}
													else
													{
														debug_message("Assembly/Subassembly info not edited, Cal Or Total Number of Or had no value");
													}
												}
												else
												{
													debug_message("Assembly/Subassembly info not edited, Cal Or Set Label had no value");
												}
											}
											else
											{
												debug_message("Assembly/Subassembly info not edited, Cal Or Size had no value");
											}
										}
										elseif($_POST['subset'] === 'ps')
										{
											$sql = "UPDATE pss SET ps_range = :range WHERE ps_id = :id;";
											$stmt = $pdo->prepare($sql);
											if(!empty($_POST['ps_range']))
											{
												$stmt->bindValue(":range",$_POST['ps_range']);
												$stmt->bindValue(":id",$_POST['save']);
												$stmt->execute();
												header("location: equipment.php");
											}
											else
											{
												debug_message("Assembly/Subassembly info not edited, PS Range had no value");
											}
										}
									}
									else
									{
										header("location: equipment.php");
									}
								}
								else
								{
									debug_message("Equipment not edited, manufacturer had no value");
								}
							}
							else
							{
								debug_message("Equipment not edited, weight had no value");
							}
						}
						else
						{
							debug_message("Equipment not edited, shipping value had no value");
						}
					}
					else
					{
						debug_message("Equipment not edited, Tubemaster value had no value");
					}
				}
				else
				{
					debug_message("Equipment not edited, description had no value");
				}
			}
			else
			{
				debug_message("Equipment not edited, location had no value");
			}
		}
		else
		{
			debug_message("Equipment not edited, name had no value");
		}
	}

	function checkIfSubset()
	{
		$sql = "SELECT * FROM bdd_racks WHERE bdd_rack_id = :id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['edit']);
		$stmt->execute();
		if($stmt->rowCount() > 0)
		{
			return array('bdd',$stmt);
		}
		
		$sql = "SELECT * FROM ttd_racks WHERE ttd_rack_id = :id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['edit']);
		$stmt->execute();
		if($stmt->rowCount() > 0)
		{
			return array('ttd',$stmt);
		}

		$sql = "SELECT * FROM cal_racks WHERE cal_rack_id =:id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['edit']);
		$stmt->execute();
		if($stmt->rowCount() > 0)
		{
			return array('cal_rack',$stmt);
		}

		$sql = "SELECT * FROM so_sets WHERE so_set_id =:id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['edit']);
		$stmt->execute();
		if($stmt->rowCount() > 0)
		{
			return array('so_set', $stmt);
		}

		$sql = "SELECT * FROM cal_or_sets WHERE cal_or_set_id =:id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['edit']);
		$stmt->execute();
		if($stmt->rowCount() > 0)
		{
			return array('cal_or', $stmt);
		}

		$sql = "SELECT * FROM pss WHERE ps_id =:id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id", $_POST['edit']);
		$stmt->execute();
		if($stmt->rowCount() > 0)
		{
			return array('ps', $stmt);
		}
		return null;
	}

	function createSubsetInputs()
	{
		$array = checkIfSubset();
		if($array !== null)
		{
			echo "<h3>Assembly/Subassembly Type</h3></br>";
			$row = $array[1]->fetch();
			echo "<input type='hidden' name='subset' value='".$array[0]."'>";
			echo "<input type='hidden' name='edit' value='".$_POST['edit']."'>";
			if ($array[0] === 'cal_rack')
			{
				 $inputs = "<label>Assembly/Subassembly Type:</label><strong>Cal Rack</strong></br>".
				 	   "<label>Cal Rack Size<span style='color:red'>*</span>:</label><input type='text' ".
					   "name='cal_rack_size' value='".$row['cal_rack_size']."' maxlength='20'>";
			}
			elseif($array[0] === 'bdd')
			{
				 $inputs = "<label>Assembly/Subassembly Type:</label><strong>Blowdown Rack (BDD Rack)</strong></br>".
				 	   "<label>BDD Tube Rack Size<span style='color:red'>*</span>:</label><input type='text' ".
					   "name='bdd_rack_size' value='".$row['bdd_tube_rack_size']."' maxlength='20'>";
			}
			elseif($array[0] === 'ttd')
			{
				 $inputs = "<label>Assembly/Subassembly Type:</label><strong>Tube Test Device Rack (TTD Rack)</strong></br>".
				 	   "<label>TTD Tube Rack Size<span style='color:red'>*</span>:</label><input type='text' ".
					   "name='ttd_rack_size' value='".$row['ttd_tube_rack_size']."'  maxlength='20'>";
			}
			elseif($array[0] === 'ps')
			{
				 $inputs = "<label>Assembly/Subassembly Type:</label><strong>Pressure Sensor (PS)</strong></br>".
				 	   "<label>PS Range<span style='color:red'>*</span>:</label><input type='text' ".
					   "name='ps_range' value='".$row['ps_range']."' maxlength='50'>";
			}
			elseif($array[0] === 'so_set')
			{
				 $inputs = "<label>Assembly/Subassembly Type:</label><strong>Supply Orifice Set (SO Set)</strong></br>".
				 	   "<label>SO Case Number<span style='color:red'>*</span>:</label><input type='number' ".
					   "name='so_case_number' min='0' value='".$row['so_case_number']."'></br>".

					   "<label>SO Size<span style='color:red'>*</span>:</label><input type='number' ".
					   "name='so_size' min='0.001' step='0.001' value='".$row['so_size']."'></br>".

					   "<label>SO Set Label<span style='color:red'>*</span>:</label><input type='text' ".
					   "name='so_set_label' value='".$row['so_set_label']."' maxlength='10'></br>".

					   "<label>SO Number In Set<span style='color:red'>*</span>:</label><input type='number' ".
					   "name='so_number' min='0' value='".$row['so_number_in_set']."'></br>".

					   "<label>SO Set Notes:</label><textarea name='so_notes' cols='50' ".
					   "rows='4' maxlength='200'>".$row['so_notes']."</textarea>";
			}
			elseif($array[0] === 'cal_or')
			{
				$inputs = "<label>Assembly/Subassembly Type:</label><strong>Cal Orifice Set (Cal Or Set)</strong></br>".
					  "<label>Cal Or Size<span style='color:red'>*</span>:</label><input type='number' ".
					  "name='cal_or_size' min='0.001' step='0.001' value='".$row['cal_or_size']."'></br>".

					  "<label>Cal Or Set Label<span style='color:red'>*</span>:</label><input type='text' ".
					  "name='cal_or_set_label' value='".$row['cal_or_set_label']."' maxlength='10'></br>".

					  "<label>Cal Or Total Number of Or<span style='color:red'>*</span>:</label><input type='number' ".
					  "name='cal_or_total_number_of_or' min='0' value='".$row['cal_or_total_number_of_or']."'>";
			}
			else
			{
				$inputs = "";
			}
			return $inputs;
		}
	}



			

	function buildRepairTable()
	{
		$sql = "SELECT repair_id, repair_date, incident_occured FROM repairs ".
		       "WHERE equipment_id = :id;";
		$stmt = $GLOBALS['pdo']->prepare($sql);
		$stmt->bindValue(":id",$_POST['edit']);
		$stmt->execute();
		$headers = "<div id='constrainer'><div class='hscrolltable'>".
			   "<table class='header center'><thead>".
			   "<th style='width:125px'>Repair Info</th><th>Repair Date</th>".
			   "<th>Incident Occurred</th></thead></table>";
		$data = "<div class='body'><table class='center'><tbody>";
		while ($row = $stmt->fetch())
		{
			$data .= "<tr><td style='width:125px'>".
			      	 "<button style='width:100%' type='submit' name='repair'".
				 " value='".$row['repair_id']."' formaction='repair_info.php'>".
				 "View/Edit</button></td>";

			$data .= "<td>".$row['repair_date']."</td>";
			$data .= "<td>".$row['incident_occured']."</td></tr>";
		}
		$data .= "</tbody></table></div></div></div>";
		return $headers.$data;
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

                $inputHTML = "<fieldset></br>".
		"<label>Equipment ID:</label>#<strong>".$row['equipment_id']."</strong></br><label>Equipment Name<span style='color:red'>*</span>:</label><input type='text' name=".
                "'equipment_name' value='".$row['equipment_name']."' maxlength='256'>".

                "</br><label>Equipment Serial Number:</label><input type='number' name='equipment_sn' min='0'".
                "value='".$row['equipment_sn']."'>".

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

                "<button style='margin-left:120px' type='submit' name='save' value='".$row['equipment_id']."'>Save Equipment Info</button>".
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
<div class="container" style="width: 100%; min-width:1300px">
<form method='post' action='equipment_info.php'>
<div class="table" style="width:47%;float:right">
<?php echo createSubsetInputs() ?>
</br>
</br>
<hr>
</br>
<?php echo buildRepairTable()?>
</div>
<div class="content">
<?php echo createInputs();?>
</div>
</form>
</div>
<?php tail(); ?>