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
$app['debug'] = getenv('DEBUG');

$username = $app['mysql_user'];
$password = $app['mysql_password'];
$dbname = $app['mysql_dbname'];
$dbport = null;
$dbsocket = $app['connection_name'];
$debug = $app['debug'];

$conn = null;
if ($debug) {
  // Testing
  $conn = new mysqli("127.0.0.1", $username, $password, $dbname, 3306);
} else {
  // Deployment
  $conn = new mysqli(null, $username, $password, $dbname, $dbport, $dbsocket);
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
        <a class="navbar-brand" href="#">CS348 Project</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
    <li class="nav-item"><a class="nav-link" href="pokemon.php"> Search Pokemon </a></li>
	<li class="nav-item"><a class="nav-link" href="profile.php"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
	<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>
</div>

<div class="container" style="padding-top: 2%">
</div>

<?php

//print Username and Join Date
$dateQuery = "SELECT joined_at FROM player WHERE uid=".$_SESSION['uid'];
$dateResult = $conn->query($dateQuery);
$join_date = $dateResult->fetch_row();

echo "<div class=\"container\">";
echo "<h2>".$_SESSION['name']. "'s Profile Information</h2>";
echo "<br>Username: ". $_SESSION['name']. "<br>";
echo "Joined on: ". $join_date[0]. "<br><br>";
echo "</div>";

$dateResult -> free_result();

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
