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
            background: #2b6541;
            padding: 10px 40px;
            width:400px;
            display: none;    /* !! control display */
            border: none;
            /* margin: 0px 0 22px 0; */
            margin: 0 auto;
            position:absolute;
            left: 35%;
            top: 7%;
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
        label, .addproject_maincontent span {color:#ffb55a}

</style>
<?php
  /*
    CSC 410 - Database Systems
    TubeMaster database application

    Projects Page 
    TODO: LINK TO the reactor page!!
  */
  include "functions.php";

  if (!isLoggedIn()) {
    header('Location: /login.php');
    return;
  }
  
  head("Projects");
?>

<!-- reference: https://www.w3schools.com/howto/howto_js_sidenav.asp -->
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
function popupnewprojectwin(){
  document.getElementById("maincontent").style.filter = "blur(2px)";
  document.getElementById("addproject_maincontent").style.display = "inline-block";
}
function canclenewprojectwin(){
  // confirm('do you want to go back? Your input will be unsaved Y/N');
  document.getElementById("maincontent").style.filter = "blur(0)";
  document.getElementById("addproject_maincontent").style.display = "none";
}
// var btn = document.getElementById('viewproject_btn');
// btn.addEventListener('click', function() {
//   document.location.href = '<?php echo $page; ?>';
// });
</script>

<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="index.php">< Home</a>
  <a style="cursor:pointer" onclick='popupnewprojectwin()'>+ Add</a>
  <form method="get" class="functionbtn">
    <input type="submit" name="hideinactive_submit" value="Hide All Inactive">
    <input type="submit" name="showall_submit" value="Show All Projects">
  </form>
</div>
<span style="cursor:pointer" onclick="openNav()">&#9776; open</span>


<?php
  //TODO: add project_is_active field default true to the database table!!
  /* functions php */
  function get_project_info($myPDO, $hideinactive){
    $projectinfo = $myPDO->query("SELECT * FROM projects;");
    foreach ($projectinfo as $row){
      if($row['project_is_active'] == "1"){
        $viewstring = '<td><button id="viewproject_btn" name="viewproject_btn" value="';
        $viewstring .= $row['project_id'];
        $viewstring .= '">View/Edit</button></td>';
        echo "<tr>\n";
        
        echo "  <td>" .'<input type="checkbox" name="mark_inactive[]" value="' . $row['project_id'] .'" />' . "</td>\n";  
        echo $viewstring;
        echo "  <td>" . $row['project_id'] . "</td>\n";
        echo "  <td>" . $row['project_start_date'] . "</td>\n";
        echo "  <td>" . $row['project_equipment_ship_date'] . "</td>\n";
        echo "  <td>" . $row['project_expected_end_date'] . "</td>\n";
        echo "  <td>" . $row['project_type'] . "</td>\n";
        echo "  <td>" . $row['project_testing_type'] . "</td>\n";
        echo "  <td>" . $row['reactor_id'] . "</td>\n";
        echo "  <td>" . "active" . "</td>\n";
      }
      else{
        if($hideinactive == false){
          if($row['project_is_active'] == "0" || $row['project_is_active'] == "NULL" ){
            $viewstring = '<td><button id="viewproject_btn" name="viewproject_btn" value="';
            $viewstring .= $row['project_id'];
            $viewstring .= '">View/Edit</button></td>';
            echo "<tr>\n";
            
            echo "  <td>" .'<input type="checkbox" name="mark_inactive[]" value="' . $row['project_id'] .'" />' . "</td>\n";  
            echo $viewstring;
            echo "  <td>" . $row['project_id'] . "</td>\n";
            echo "  <td>" . $row['project_start_date'] . "</td>\n";
            echo "  <td>" . $row['project_equipment_ship_date'] . "</td>\n";
            echo "  <td>" . $row['project_expected_end_date'] . "</td>\n";
            echo "  <td>" . $row['project_type'] . "</td>\n";
            echo "  <td>" . $row['project_testing_type'] . "</td>\n";
            echo "  <td>" . $row['reactor_id'] . "</td>\n"; //TODO : link to the reactor page!
            echo "  <td>" . "inactive" . "</td>\n";
          }
        }
      }
      echo '</tr>';      
    }
  }

  // take a list of id as input and change and mark the selected as inactive in database
  // param: pdo, id_list: an array of selected project's ids, inactive: true if want to mark as inactive & false if want to be active
  function markas_inactive($pdo,$id_list,$inactive){
    $flag = 't';
    if($inactive){ $flag = 'f';}
    $inputdata = [
      ':project_is_active' => $flag,
    ];  
    foreach($id_list as $projectid){
      $sql = 'UPDATE projects '
            . 'SET project_is_active = :flag '
            . 'WHERE project_id = :projectid ;';
      try { 
        // reference: http://www.postgresqltutorial.com/postgresql-php/update/
        $stmt = $pdo->prepare($sql);
        // bind values to the statement
        $stmt->bindValue(':flag', $flag);
        $stmt->bindValue(':projectid', $projectid);
        // update data in the database
        $stmt->execute();
        // return the number of row affected
        // echo $stmt->rowCount();
        //refresh the page to update the display table
        echo "<meta http-equiv='refresh' content='0'>"; 
      } catch (\PDOException $e) {
        echo $sql;
        // throw new \PDOException($e->getMessage(), (int)$e->getCode());
      } 
    }
  }

  function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
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
    echo "</select>";
  }

  //insert the new project input to the database
  function insertnewproject($pdo, $inputdata){
    //construct the sql query for only the valid columns -- coz somehow cannot insert null to the columns
    $validcolumn = [];
    foreach($inputdata as $key => $value) {
      if($value !== ""){
        $validcolumn[$key] = $value;
      }
    }
    
    $rawinputcolumn = "(project_id, ";
    $rawinputcolumn2 = "(:project_id, ";
    //construct the input string
    foreach($validcolumn as $key1 => $value1){
      $rest = substr("$key1", 1); 
      $rawinputcolumn .= $rest . ", ";
      $rawinputcolumn2 .= $key1 . ", ";
    }
    $inputcolumn = substr($rawinputcolumn, 0, -2); 
    $inputcolumn2 = substr($rawinputcolumn2, 0, -2); 
    $inputcolumn .= ") "; //correspond to (reactor_id, project_start_date)
    $inputcolumn2 .= ") "; //correspond to (:reactor_id, :project_start_date)

    //get the current max project id + 1 to be the new project's id
    //TODO: possible synchronization issue
    $result1 = $pdo->query("SELECT project_id FROM projects;");
    $maxid = 0;
    foreach ($result1 as $row1){
      $currid = $row1['project_id'];
      if($currid >= $maxid){
        $maxid =$currid ;
      }
    }
    $newprojectid = $maxid+1;
    //added the project id as column and construct the final input array -- coz not sure how to add a new pair...
    $finalinput[':project_id'] = $newprojectid;
    foreach($validcolumn as $key3 => $value3) {
      if($value !== ""){
        $finalinput[$key3] = $value3;
      }
    }

    //construct the sql query
    $sql = 'INSERT INTO projects ';
    $sql .= $inputcolumn;
    $sql .= 'VALUES ';
    $sql .= $inputcolumn2;
    // // $sql .= '(:reactor_id, :project_start_date, :project_expected_end_date, :project_equipment_ship_date, :project_type, :project_testing_type, :project_is_active)';
    
    //sanity manual check
    echo $sql;
    print_r($finalinput);

    try { 
      $pdo->prepare($sql)->execute($finalinput);
      alert('Record added successfully');
      //refresh the page to update the display table
      echo "<meta http-equiv='refresh' content='0'>";
      
    } catch (\PDOException $e) {
      dm('$input = ' . $test);
      dm('$sql = ' . $sql);
      dm('ERROR: Cannot add project:');
      dm('MySQL Error Message: ' . $e);
      // throw new \PDOException($e->getMessage(), (int)$e->getCode());
    } 
  }


