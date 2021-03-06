<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Pokemon Management System</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="style.css">
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
$error_msg_pokemon_inst = "";
$error_msg_party = "";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Always clear old error messages
  $error_msg_pokemon_inst = "";
  $error_msg_party = "";
  if ($_POST["action"] == "Add" || $_POST["action"] == "Release") {
    // Pokemon Instance table studd
    // Check if name is valid
    if ($_POST["action"] == "Add") {
      $iid = $_POST["id"];

      $partySizeQuery = "SELECT COUNT(*) FROM party WHERE uid = " . $_SESSION['uid'];
      $partySize = $conn -> query($partySizeQuery) -> fetch_row()[0];

      $isInPartyQuery = "SELECT COUNT(*) FROM party WHERE iid = " . $iid;
      $inParty = $conn -> query($isInPartyQuery) -> fetch_row()[0];

      // Check if already in party
      if ($inParty) {
        // Cannot add duplicates
        $error_msg_pokemon_inst = "Cannot add <Pokemon_Name> to your party as <Pokemon_Name> is already in your party.";
      } else {
        if ($partySize >= 6) {
          // Cannot add as party is full
          $error_msg_pokemon_inst = "Cannot add <Pokemon_Name> to your party as your party is full with 6 members.";
        } else {
          // Add Pokemon to Party
          $insertPartyQuery = "INSERT INTO party
            (uid, iid, party_order)
            VALUES (" . $_SESSION['uid'] . ", " . $iid . ", " . intval($partySize) . ")";
          $conn -> query($insertPartyQuery);
        }
      }
    } else if ($_POST["action"] == "Release") {
      $iid = $_POST["id"];

      $isInPartyQuery = "SELECT COUNT(*) FROM party WHERE iid = " . $iid;
      $inParty = $conn -> query($isInPartyQuery) -> fetch_row()[0];

      // Check if in party
      if ($inParty) {
        // Cannot add release Pokemon currently in the party
        $error_msg_pokemon_inst = "Cannot release <Pokemon_Name> as <Pokemon_Name> is in your party. Please remove <Pokemon_Name> from your party first.";

      } else {
        // Remove Pokemon Instance
        $releasePokemonQuery = "DELETE FROM pokemon_inst
          WHERE iid = " . $iid;
        $conn -> query($releasePokemonQuery);
      }
    }
  } else if ($_POST["action"] == "Swap" || $_POST["action"] == "Remove") {
    // Party table stuff
    if ($_POST["action"] == "Swap") {
      // Ensure both radio buttons are selected
      if (!($_POST["id_swap_a"] && $_POST["id_swap_b"])) {
        // Missing radio button
        $error_msg_party = "Cannot swap as you have not selected a radio button in both the 'Swap A' and 'Swap B' column.";
        $message = "CANNOT SWAP";
      } else {
        $iid_a = $_POST["id_swap_a"];
        $iid_b = $_POST["id_swap_b"];

        $get_order_a_query = "SELECT party_order FROM party WHERE iid = " . $iid_a . " AND uid = " . $_SESSION['uid'];
        $order_a = $conn -> query($get_order_a_query) -> fetch_row()[0];

        $get_order_b_query = "SELECT party_order FROM party WHERE iid = " . $iid_b . " AND uid = " . $_SESSION['uid'];
        $order_b = $conn -> query($get_order_b_query) -> fetch_row()[0];

        $set_order_a_query = "UPDATE party SET party_order = " . $order_b
          . " WHERE iid = " . $iid_a . " AND uid = " . $_SESSION['uid'];
        $conn -> query($set_order_a_query);

        $set_order_b_query = "UPDATE party SET party_order = " . $order_a
          . " WHERE iid = " . $iid_b . " AND uid = " . $_SESSION['uid'];
        $conn -> query($set_order_b_query);
      }
    } else if ($_POST["action"] == "Remove") {
      $iid = $_POST["id"];
      // Decrement party_order of party members with higher number than entry to be removed
      // Then remove entry
      $get_order_query = "SELECT party_order FROM party WHERE iid = " . $iid . " AND uid = " . $_SESSION['uid'];
      $order = $conn -> query($get_order_query) -> fetch_row()[0];

      $bigger_order_query = "SELECT uid, iid, party_order FROM party WHERE party_order > " . $order . " AND uid = " . $_SESSION['uid'];
      // Get entries with larger orders
      if ($result = $conn -> query($bigger_order_query)) {
        while ($row = $result -> fetch_row()) {
          // Decrement party_order of larger orders
          $uid_to_decrement = $row[0];
          $iid_to_decrement = $row[1];
          $party_order_to_decrement = $row[2];
          $new_party_order = $party_order_to_decrement - 1;

          $set_new_order_query = "UPDATE party SET party_order = " . $new_party_order
            . " WHERE iid = " . $iid_to_decrement . " AND uid = " . $uid_to_decrement;
          $conn -> query($set_new_order_query);
        }
        $result -> free_result();
      }
      // Remove entry
      $removeFromPartyQuery = "DELETE FROM party
        WHERE iid = " . $iid . " AND uid = " . $_SESSION['uid'];
      $conn -> query($removeFromPartyQuery);
    }
  }
  unset($_POST["action"]);
  unset($_POST["id"]);
}

