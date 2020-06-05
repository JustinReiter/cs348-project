<!DOCTYPE HTML>
<html>
<head>
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
$app['bucket_name'] = "cs348-project-279406.appspot.com";
$app['mysql_user'] =  $mysql_user;
$app['mysql_password'] = $mysql_password;
$app['mysql_dbname'] = "pokemon";
$app['project_id'] = getenv('GCLOUD_PROJECT');
$app['connection_name'] = "/cloudsql/cs348-project-279406:us-east4:cs348-project-db-1";

$username = $app['mysql_user'];
$password = $app['mysql_password'];
$dbname = $app['mysql_dbname'];
$dbport = null;
$dbsocket = $app['connection_name'];


// Create connection
//for testing on localhost:8080
//$conn = new mysqli("127.0.0.1", $username, $password, $dbname, 3306);

//for deployment
$conn = new mysqli(null, $username, $password, $dbname, $dbport, $dbsocket);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "\nConnected successfully\n";

// Initialization empty variables
$nameErr = $dexNumErr = $typeErr = "";
$name = $dexNum = $type = "";

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

<h2>Pokemon Searcher</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  Search by Pokemon Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error"><?php echo $nameErr;?></span>
  <br><br>
  Search by Pokedex Number: <input type="text" name="dexNum" value="<?php echo $dexNum;?>">
  <span class="error"><?php echo $dexNumErr;?></span>
  <br><br>
  Search by Pokemon Type: <input type="text" name="type" value="<?php echo $type;?>">
  <span class="error"><?php echo $typeErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit">
</form>

<?php
echo "<hr>";
echo "<h2>Search Results:</h2>";

$baseQuery = "SELECT pokedex_number, name, classification, type1, type2 FROM pokedex WHERE";
$nameCond = "TRUE";
$dexNumCond = "TRUE";
$typeCond = "TRUE";
if (strcmp($name, "") !== 0) {
    $nameCond = "name = \"" . $name . "\"";
}
if (strcmp($dexNum, "") !== 0) {
    $dexNumCond = "pokedex_number = " . $dexNum;
}
if (strcmp($type, "") !== 0) {
    $typeCond = "(type1 = \"" . $type . "\" OR type2 = \"" . $type . "\")";
}
$finalQuery = $baseQuery . " " . $nameCond . " AND " . $dexNumCond . " AND " . $typeCond;

echo "<table style=\"width:100%\">";
echo "<tr>";
  echo "<th>Pokedex Number</th>";
  echo "<th>Name</th>";
  echo "<th>Classification</th>";
  echo "<th>Primary Type</th>";
  echo "<th>Secondary Type</th>";
echo "</tr>";
if ($result = $conn -> query($finalQuery)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
    echo "</tr>";
  }
  $result -> free_result();
}
echo "</table>";

$conn -> close();
?>

</body>
</html>
