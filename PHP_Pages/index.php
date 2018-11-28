<!--
CSC 410 - Database Systems
TubeMaster database application

Home Page - landing page to link to all other important pages
Written by Jordan Turley
-->

<?php
require_once 'functions.php';

if (!isLoggedIn()) {
	header('Location: /login.php');
	return;
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>TubeMaster</title>
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<style type="text/css">
		#main {
			text-align: center;
		}
		#link-list > li {
			display: inline-block;
			margin-left: 0.5em;
			margin-right: 0.5em;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="jumbotron" id="main">
					<h1>TubeMaster</h1>
					<ul id="link-list">
						<li><a href="clients.php">Clients</a></li>
						<li><a href="all_projects.php">All Projects</a></li>
						<li><a href="equipment.php">Equipment</a></li>
						<li><a href="repairs.php">Repairs</a></li>
						<li><a href="packing_lists.php">Packing Lists</a></li>
						<li><a href="personnel.php">Personnel</a></li>
						<li><a href="logout.php">Logout</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</body>
</html>