?>

<?php include('navbar.php'); ?>

<div class="container" style="padding-top: 2%">
</div>

<?php

echo "<div class=\"container\">";
echo "<h2>".$_SESSION['name']. "'s Party:</h2>";
echo "<h5><span style='color:red;'>".$error_msg_party."</span></h5>";
echo "<form method='post' action='' id='swap_form'>
  <input type='submit' name='action' value='Swap'/>
</form><br>";
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
    echo "<th>Swap A</th>";
    echo "<th>Swap B</th>";
    echo "<th>Remove from Party</th>";
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
      echo "<td>
        <input form='swap_form' type='radio' name='id_swap_a' value='" . $row[12] . "'/>
      </td>";
      echo "<td>
        <input form='swap_form' type='radio' name='id_swap_b' value='" . $row[12] . "'/>
      </td>";
      echo "<td><form method='post' action='' id='remove_form'>
        <input type='submit' name='action' value='Remove'/>
        <input type='hidden' name='id' value='" . $row[12] . "'/>
      </form></td>";
    echo "</tr>";
  }
  $result -> free_result();
}

echo "</table>";
echo "</div>";

echo "<hr>";

echo "<div class=\"container\">";
echo "<h2>".$_SESSION['name']. "'s Pokemon:</h2>";
echo "<h5><span style='color:red;'>".$error_msg_pokemon_inst."</span></h5>";

echo "<table class=\"table table-striped table-hover\" style=\"width:100%\">";
echo "<thead>";
  echo "<tr>";
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
    echo "<th>Add to Party</th>";
    echo "<th>Release from Management</th>";
  echo "</tr>";
echo "</thead>";

$ownership_query = "SELECT p.pid, p.name, i.max_hp, i.attack, i.defense, i.sp_atk, i.sp_def, i.speed,
i.move_1, i.move_2, i.move_3, i.move_4, i.iid, i.nickname
  FROM pokemon_inst AS i, pokemon AS p WHERE i.pid = p.pid AND i.uid = " . $_SESSION['uid'] . " ORDER BY p.pid";
if ($result = $conn -> query($ownership_query)) {
  while ($row = $result -> fetch_row()) {
    echo "<tr>";
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
      echo "<td><form method='post' action='' id='add_form'>
        <input type='submit' name='action' value='Add'/>
        <input type='hidden' name='id' value='" . $row[12] . "'/>
      </form></td>";
      echo "<td><form method='post' action='' id='release_form'>
        <input type='submit' name='action' value='Release' onclick=\"return confirm('Are you sure you want to say farewell to " . $row[13] . " forever?')\"/>
        <input type='hidden' name='id' value='" . $row[12] . "'/>
      </form></td>";
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