?>

<div id="maincontent" class="maincontent">
  <h1>Projects</h1>
  <table>
  <form method="post">
    <tbody>
      <tr name="tableheader">
        <th></th>
        <th>Project Info</th>
        <th>ID</th>
        <th>Start Date</th>
        <th>Equipment Ship Date</th>
        <th>Expected End Date</th>
        <th>Type</th>
        <th>Testing Type</th>
        <th>Reactor ID</th>
        <th>Status</th> 
      </tr>
      <?php 
      //main content:
      $pdo = connect_to_psql('tmdatabase');
      //hiding inactive projects:
      $hideinactive = isset($_GET['hideinactive_submit']);
      get_project_info($pdo, $hideinactive);

      //mark project as active or inactive:
      if(isset($_POST['confirm_Inactive'])){
        if(sizeof($_POST['mark_inactive']) !== 0){ //check if there is selection
          markas_inactive($pdo,$_POST['mark_inactive'], true);
        } 
      }
      else if(isset($_POST['confirm_active'])){
        if(sizeof($_POST['mark_inactive']) !== 0){ //check if there is selection
          markas_inactive($pdo,$_POST['mark_inactive'], false);
        } 
      }

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
          insertnewproject($pdo, $inputdata);   
        }
      }


      // view or edit the selected project
      if(array_key_exists('viewproject_btn',$_POST)){
        $projid = $_POST['viewproject_btn'];
        //reference: https://stackoverflow.com/questions/11217127/can-i-pass-a-variable-in-a-meta-http-equiv-refresh
        echo '<META HTTP-EQUIV="Refresh" Content="0; URL=viewproject.php?projectid=' . $projid . '">'; 
      }




      ?>
    </tbody>
    <tfoot>
      <tr class="markas">   
        <input type="submit" name="confirm_active" value="Mark as active"> 
        <input type="submit" name="confirm_Inactive" value="Mark as Inactive"> 
      </tr>
    </tfoot>
  </form>
  </table>
</div>

<!-- reference: https://www.w3schools.com/howto/howto_css_signup_form.asp -->
<form method="post">
  <div class="addproject_maincontent" id="addproject_maincontent"> 
    <a href="javascript:void(0)" class="closebtn" onclick="canclenewprojectwin()">&times;</a>
    <h2>Add A New Project</h2>
        <label>Start Date: *</label>
          <input required type="date" min="2000-01-01" max="3000-01-01" name="startdate">
        <label>Equipment Ship Date: *</label>
          <input required type="date" min="2000-01-01" max="3000-01-01" name="shipdate">
        <label>Expected End Date: </label>
          <input type="date" min="2000-01-01" max="3000-01-01" name="enddate">
        <label>Project Type: </label>
          <input type="text" name="projecttype">
        <label>Testing Type: </label>
          <input type="text" name="testtype">
        <label>Reactor ID Linked: *  </label>
          <?php display_allreactors($pdo,1)?><br>
        <!-- <div class="statusradio" style="margin:15px 0;"> -->
          <label>Status: </label>
            <input required checked type="radio" name="radio" value="t"><span>Active</span>
            <input type="radio" name="radio" value="f"><span>Inactive</span>
        <!-- </div> -->
    <div class="newproject_confirmbtn">
      <!-- <button type="button" class="cancel_newproject" onclick="canclenewprojectwin()">Cancel</button> -->
      <input type="submit" name="confirm_newproject" value="Submit">
    </div>
  </div>
</form>





<?php tail(); ?>