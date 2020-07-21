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
$nameErr = "";
$uid = "";

// Redirects user to login page if no login data found
if (!isset($_SESSION['name']) || !isset($_SESSION['uid'])) {
	header("Location: ./index.php");
	exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if name is valid
  if (empty($_POST["uid"])) {
    $uid = "";
  } else {
    $uid = test_input($_POST["uid"]);
    if (!preg_match("/^[0-9a-zA-Z \.']*$/",$name)) {
      $nameErr = "Only numbers, letters, periods, apostraphes, and white space are allowed.";
    }
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
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

<div class="container" style="padding-top: 2%">
  <h2>Party Searcher</h2>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="form-group">
      <label for="user-id">Search for Username:</label>
      <input class="form-control" uid="user-id" value="<?php echo $name;?>">
    </div>
    <span class="error"><?php echo $nameErr;?></span>

    <input class="btn btn-primary" uid="submit" value="Submit">
  </form>
</div>

<?php
echo "<hr>";
echo "<div class=\"container\">";
echo "<h2>Search Results:</h2>";

$basequery = "SELECT uid, iid FROM party WHERE";
$uidCond = "TRUE";

if(strcmp ($uid, "") !== 0 ) {
	$uidCond = "name = \"" . $uid . "\"";
}

$finalQuery = $basequery . " " . $uidCond;

echo "<table class=\"table table-striped table-hover\" style=\"width:100%\">";
echo "<thead>";
  echo "<tr>";
    echo "<th>User ID</th>";
    echo "<th>Party Instance ID</th>";
  echo "</tr>";
echo "</thead>";

$pointer = 0;
if ($result = $conn -> query($finalQuery)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
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

