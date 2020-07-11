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

if (isset($_POST['favourite']) && isset($_POST['uid']) && isset($_POST['pid']) && $_POST['favourite'] == "true") {
	$query = "INSERT INTO favourite_pokemon (uid, pid) VALUES(".(int)$_POST['uid'].",".(int)$_POST['pid'].");";
	if ($conn -> query($query) === TRUE) {
		echo json_encode(array("success"=>TRUE));
	} else {
		echo json_encode(array("success"=>FALSE,"error"=>"Failed to insert into database"));
	}	
} elseif (isset($_POST['favourite']) && isset($_POST['uid']) && isset($_POST['pid']) && $_POST['favourite'] == "false") {
	$query = "DELETE FROM favourite_pokemon WHERE uid=".(int)$_POST['uid']." AND pid=".(int)$_POST['pid'].";";
	if ($conn -> query($query) === TRUE) {
		echo json_encode(array("success"=>TRUE));
	} else {
		echo json_encode(array("success"=>FALSE,"error"=>"Failed to insert into database"));
	}
} else {
	echo json_encode(array("success"=>FALSE,"error"=>"Bad request"));
}


?>
