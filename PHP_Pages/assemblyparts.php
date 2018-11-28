
<?php
  /*
    CSC 410 - Database Systems
    TubeMaster database application

    assembly parts page
    Only the three items below TTD, BDD and Calibration Standard have sub assemblies
    TODO: c
  */
  include "functions.php";
  head("Assembly Parts");
?>

<div class="maincontent" id="maincontent"> 
    <?php
        $equipid = $_GET['equipid'];
        $pdo = connect_to_psql('tmdatabase');   
        echo '<a href="index.php"> Home</a>';
        echo '<a href="equipment.php" style="margin:20px;"> Back to equipment page</a><br>'; 
        if(empty($equipid)){ echo "<p>There is no specific equipment selected.</p>";}
        else{
            // echo '<form method="post"> <button style="margin-top:20px;" name="addnewparts_btn" value="'.$equipid.'">Add new parts</button></form>';
            echo "<h2>Assembly Parts for Equipment #$equipid</h2>";
            $currparts = fetchpartsfromdb($pdo, $equipid);
            displayallequipments($pdo, $equipid, $currparts);
            if(array_key_exists('confirm_addpart',$_POST)){ 
                $selection = $_POST[selectedequip];
                if(sizeof($selection)!==0){
                    addnewpart($pdo, $equipid, $selection); 
                }
            }
            if(array_key_exists('confirm_deleteparts',$_POST)){ 
                $selection_del = $_POST[selectedequip_del];
                if(sizeof($selection_del)!==0){
                    deletepart($pdo, $equipid, $selection_del); 
                }
            }


        }
        

    ?>
</div>
<?php
    function alert($msg) {
        echo "<script type='text/javascript'>alert('$msg');</script>";
    }
    function deletepart($pdo, $equipid, $selection){
        foreach($selection as $curr){
            $inputdata = [
                ':assembly_id' => $equipid,
                ':part_id' => $curr,
              ]; 
            $sqlquery = "DELETE FROM assembly_parts WHERE assembly_id = :assembly_id and part_id = :part_id;";
            // echo $sqlquery;
            
            try { 
                $pdo->prepare($sqlquery)->execute($inputdata);  
                echo "<meta http-equiv='refresh' content='0'>";    
              } catch (\PDOException $e) {
                debug_message('$sql = ' . $sql);
                debug_message('ERROR: Cannot add course:');
                debug_message('MySQL Error Message: ' . $e);
                // throw new \PDOException($e->getMessage(), (int)$e->getCode());
              } 

        }
    }

    function fetchpartsfromdb($pdo, $equipid){
        $result = $pdo->query("SELECT part_id FROM assembly_parts WHERE assembly_id = $equipid;");
        $currparts = [];
        echo '<form method = "post">';
        foreach($result as $row){
            array_push($currparts, $row['part_id']);
            $currname = findnameforequipid($pdo, $row['part_id']);
            // $viewstring = '<button id="viewequip_btn" name="viewequip_btn" value="';
            // $viewstring .= $row['part_id'];
            // $viewstring .= '">View/Edit</button>';

            echo '<input type="checkbox"  name="selectedequip_del[]" value="' . $row['part_id'] . '">';
            echo '<a href="#">'; //TODO: link to the equipinfo page
            echo ' #'.$row['part_id'].' '.$currname;
            echo '</a>';
            // echo $viewstring;
    
        }
        echo '<br><input style="margin-top:10px;" type="submit" name="confirm_deleteparts" value="delete selected"> ';
        echo '</form>';
        echo '<hr>';
        return $currparts;
    }

    function addnewpart($pdo, $equipid,$selection){
        // alert ($equipid);
        foreach($selection as $row){
            // echo $row;
            $inputdata = [
                ':assembly_id' => $equipid,
                ':part_id' => $row,
            ];
            $sql = 'INSERT INTO assembly_parts ';
            $sql .= 'VALUES ';
            $sql .= '(:assembly_id,:part_id)';
            try { 
                $pdo->prepare($sql)->execute($inputdata);
                // alert('Record added successfully');
                //refresh the page to update the display table
                echo "<meta http-equiv='refresh' content='0'>";
                
              } catch (\PDOException $e) {
                debug_message('$sql = ' . $sql);
                debug_message('ERROR: Cannot add course:');
                debug_message('MySQL Error Message: ' . $e);
                // throw new \PDOException($e->getMessage(), (int)$e->getCode());
              } 

        }

    }  

    function displayallequipments($pdo, $equipid, $currparts){
        // echo sizeof($currparts);
        echo '<p>Here are all the available equipment:</p>';

        $result = $pdo->query("SELECT equipment_id FROM equipment;");
        echo '<form method = "post">';
        $allequip = [];
        array_push($currparts, $equipid);
        foreach($result as $row){
            array_push($allequip, $row['equipment_id']);
        }
        // echo sizeof($currparts);
        // echo sizeof($allequip);
        if(sizeof($currparts) === sizeof($allequip)){
            echo '<p>Unfortunately, there is no available assembly that you could add as parts</p>';
        }
        else{
            foreach($allequip as $equip){
                if(!in_array($equip, $currparts)){
                    $equipname = findnameforequipid($pdo, $equip);
                    //assuming an equip can add itself as parts
                    echo '<input style="margin-left:10px;" type="checkbox"  name="selectedequip[]" value="' . $equip . '">';
                    echo '<a href = "#">'; //TODO link to the equip info page
                    echo '#'. $equip . " ".$equipname;
                    echo '</a>';
                }
            }
        }

        echo '<br><input type="submit" name="confirm_addpart" value="add Selection as parts" style="margin-top:10px;">';
        echo "</form>";
    }

    function findnameforequipid($pdo, $equipid){
        $sqlquery = "SELECT equipment_name FROM equipment WHERE equipment_id = '$equipid';";
        // echo $sqlquery . "\n";
        $result = $pdo->query($sqlquery);
        $row = $result->fetch(PDO::FETCH_NUM); //equals foreach($stmt as $row)...
        $equipname = $row[0];
        return $equipname;
    }

?>

<?php tail(); ?>