<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/env.php';

use Google\Cloud\Storage\StorageClient;

$app = array();
$app['bucket_name'] = "cs348demo.appspot.com";
$app['mysql_user'] =  $mysql_user;
$app['mysql_password'] = $mysql_password;
$app['mysql_dbname'] = "guestbook";
$app['project_id'] = getenv('GCLOUD_PROJECT');
$app['connection_name'] = "/cloudsql/cs348demo:us-central1:cs348demo-db";



$username = $app['mysql_user'];
$password = $app['mysql_password'];
$dbname = $app['mysql_dbname'];
$dbport = null;
$dbsocket = $app['connection_name'];


// Create connection
//for testing on localhost:8080
$conn = new mysqli("127.0.0.1", $username, $password, $dbname,3306);

//for deployment 
//$conn = new mysqli(null, $username, $password, $dbname, $dbport, $dbsocket);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "\nConnected successfully\n";


$sql = "SELECT * FROM entries";

$counter  = 0;

if ($result = $conn -> query($sql)) {
  while ($row = $result -> fetch_row()) {
        printf ("%s (%s)\n", $row[0], $row[1]);
        $counter = $counter + 1;
  }
  $result -> free_result();
  echo "\n$counter\n";
}

$conn -> close();

?>
