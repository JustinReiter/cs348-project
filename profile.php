<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
        <a class="navbar-brand" href="pokemon.php">CS348 Project</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li class="nav-item"><a class="nav-link" href="pokemon.php"> Search Pokemon </a></li>
		<li class="nav-item"><a class="nav-link" href="catchPokemon.php"> Catch Pokemon </a></li>
		<li class="nav-item"><a class="nav-link" href="organizePokemon.php"> Organize Pokemon </a></li>
		<li class="nav-item"><a class="nav-link" href="battle.php"> Battle </a></li>
		<li class="nav-item"><a class="nav-link" href="profile.php"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
		<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>
</div>

<div class="container" style="padding-top: 2%">
</div>

<?php

//print Username, UID Number, and Join Date
$dateQuery = "SELECT joined_at FROM player WHERE uid=".$_SESSION['uid'];
$dateResult = $conn->query($dateQuery);
$join_date = $dateResult->fetch_row();

echo "<div class=\"container\">";
echo "<h2>".$_SESSION['name']. "'s Profile Information</h2>";
echo "<br>Username: ". $_SESSION['name']. "<br>";
echo "User ID Number: ". $_SESSION['uid']. "<br>";
echo "Joined on: ". $join_date[0]. "<br><br>";
echo "</div>";

$dateResult -> free_result();

//print Party Pokemon Table
echo "<hr>";
echo "<div class=\"container\">";

echo "<h2>".$_SESSION['name']. "'s Party:</h2>";
echo "<table class=\"table table-striped table-hover\" style=\"width:100%\">";
echo "<thead>";
  echo "<tr>";
    echo "<th>Order Number</th>";
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

$party_query = "SELECT p.pid, p.name, i.max_hp, i.attack, i.defense, i.sp_atk, i.sp_def, i.speed,
  i.move_1, i.move_2, i.move_3, i.move_4, i.party_iid, i.nickname, i.party_order
  FROM (
    SELECT max_hp, attack, defense, sp_atk, sp_def, speed, pid,
    move_1, move_2, move_3, move_4, party_order, party.iid AS party_iid, nickname
    FROM party INNER JOIN pokemon_inst ON party.iid = pokemon_inst.iid WHERE party.uid = " . $_SESSION['uid'] . "
  ) AS i,
  pokemon AS p
  WHERE i.pid = p.pid ORDER BY i.party_order";

if ($result = $conn -> query($party_query)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
      echo "<td>" . intval(intval($row[14]) + 1) . "</td>";
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
}
echo "</table>";
echo "</div>";

//print Favourite pokemon table
echo "<hr>";
echo "<div class=\"container\">";
echo "<h2>".$_SESSION['name']. "'s Favourite Pokemon:</h2>";

echo "<table class=\"table table-striped table-hover\" style=\"width:100%\">";
echo "<thead>";
  echo "<tr>";
    echo "<th>Pokedex Number</th>";
    echo "<th>Name</th>";
    echo "<th>Classification</th>";
    echo "<th>Primary Type</th>";
    echo "<th>Secondary Type</th>";
  echo "</tr>";
echo "</thead>";

$favouriteQuery = "SELECT p.pid, name, classification, type1, type2 FROM pokemon AS p, favourite_pokemon AS f WHERE p.pid = f.pid AND f.uid=".$_SESSION['uid']." ORDER BY p.pid";
if ($result = $conn -> query($favouriteQuery)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
      echo "<td>" . $row[0] . "</td>";
      echo "<td><a href='./viewPokemonPage.php?pkm=" . $row[0] . "'>" . $row[1] .  "</a></td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
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
