<style>
        h1{
          margin-top: 15px;
        }
        .maincontent{
            padding: 0px 170px;
            transition: 0.2s;
        }
        table, th, td {
            margin-top:10px;
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 6px 8px;
            text-align: left;
        }
        input[type=text] {
            padding:5px; 
            border:2px solid #ccc; 
            -webkit-border-radius: 5px;
            border-radius: 5px;
        }
        input[type=text]:focus { border-color:#333;}
        input[type="submit"]:hover, button:hover { opacity: 1; color: #999;}
        input[type=submit], button {
            /* border-color: coral; */
            margin-left: 5px;
            font-size: 13px;
            width:140px; 
            height:25px;
            color : white;
            background:#2b6541; 
            cursor:pointer;
            border-radius: 3px; 
            transition: 0.2s;
            opacity: 0.9;
        }
        .sidenav {
            height: 100%;
            width: 160px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #2b6541;
            overflow-x: hidden;
            transition: 0.2s;
            padding-top: 60px;
        }

        .sidenav a,a {
            padding: 3px 8px 3px 30px;
            text-decoration: none;
            font-size: 20px;
            color: white;
            display: block;
            transition: 0.1s;
        }
        a:hover { color: #999; }
        .sidenav .closebtn, .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }
  
        .functionbtn {margin-top: 25px}
        @media screen and (max-height: 450px) {
          .sidenav {padding-top: 15px;}
          .sidenav a {font-size: 18px;}
        }

        .addproject_maincontent{
            /* background: #2b6541; */
            /* padding: 10px 40px; */
            width:500px;
            /* display: none;    !! control display */
            border: none;
            /* margin: 0px 0 22px 0; */
            margin: 0 auto;
            position:absolute;
            transition:0.2s;
        }
        .addproject_maincontent h2{color: white;}
        /* Full-width input fields */
        .addproject_maincontent input[type=text], .addproject_maincontent input[type=date]{
          /* width: 100%; */
          padding: 10px;
          margin: 5px 0 15px 0;
          width: 100%;
          display: inline-block;
          border: none;
          background: #f1f1f1;
          /* background-color: #ddd; */
          outline: none;
        }
        .newproject_confirmbtn{ margin:15px 0px;}
        label, .addproject_maincontent span {color:#2b6541}

</style>
<?php
  /*
    CSC 410 - Database Systems
    TubeMaster database application

    add new Project Page 
  */
  include "functions.php";
  head("Add New Projects");
  
?>


<!-- https://www.w3schools.com/howto/howto_js_sidenav.asp -->
<script>
// javascript function to help unfold manu
function openNav() {
  document.getElementById("mySidenav").style.width = "160px";
  document.getElementById("maincontent").style.paddingLeft = "170px";
}
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("maincontent").style.paddingLeft = "20px";
}
</script>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="index.php">< Home</a>
  <a href="all_projects.php">< Back</a>
</div>
<span style="font-size:20px;cursor:pointer" onclick="openNav()">&#9776; open</span>

<div class="maincontent" id="maincontent"> 
  <?php 
    $pdo = connect_to_psql('tmdatabase');
    $projid = $_GET['projectid'];
    viewproject($pdo, $projid);

    //add new project to the database
    if(isset($_POST['confirm_newproject'])){
      //TODO: check constraint: if start date is earliest first!!
      $starttime = strtotime($_POST['startdate']);
      $endtime1 = strtotime($_POST['enddate']);
      $endtime2 = strtotime($_POST['shipdate']);
      $canquery = false;
      if($_POST['enddate'] != ""){
        if($starttime - $endtime1 > 0 || $starttime - $endtime2 > 0){
          echo "!!please input again. The project's start time needs to be the earliest."; 
          //TODO: somehow the alert not showing up - could it be checked at the input state?
          alert("please input again. The project's start time needs to be the earliest.");
        }
        else {$canquery= true;}
      }
      if($_POST['enddate'] == ""){
        if($starttime - $endtime2 > 0){
          echo "~please input again. The project's start time needs to be the earliest."; 
          //TODO: somehow the alert not showing up - could it be checked at the input state?
          alert("please input again. The project's start time needs to be the earliest.");
        }
        else {$canquery= true;}
      }
      if($canquery){
        $inputdata = [
          ':reactor_id' => $_POST['selection_reactorid'],
          ':project_start_date' => $_POST['startdate'],
          ':project_expected_end_date'  => $_POST['enddate'],
          ':project_equipment_ship_date' => $_POST['shipdate'],
          ':project_type' => $_POST['projecttype'],
          ':project_testing_type' => $_POST['testtype'],
          ':project_is_active' => $_POST['radio'],
        ];
        updatenewproject($pdo, $inputdata, $projid);   
      }
    }

  ?>
</div>

<?php 
  function viewproject($pdo, $projid){
    echo "<h1>View/ Edit Project $projid</h1>";
    $projectinfo = $pdo->query("SELECT * FROM projects WHERE project_id = $projid;");
    $row = $projectinfo->fetch(PDO::FETCH_NUM);
    $projid = $row[0];
    $reactor_id = $row[7];
    $project_start_date = $row[1];
    $project_expected_end_date = $row[2];
    $project_equipment_ship_date = $row[3];
    $project_type = $row[4];
    $project_testing_type = $row[5];
    $project_is_active = $row[6];

    $htmldisplay1 = <<< "HTML"
  <form method="post">
  <div class="addproject_maincontent" id="addproject_maincontent"> 
    <label>Start Date: *</label>
HTML;
    echo $htmldisplay1;
    echo '<input required type="date" min="2000-01-01" max="3000-01-01" name="startdate"'. "value=\"$project_start_date\">";
    echo '<label>Equipment Ship Date: *</label>';
    echo '<input required type="date" min="2000-01-01" max="3000-01-01" name="shipdate"'. "value=\"$project_equipment_ship_date\">";
    echo  '<label>Expected End Date: </label>';
    echo  '<input type="date" min="2000-01-01" max="3000-01-01" name="enddate"' ."value=\"$project_expected_end_date\">";
    echo  '<label>Project Type: </label>'; 
    echo  '<input type="text" name="projecttype"'."value=\"$project_type\">";
    echo  '<label>Testing Type: </label>';
    echo  '<input type="text" name="testtype"'."value=\"$project_testing_type\">";
    echo  '<p><label>Reactor ID Linked: *  </label><br>';
    display_allreactors($pdo, $reactor_id);
    echo  '<p><label>Status: </label>';
    displaystatus($project_is_active);
    $htmldisplay2 = <<< "HTML"
    <div class="newproject_confirmbtn">
      <input type="submit" name="confirm_newproject" value="Confirm Change">
    </div>
    </div>
  </form>
HTML;
    echo $htmldisplay2;
  }

  //display the radio input showing the status of the project
  function displaystatus($project_is_active){
    if($project_is_active == 't'){
      echo  '<input required checked type="radio" name="radio" value="t"><span>Active</span>';
      echo  '<input type="radio" name="radio" value="f"><span>Inactive</span></p>';
    }
    else{
      echo  '<input type="radio" name="radio" value="t"><span>Active</span>';
      echo  '<input required checked type="radio" name="radio" value="f"><span>Inactive</span></p>';
    }
   
  }

  //param: $pdo: the database linked to, $default: the default reactor id that want to show
  //shows all the reactor id options in a drop down selection box
  function display_allreactors($pdo, $default){
    $result = $pdo->query("SELECT reactor_id FROM reactors;");
    echo '<select name = "selection_reactorid">';
    foreach ($result as $row){
      dm($row);
      //select the match as default
      if( $row['reactor_id'] == $default){
        echo "<option selected value=\"" . $row['reactor_id'] . "\"> ". $row['reactor_id'] . "</option>";
      }
      else{
        echo "<option value=\"" . $row['reactor_id'] . "\"> ". $row['reactor_id'] . "</option>";
      }
    }
    echo "</select></p>";
  }


  function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
  }

  //update the new project input to the database
  function updatenewproject($pdo, $inputdata, $projid){
    $validcolumn = [];
    foreach($inputdata as $key => $value) {
      if($value !== ""){
        $validcolumn[$key] = $value;
      }
    }
    // print_r($validcolumn);
    foreach($validcolumn as $columname => $info){
      $subcolumname = substr("$columname", 1);
      $sql = 'UPDATE projects '
            . "SET $subcolumname = $columname "
            . "WHERE project_id = $projid ;";
      try { 
        // reference: http://www.postgresqltutorial.com/postgresql-php/update/
        $stmt = $pdo->prepare($sql);
        // bind values to the statement
        $stmt->bindValue($columname, $info);
        // update data in the database
        $stmt->execute();
        // return the number of row affected
        // echo $stmt->rowCount();
        //go back to the all info page
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=all_projects.php?projectid=' . $projid . '">'; ; 
      } catch (\PDOException $e) {
        echo $sql;
        // throw new \PDOException($e->getMessage(), (int)$e->getCode());
      } 
   
    }

  
    // try { 
    //   $pdo->prepare($sql)->execute($finalinput);
    //   // alert('Record added successfully');
    //   //refresh the page to update the display table
    //   // echo "<meta http-equiv='refresh' content='0'>";
        
    // } catch (\PDOException $e) {
    //   echo $sql;
    //   print_r($finalinput);
    //   dm('$input = ' . $test);
    //   dm('$sql = ' . $sql);
    //   dm('ERROR: Cannot add project:');
    //   dm('MySQL Error Message: ' . $e);
    //   // throw new \PDOException($e->getMessage(), (int)$e->getCode());
    // } 
  }
  




?>
<?php tail(); ?>
