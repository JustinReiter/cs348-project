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
	var val = 0;
	function battleUpdate() {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {val: val},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				document.getElementById("res").innerText = "Response " + data.val + " - " + data.res;
				val++;
				if (!data.res) {
					setTimeout(battleUpdate, 5000);
				}
			}
		}
		});
	}

	function startDuel(uid) {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {uid: uid, startDuel: true},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				document.getElementById("res").innerText = "Starting game";
				document.getElementById("start-button").style.display = 'none';
				
				document.getElementById("opponent-img").src = "img/" + data.enemy[data.enemyPkmIndex].pid + ".png";
				document.getElementById("player-img").src = "img/" + data.player[data.playerPkmIndex].pid + ".png";

				document.getElementById("enemy-pkm-name").innerText = data.enemy[data.enemyPkmIndex].nickname;
				document.getElementById("enemy-pkm-hp").innerText = data.enemy[data.enemyPkmIndex].cur_hp + " / " + data.enemy[data.enemyPkmIndex].max_hp + " HP";
				document.getElementById("player-pkm-name").innerText = data.player[data.playerPkmIndex].nickname;
				document.getElementById("player-pkm-hp").innerText = data.player[data.playerPkmIndex].cur_hp + " / " + data.player[data.playerPkmIndex].max_hp + " HP";

				document.getElementById("move-1").innerText = data.player[data.playerPkmIndex].move_1;
				document.getElementById("move-2").innerText = data.player[data.playerPkmIndex].move_2;
				document.getElementById("move-3").innerText = data.player[data.playerPkmIndex].move_3;
				document.getElementById("move-4").innerText = data.player[data.playerPkmIndex].move_4;

				document.getElementById("battle-div").style.display = '';
			}
		}
		});
	}

	function useMove(uid, move_name) {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {uid: uid, startDuel: true},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				document.getElementById("res").innerText = "Starting game";
				document.getElementById("start-button").style.display = 'none';

				document.getElementById("battle-div").style.display = '';
				document.getElementById("opponent-img").src = "img/" + data.enemy[data.enemyPkmIndex].iid + ".png";

				document.getElementById("player-img").src = "img/" + data.player[data.playerPkmIndex].iid + ".png";
			} else {
				console.log("ERROR");
			}
		}
		});
	}
</script>
<style>
.error {color: #FF0000;}
</style>
</head>
<body onload="battleUpdate()">

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

// Redirects user to login page if no login data found
if (!isset($_SESSION['name']) || !isset($_SESSION['uid'])) {
	header("Location: ./index.php");
	exit();
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
        <a class="navbar-brand" href="pokemon.php">CS348 Project</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li class="nav-item"><a class="nav-link" href="pokemon.php"> Search Pokemon </a></li>
		<li class="nav-item"><a class="nav-link" href="catchPokemon.php"> Catch Pokemon </a></li>
		<li class="nav-item"><a class="nav-link" href="organizePokemon.php"> Organize Pokemon </a></li>
		<li class="nav-item"><a class="nav-link" href="battle.php"> Battle </a></li>
		<li class="nav-item"><a class="nav-link" href="profile.php"><span class="fa fa-user"></span> <?php echo $_SESSION['name'];?></a></li>
		<li class="nav-item"><a class="nav-link" href="index.php"><span class="fa fa-sign-out"></span> Logout</a></li>
      </ul>
    </div>
  </nav>
</div>

<div class="container" padding-top="4%">
  <h5 id="res">Waiting for response</h5>
  <button id="start-button" class="btn btn-primary" onclick="startDuel(<?php echo $_SESSION['uid']?>)">Start Duel</button>
</div>
<div id="battle-div" class="container" style="display: none;">
	<div id="enemy" class="card">
		<div class="row no gutters">
			<div class="col-md-8">
				<h5 id="enemy-pkm-name" class="card-title">Player Pokemon</h5>
				<p id="enemy-pkm-hp" class="card-text">X / X HP</p>
			</div>
			<div class="col-md-4">
				<img id="opponent-img" src="" class="card-img" alt="Opponent Pokemon">
			</div>
		</div>
	</div>
	<div id="player" class="card">
		<div class="row no gutters">
			<div class="col-md-4">
				<img id="player-img" src="" class="card-img" alt="Player Pokemon">
			</div>
			<div class="col-md-4">
				<h5 id="player-pkm-name" class="card-title">Player Pokemon</h5>
				<p id="player-pkm-hp" class="card-text">X / X HP</p>
			</div>
			<div class="col-md-4">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th colspan="2" style="text-align: center;">Moves</th>
						</tr>
					</thead>
					<tr>
						<td><button id="move-1" class="btn" onclick="useMove(<?php echo $_SESSION['uid']?>, 'move_1')">Move 1</button></td>
						<td><button id="move-2" class="btn" onclick="useMove(<?php echo $_SESSION['uid']?>, 'move_2')">Move 2</button></td>
					</tr>
					<tr>
						<td><button id="move-3" class="btn" onclick="useMove(<?php echo $_SESSION['uid']?>, 'move_3')">Move 3</button></td>
						<td><button id="move-4" class="btn" onclick="useMove(<?php echo $_SESSION['uid']?>, 'move_4')">Move 4</button></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>


<?php
$query = "SELECT";



$conn -> close();
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

