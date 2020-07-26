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

if (isset($_POST['val'])) {
	$newVal = $_POST['val'] + 1;
	$newRes = "";
	if ($newVal === 5) {
		$newRes = "Welcome to the battle!";
	}
	echo json_encode(array("success"=>TRUE, "val"=>$newVal, "res"=>$newRes));
} else if (isset($_POST['startDuel']) && isset($_POST['uid'])) {
	$partyQuery1 = "SELECT * FROM party, pokemon_inst WHERE party.uid=" . $_POST['uid'] . " AND party.iid=pokemon_inst.iid ORDER BY party.party_order";
	$result = $conn -> query($partyQuery1) -> fetch_all(MYSQLI_ASSOC);
	echo json_encode(array("success"=>TRUE,"party"=>$result));
	
} else {
	echo json_encode(array("success"=>FALSE,"error"=>"Bad request"));
}


?>

