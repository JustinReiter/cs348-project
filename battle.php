<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Pokemon Searcher</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>

	var initialRequest = true;

	/**
	* Pings server every 5 seconds to get updates... Used when waiting for opponent / updating game initially
	* @param {int} player uid (should be in session storage)
	*/
	function battleUpdate(uid) {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {uid: uid, getUpdate: true},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				if (!data.gameInProgress) {
					// Game was in progress before and just ended
					if (document.getElementById("battle-div").style.display !== 'none') {
						if (data.numPartyAlive == 0) {
							// Game over... lose
							document.getElementById("battle-div").style.display = 'none';
							alert("Your opponent has knocked out your last Pokemon, " + document.getElementById("player-pkm-name").innerText + " and won the battle. Keep training harder for next time!");
						} else {
							// Opponent forfeit... win
							alert("Your opponent has forfeit and left the battle. You have been declared the victor of this battle! Congratulations!");
						}
						document.getElementById("player-pkm-hp").innerText = 9999;
					}

					// If no game is in progress, hide battle pane and show start button
					document.getElementById("start-button").style.display = '';
					document.getElementById("battle-div").style.display = 'none';
				} else if (data.playerTurn) {
					// If game is in progress, hide start button and show battle pane
					document.getElementById("start-button").style.display = 'none';
					document.getElementById("battle-div").style.display = '';

					// Only do this once to get pokemon details
					if (true) {
					// if (initialRequest) {
						startDuel(uid);
						initialRequest = false;
					}

					// Enable moves so player can pick next turn
					disableMoveButtons(false);
				} else {
					// Only do this once to get pokemon details
					if (true) {
					// if (initialRequest) {
						startDuel(uid);
						initialRequest = false;
					}

					// Disable buttons so player cannot move on opponents turn
					disableMoveButtons(true);
				}

				if (data.gameInProgress) {
					document.getElementById("player-pkm-remaining").innerText = data.playerPokemonRemaining;
					document.getElementById("enemy-pkm-remaining").innerText = data.enemyPokemonRemaining;

					var oldHp = parseInt(document.getElementById("player-pkm-hp").innerText);
					var newHp = parseInt(data.playerHP);
					var oldNickname = document.getElementById("player-pkm-name").innerText;
					var newNickname = data.playerNickname;
					// Update pokemon name/hp
					document.getElementById("player-pkm-hp").innerText = data.playerHP;
					document.getElementById("player-img").src = "img/" + data.playerPid + ".png";
					document.getElementById("player-pkm-name").innerText = data.playerNickname;

					document.getElementById("move-1").innerText = data.move1;
					document.getElementById("move-2").innerText = data.move2;
					document.getElementById("move-3").innerText = data.move3;
					document.getElementById("move-4").innerText = data.move4;

					if (oldHp < newHp) {
						// HP going up means old pokemon fainted and new one switched in
						alert(oldNickname + " has been knocked out! You send out " + newNickname + " to continue the battle.");
					}
				}

				document.getElementById("res").innerText = data.res;
				document.getElementById("msg").innerText = data.msg;

				// Ping server after 2 seconds (acts asynchronously)
				setTimeout(battleUpdate, 2000, uid);
			} else {
				// There was an error... Display error on page
				document.getElementById("res").innerText = "Response: " + data.error;
				document.getElementById("msg").innerText = "Please refresh the page to restart the battle";
				disableMoveButtons(true);
				document.getElementById("start-button").style.display = 'none';
				document.getElementById("battle-div").style.display = 'none';
			}
		}
		});
	}

	function forfeit(uid) {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {uid: uid, forfeit: true},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				document.getElementById("battle-div").style.display = 'none';
				document.getElementById("player-pkm-hp").innerText = 9999;
				alert("You have forfeit and have been declared as the loser of the battle.")
			}
		}
		});
	}

	/**
	* Starts duel and grabs pokemon information to display on screen
	* @param {int} player uid (should be in session storage)
	*/
	function startDuel(uid) {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {uid: uid, startDuel: true},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				// Update pokemon and battle information
				// document.getElementById("res").innerText = "Starting game";
				document.getElementById("start-button").style.display = 'none';

				// Images... Do this first as we need to get img from backend
				document.getElementById("opponent-img").src = "img/" + data.enemy[data.enemyPkmIndex].pid + ".png";
				document.getElementById("player-img").src = "img/" + data.player[data.playerPkmIndex].pid + ".png";

				// Update pokemon name/hp
				document.getElementById("enemy-pkm-name").innerText = data.enemy[data.enemyPkmIndex].nickname;
				document.getElementById("enemy-pkm-hp").innerText = data.enemy[data.enemyPkmIndex].cur_hp + " / " + data.enemy[data.enemyPkmIndex].max_hp + " HP";
				document.getElementById("player-pkm-name").innerText = data.player[data.playerPkmIndex].nickname;
				document.getElementById("player-pkm-hp").innerText = data.player[data.playerPkmIndex].cur_hp + " / " + data.player[data.playerPkmIndex].max_hp + " HP";

				// Populate pokemon moves
				document.getElementById("move-1").innerText = data.player[data.playerPkmIndex].move_1;
				document.getElementById("move-2").innerText = data.player[data.playerPkmIndex].move_2;
				document.getElementById("move-3").innerText = data.player[data.playerPkmIndex].move_3;
				document.getElementById("move-4").innerText = data.player[data.playerPkmIndex].move_4;

				// Lastly, show battle pane
				document.getElementById("battle-div").style.display = '';
			}
			/*
			if (data.searching) {
				document.getElementById("res").innerText = "Searching for an opponent...";
			}
			*/
		}
		});
	}

	/**
	* Plays a move when it is the player's turn
	* @param {int} player uid (should be in session storage)
	* @param {string} string of move number being used (ex 'move_1')... Not actual move name
	*/
	function useMove(uid, move_name) {
		$.ajax({
		url: './battleServer.php',
		type: 'POST',
		data: {uid: uid, useMove: true, move_name: move_name},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.success) {
				// Disable player move buttons and update game info
				disableMoveButtons(true);
				alert(data.effectiveness + " You dealt " + data.damage + " using " + data.move_name + " to " + document.getElementById("enemy-pkm-name").innerText + ".");
				if (data.newPokemon) {
					// Images... Do this first as we need to get img from backend
					document.getElementById("opponent-img").src = "img/" + data.enemyPid + ".png";
					// Update pokemon name/hp
					var oldPokemon = document.getElementById("enemy-pkm-name").innerText;
					var newPokemon = data.enemyNickname;
					document.getElementById("enemy-pkm-name").innerText = data.enemyNickname;

					document.getElementById("move-1").innerText = data.newMove1;
					document.getElementById("move-2").innerText = data.newMove2;
					document.getElementById("move-3").innerText = data.newMove3;
					document.getElementById("move-4").innerText = data.newMove4;

					alert("You have knocked your opponent's " + oldPokemon + ". They have sent out " + newPokemon + " to fight on.");
				}

				document.getElementById("res").innerText = "Opponent's Turn";
				document.getElementById("enemy-pkm-hp").innerText = data.enemyHP;

				document.getElementById("enemy-pkm-remaining").innerText = data.enemyPokemonRemaining;

				if (data.win) {
					var oldPokemon = document.getElementById("enemy-pkm-name").innerText;
					document.getElementById("player-pkm-hp").innerText = 9999;
					document.getElementById("battle-div").style.display = 'none';
					alert("Congratulations! You have won the battle by knocking out your opponent's final Pokemon, " + oldPokemon);
				}
			}
		}
		});
	}

	function disableMoveButtons(shouldDisable) {
		document.getElementById("move-1").disabled = shouldDisable;
		document.getElementById("move-2").disabled = shouldDisable;
		document.getElementById("move-3").disabled = shouldDisable;
		document.getElementById("move-4").disabled = shouldDisable;
		document.getElementById("forfeit").disabled = shouldDisable;
	}

</script>
<style>
.error {color: #FF0000;}
</style>
</head>
<body onload="battleUpdate(<?php echo $_SESSION['uid']?>)">

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

<?php include('navbar.php'); ?>

<div class="container" padding-top="4%">
	<br>
  <h5 id="res">Waiting for response...</h5>
  <p id="msg"></p>
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
				<p id="enemy-pkm-remaining" class="card-text">X / X Pokemon Remaining</p>
				<img id="opponent-img" src="" class="card-img" alt="Opponent Pokemon">
			</div>
		</div>
	</div>
	<div id="player" class="card">
		<div class="row no gutters">
			<div class="col-md-4">
				<img id="player-img" src="" class="card-img" alt="Player Pokemon">
				<p id="player-pkm-remaining" class="card-text">X / X Pokemon Remaining</p>
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
				<button id="forfeit" class="btn" onclick="forfeit(<?php echo $_SESSION['uid']?>)">Forfeit</button>
			</div>
		</div>
	</div>
</div>

<?php

$conn -> close();
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
