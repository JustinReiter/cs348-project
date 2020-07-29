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
$app['bucket_name'] = getenv('GOOGLE_STORAGE_BUCKET');
$app['mysql_user'] =  $mysql_user;
$app['mysql_password'] = $mysql_password;
$app['mysql_dbname'] = getenv('MYSQL_DBNAME');
$app['connection_name'] = getenv('MYSQL_CONNECTION_NAME');
$app['prod'] = getenv('PROD');

$username = $app['mysql_user'];
$password = $app['mysql_password'];
$dbname = $app['mysql_dbname'];
$dbport = null;
$dbsocket = $app['connection_name'];
$prod = $app['prod'];

$conn = null;
if ($prod) {
  // Deployment
  $conn = new mysqli(null, $username, $password, $dbname, $dbport, $dbsocket);
} else {
  // Testing
  $conn = new mysqli("127.0.0.1", $username, $password, "pokemon", 3306);
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "\nConnected successfully\n";

// Initialization empty variables
$nameErr = $dexNumErr = $typeErr = "";
$name = $dexNum = $type = "";

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
        <a class="navbar-brand" href="#">CS348 Project</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
  <li class="nav-item"><a class="nav-link" href="pokemon.php"> Search Pokemon </a></li>
	<li class="nav-item"><a class="nav-link" href="catchPokemon.php"> Catch Pokemon </a></li>
	<li class="nav-item"><a class="nav-link" href="organizePokemon.php"> Organize Pokemon </a></li>
	<li class="nav-item"><a class="nav-link" href="partyshare.php"> Parties </a></li>
	<li class="nav-item"><a class="nav-link" href="profile.php"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
	<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>
</div>

<?php

$user = $_GET['user'];

$query = "SELECT p.pid, p.name, i.max_hp, i.attack, i.defense, i.sp_atk, i.sp_def, i.speed,
  i.move_1, i.move_2, i.move_3, i.move_4, i.party_iid, i.nickname, i.party_order
  FROM (
    SELECT max_hp, attack, defense, sp_atk, sp_def, speed, pid,
    move_1, move_2, move_3, move_4, party_order, party.iid AS party_iid, nickname
    FROM party INNER JOIN pokemon_inst ON party.iid = pokemon_inst.iid WHERE party.uid = ". $user . "
  ) AS i,
  pokemon AS p
  WHERE i.pid = p.pid ORDER BY i.party_order;";

$idquery = "SELECT name FROM player WHERE uid =" .$user .";";
$res = $conn -> query($idquery);
$idrow = $res-> fetch_row();

echo "<div class=\"table-responsive\" max-width: 16rem >";
echo "<h2>". $idrow[0]. "'s Party:</h2>";
echo "<h2></h2>";
echo "<h5><span style='color:red;'>".$error_msg_party."</span></h5>";
echo "<table class=\"table table-image table-bordered\">";
echo "<thead class=\"thead-dark\">";
  echo "<tr>";
    echo "<th>Image</th>";
    echo "<th>Pokedex Number</th>";
    echo "<th>Name</th>";
    echo "<th>Max HP</th>";
    echo "<th>Attack</th>";
    echo "<th>Defense</th>";
    echo "<th>Special Attack</th>";
    echo "<th>Special Defense</th>";
    echo "<th>Speed</th>";
    echo "<th>Move 1</th>";
    echo "<th>Move 2</th>";
    echo "<th>Move 3</th>";
    echo "<th>Move 4</th>";
  echo "</tr>";
echo "</thead>";

if ($result = $conn -> query($query)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
      echo "<td class=\"w-5\">";
        echo "<img src=\"img/" . $row[0] . ".png\" class=\"img-fluid\" alt=\"Hmmm, I wonder where the image is?\">";
      echo "<td>" . $row[0] . "</td>";
      echo "<td><a href='./viewPokemonPage.php?pkm=" . $row[0] . "'>" . $row[1] .  "</a></td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      echo "<td>" . $row[5] . "</td>";
      echo "<td>" . $row[6] . "</td>";
      echo "<td>" . $row[7] . "</td>";
      echo "<td>" . $row[8] . "</td>";
      echo "<td>" . $row[9] . "</td>";
      echo "<td>" . $row[10] . "</td>";
      echo "<td>" . $row[11] . "</td>";
    echo "</tr>";
  }
  $result -> free_result();
}

echo "</table>";
echo "</div>";
?>

<?php
$conn -> close();
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>


