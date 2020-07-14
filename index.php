<?php
session_start();
?>
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
$app['bucket_name'] = getenv('GOOGLE_STORAGE_BUCKET');
$app['mysql_user'] =  $mysql_user;
$app['mysql_password'] = $mysql_password;
$app['mysql_dbname'] = getenv('MYSQL_DBNAME');
$app['connection_name'] = getenv('MYSQL_CONNECTION_NAME');

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
  
  // Check if pin is valid
  if (empty($_POST["pin"])) {
    $pinErr = "Pin cannot be empty";
  } else {
    // pin must be 4 numbers
    if (!preg_match("/^[0-9]*$/", $_POST["pin"]) || strlen($_POST["pin"]) != 4) {
      $pinErr = "Pin must only contain 4 numbers";
    } else {
      $pin = test_input($_POST["pin"]);
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
      <a class="navbar-brand" href="#" >CS348 Project</a>
    </div>
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
      <label for="player-pin">Pin (4 numbers):</label>
      <input class="form-control" id="player-pin" type="password" name="pin" value="">
    </div>
    <div>
    	<span class="error"><?php echo $pinErr;?></span>
    </div>
    <button class="btn btn-primary" type="submit" name="submit" value="Submit">Login</button>
    <button class="btn btn-primary" type="submit" name="create" value"Create">Create New Account</button>
  </form>
</div>

<?php

$query = "SELECT * FROM players WHERE name='$name'";

// Add pin check if loggin in
if (isset($_POST["submit"])) {
	$query = $query . " AND pin='$pin'";
}

// If name or pin (only during login -- not signup) contains an error, skip check
if (empty($nameErr) && (!isset($_POST["submit"]) || empty($pinErr))) {
	$result = $conn -> query($query) -> fetch_row();

	if (isset($_POST["submit"])) {
		// Check if player credentials matches input
		if (!empty($result)) {
			// Store name and uid in session storage
			$_SESSION['name'] = $name;
			$_SESSION['uid'] = $result[0];
			header('Location: /pokemon.php');
			exit();
		} else {
			$loginErr = "Username and pin combination is incorrect.";
		}
	} elseif (isset($_POST["create"])) {
		// Check if any player exists with name -- make new new player not
		if (empty($result)) {
			$uidquery = "SELECT max(uid)+1 FROM players";
			$uid = $conn -> query($uidquery) -> fetch_row();
			$insert = "INSERT INTO players (uid, name, pin, joined_at) VALUES('$uid[0]', '$name', '$pin', now())";
			if ($conn -> query($insert) === TRUE) {
                		$_SESSION['name'] = $name;
				$_SESSION['uid'] = $uid[0];
				header('Location: /pokemon.php');
				exit();
			} else {
				$loginErr = "Unable to connect.";
			}
		} else {
			$loginErr = "A user with that name already exists.";
		}
	}
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
