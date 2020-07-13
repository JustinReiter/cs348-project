<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Pokemon Searcher</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
	function favourite(uid, pid) {
		$.ajax({
		url: './favouritePokemon.php',
		type: 'POST',
		data: {favourite: true, uid: uid, pid: pid},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				document.getElementById("pkm-" + pid).className = "fa fa-heart";
				document.getElementById("pkm-a-" + pid).setAttribute("onclick", "unfavourite(" + uid + "," + pid + ");");
			}
		}
		});

	}

	function unfavourite(uid, pid) {
		$.ajax({
		url: './favouritePokemon.php',
		type: 'POST',
		data: {favourite: false, uid: uid, pid: pid},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				document.getElementById("pkm-" + pid).className = "fa fa-heart-o";
				document.getElementById("pkm-a-" + pid).setAttribute("onclick", "favourite(" + uid + "," + pid + ");");
			}
		}
		});
	}
</script>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

use Google\Cloud\Storage\StorageClient;

$app = array();
$app['bucket_name'] = "cs348demo.appspot.com";
$app['mysql_user'] =  $mysql_user;
$app['mysql_password'] = $mysql_password;
$app['mysql_dbname'] = "pokemon";
$app['project_id'] = getenv('GCLOUD_PROJECT');
$app['connection_name'] = "/cloudsql/cs348demo-279318:us-central1:cs348demo-db";

$username = $app['mysql_user'];
$password = $app['mysql_password'];
$dbname = $app['mysql_dbname'];
$dbport = null;
$dbsocket = $app['connection_name'];


// Create connection
//for testing on localhost:8080
$conn = new mysqli("127.0.0.1", $username, $password, $dbname, 3306);

//for deployment
//$conn = new mysqli(null, $username, $password, $dbname, $dbport, $dbsocket);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Redirects user to login page if no login data found
if (!isset($_SESSION['name']) || !isset($_SESSION['uid'])) {
	header("Location: ./index.php");
	exit();
}

?>

<div id="navbar">
  <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="./pokemon.php">CS348 Project</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
	<li class="nav-item"><a class="nav-link" href="#"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
	<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>
</div>

<div class="container" style="padding-top:4%">
	<div class="card">
		<div class="row no gutters">
		<div class="col-md-4">
			<img src"" class="card-img" alt="Text">
		</div>
		<div class="col-md-4">
			<div class="card-body">
<?php

if (isset($_GET["pkm"])) {
	$query = "SELECT * FROM pokemon WHERE pid=".$_GET["pkm"].";";
	$result = $conn -> query($query) -> fetch_all(MYSQLI_ASSOC);
	$result = $result[0];
	echo "<h5 class='card-title'>" . $result["name"] . "</h5>";
	
	echo "<p class='card-text'>Pokedex Number: " . $result["pid"] . "</p>";
	echo "<p class='card-text'>Generation: " . $result["gen"] . "</p>";

	echo "<p class='card-text'>Type: " . $result["type1"];

	if (!empty($result["type2"])) {
		echo " & " . $result["type2"];
	}

	echo "</p><br>";
	
	echo "<p class='card-text'>Height: " . $result["height"] . " m</p>";
	echo "<p class='card-text'>Weight: " . $result["weight"] . " kg</p>";
	echo "<p class='card-text'>Percent male: " . $result["percent_male"] . "%</p>";


	echo "</div></div><div class='col-md-4'><div class='card-body'><br>";


	echo "<p class='card-text'>Base HP: " . $result["hp"] . "</p>";
	echo "<p class='card-text'>Base Attack: " . $result["attack"] . "</p>";
	echo "<p class='card-text'>Base Defense: " . $result["defense"] . "</p>";
	echo "<p class='card-text'>Base Sp. Atk: " . $result["sp_atk"] . "</p>";
	echo "<p class='card-text'>Base Sp. Def: " . $result["sp_def"] . "</p>";
	echo "<p class='card-text'>Base Speed: " . $result["speed"] . "</p><br>";

	echo "<p class='card-text'>Base Egg Steps: " . $result["base_egg_steps"] . " steps</p>";
	echo "<p class='card-text'>Base Happiness: " . $result["base_happiness"] . "</p><br>";

} else {
	header("Location: ./pokemon.php");
	exit();	
}
?>

			</div>
		</div>
		</div>
	</div>
	<div class="btn">
		<a href="./pokemon.php"><span class="fa fa-arrow-left"></span> Back to Pokemon Search</a>
	</div>
</div>


<?php
$conn -> close();
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
