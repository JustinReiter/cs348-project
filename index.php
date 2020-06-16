<!DOCTYPE HTML>
<html>
<head>
<title>Pokemon Searcher</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
// echo "\nConnected successfully\n";

// Initialization empty variables
$nameErr = $pinErr = $loginErr = "";
$name = $pin = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  // Check if name is valid
  if (empty($_POST["name"])) {
	  $nameErr = "Name must be specified";
  } else {
    $name = test_input($_POST["name"]);
    if (!preg_match("/^[0-9a-zA-Z \.']*$/",$name)) {
	    $nameErr = "Only numbers, letters, periods, apostraphes, and white space are allowed.";
    }
  }
  
  // Check if dexNum is valid
  if (empty($_POST["pin"])) {
    $pinErr = "Pin cannot be empty";
  } else {
    $pin = test_input($_POST["pin"]);
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
    <a class="navbar-brand" href="#">CS348 Project</a>
  </nav>
</div>


<div class="container" style="padding-top: 2%">
  <h2>Login</h2>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <span class="error"><?php echo $loginErr;?></span>
    <div class="form-group">
      <label for="player-name">Username:</label>
      <input class="form-control" id="player-name" type="text" name="name" value="<?php echo $name;?>">
    </div>
    <span class="error"><?php echo $nameErr;?></span>
    <div class="form-group">
      <label for="player-pin">Pin:</label>
      <input class="form-control" id="player-pin" type="password" name="pin" value="<?php echo $dexNum;?>">
    </div>
    <span class="error"><?php echo $pinErr;?></span>
    <input class="btn btn-primary" type="submit" name="submit" value="Submit">
  </form>
</div>

<?php
$query = "SELECT * FROM players WHERE name='$name' AND pin='$pin'";
if ($result = $conn -> query($query) -> fetch_row()) {
	if ($result) {
		header('Location: /pokemon.php');
		exit();
	}
  $result -> free_result();
} else {
	$loginErr = "Username and pin combination is incorrect.";
}
$conn -> close();
?>
<div class="container">
<span class="error"><?php echo $loginErr;?></span>
</div>


<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
