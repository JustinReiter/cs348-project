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
// echo "\nConnected successfully\n";

// Initialization empty variables
$nameErr = $dexNumErr = $typeErr = "";
$name = $dexNum = $type = "";

// Redirects user to login page if no login data found
if (!isset($_SESSION['name']) || !isset($_SESSION['uid'])) {
	header("Location: ./index.php");
	exit();
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if name is valid
  if (empty($_POST["name"])) {
    $name = "";
  } else {
    $name = test_input($_POST["name"]);
    if (!preg_match("/^[0-9a-zA-Z \.']*$/",$name)) {
      $nameErr = "Only numbers, letters, periods, apostraphes, and white space are allowed.";
    }
  }
  
  // Check if dexNum is valid
  if (empty($_POST["dexNum"])) {
    $dexNum = "";
  } else {
    $dexNum = test_input($_POST["dexNum"]);
    if(!is_numeric($dexNum)) {
      $dexNumErr = "Pokedex Number must be a number.";
    }
  }
  
  // Check if type is valid
  if (empty($_POST["type"])) {
    $type = "";
  } else {
    $type = test_input($_POST["type"]);
    if (!preg_match("/^[a-zA-Z ]*$/",$type)) {
	    $typeErr = "Only letters and white space are allowed.";
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
	<li class="nav-item"><a class="nav-link" href="#"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
	<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>
</div>


<div class="container" style="padding-top: 2%">
  <h2>Pokemon Searcher</h2>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="form-group">
      <label for="pkm-name">Search by Pokemon Name:</label>
      <input class="form-control" id="pkn-name" type="text" name="name" value="<?php echo $name;?>">
    </div>
    <span class="error"><?php echo $nameErr;?></span>
    <div class="form-group">
      <label for="pkn-number">Search by Pokedex Number:</label>
      <input class="form-control" id="pkn-number" type="text" name="dexNum" value="<?php echo $dexNum;?>">
    </div>
    <span class="error"><?php echo $dexNumErr;?></span>
    <div class="form-group">
      <label for="pkn-type">Search by Pokemon Type:</label>
      <input class="form-control" id="pkn-type" type="text" name="type" value="<?php echo $type;?>">
    </div>
    <span class="error"><?php echo $typeErr;?></span>
    <input class="btn btn-primary" type="submit" name="submit" value="Submit">
  </form>
</div>

<?php
echo "<hr>";
echo "<div class=\"container\">";
echo "<h2>Search Results:</h2>";

$baseQuery = "SELECT pid, name, classification, type1, type2 FROM pokemon WHERE";
$nameCond = "TRUE";
$dexNumCond = "TRUE";
$typeCond = "TRUE";
if (strcmp($name, "") !== 0) {
    $nameCond = "name = \"" . $name . "\"";
}
if (strcmp($dexNum, "") !== 0) {
    $dexNumCond = "pid = " . $dexNum;
}
if (strcmp($type, "") !== 0) {
    $typeCond = "(type1 = \"" . $type . "\" OR type2 = \"" . $type . "\")";
}
$finalQuery = $baseQuery . " " . $nameCond . " AND " . $dexNumCond . " AND " . $typeCond;

echo "<table class=\"table table-striped table-hover\" style=\"width:100%\">";
echo "<thead>";
  echo "<tr>";
    echo "<th>Pokedex Number</th>";
    echo "<th>Name</th>";
    echo "<th>Classification</th>";
    echo "<th>Primary Type</th>";
    echo "<th>Secondary Type</th>";
    echo "<th>Favourited</th>";
  echo "</tr>";
echo "</thead>";

$favouriteQuery = "SELECT pid FROM favourite_pokemon WHERE uid=".$_SESSION['uid']." ORDER BY pid";
$favouriteArr = $conn -> query($favouriteQuery) -> fetch_all();
$pointer = 0;
if ($result = $conn -> query($finalQuery)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
      echo "<td>" . $row[0] . "</td>";
      echo "<td><a href='./viewPokemonPage.php?pkm=" . $row[0] . "'>" . $row[1] .  "</a></td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      while ($favouriteArr && count($favouriteArr) > $pointer && $favouriteArr[$pointer][0] < $row[0]) {
	      $pointer = $pointer + 1;
      } 
      if ($favouriteArr && count($favouriteArr) > $pointer && $favouriteArr[$pointer][0] == $row[0] ) {
	      echo "<td><a id='pkm-a-".$row[0]."' href='javascript:;' onclick='unfavourite(".$_SESSION['uid'].",".$row[0].")' ><span id='pkm-".$row[0]."' class='fa fa-heart'></span></a></td>";
	      $pointer = $pointer + 1;
      } else {
	      echo "<td><a id='pkm-a-".$row[0]."' href='javascript:;' onclick='favourite(".$_SESSION['uid'].",".$row[0].")' ><span id='pkm-".$row[0]."' class='fa fa-heart-o'></span></a></td>";
      }
    echo "</tr>";
  }
  $result -> free_result();
}
echo "</table>";
echo "</div>";

$conn -> close();
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
