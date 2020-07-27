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

//! Check what type of update we need to provide
///	getUpdate -> pinged every 5 sec... Just display whose turn it is and basic game info
/// startDuel -> used when player goes to battle page... Create new battle if no unfinished battles exist, else return in-progress game
/// useMove -> used for player turns when a user makes a move
if (isset($_POST['getUpdate']) && isset($_POST['uid'])) {

	// Find battle the user is in... For now the user should be in uid1 and the bot in uid2 unless bad happened
	$battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ") AND is_finished=0";
	$result = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);
	$playerTurn = $gameInProgress = FALSE;
		
	// Response/message is used to update game text on frontend
	$response = "Welcome to the Arena";
	$message = "Click 'Start Duel' to join a game.";

	if (count($result)) {
		$gameInProgress = TRUE;

		// Since there is a battle in-progress, get turn info to find out whose turn it is next
		// *Should* always be the players turn as bot should play immediately
		$result = $result[0];
		$turnQueryCheck = "SELECT uid, MAX(turn_number) FROM turn WHERE gid=" . $result['gid'] . " GROUP BY uid";
		$lastPlayer = $conn->query($turnQueryCheck)->fetch_all(MYSQLI_ASSOC);

		// Player will always go first, therefore if turn entries are empty for the battle, is it the player's turn
		if (empty($lastPlayer) || $lastPlayer[0]['uid'] != $_POST['uid']) {
			$playerTurn = TRUE;
			
			$response = "Player Turn";
			$message = "Click one of your moves.";
		} else {
			$response = "Opponent's Turn";
			$message = "Please wait for you opponent to move.";
		}
	}
	
	echo json_encode(array("success"=>TRUE, "playerTurn"=>$playerTurn, "gameInProgress"=>$gameInProgress, "res"=>$response, "msg"=>$message));
} else if (isset($_POST['startDuel']) && isset($_POST['uid'])) {

	// We must first get the parties of both players in the battle. This will mainly be used for creating the battle row and for displaying on the frontend
	$partyQuery1 = 'SELECT * FROM party, pokemon_inst WHERE party.uid=' . $_POST['uid'] . ' AND party.iid=pokemon_inst.iid ORDER BY party.party_order';
	$playerParty = $conn -> query($partyQuery1) -> fetch_all(MYSQLI_ASSOC);
	
	if (count($playerParty) == 0) {
		// Player party is empty and thus cannot battle
		echo json_encode(array("success"=>FALSE,"error"=>"Player has no party"));
	} else {
		
		// $partyAlive1 will be used when storing entries in battle
		// Format consists of '#,#,#,#,#,#' where it is a string of iid's delimitted by commas
		$playerFirstPokemon = $partyAlive1 = $playerParty[0]['iid'];
		for ($row = 1; $row < count($playerParty); $row++) {
			$partyAlive1 = $partyAlive1 . "," . $playerParty[$row]['iid'];
		}

		// Same process as the first party... Get the party in order and create the $partyAlive2 val
		$partyQuery2 = 'SELECT * FROM party, pokemon_inst WHERE party.uid="3" AND party.iid=pokemon_inst.iid ORDER BY party.party_order';
		$botParty = $conn -> query($partyQuery2) -> fetch_all(MYSQLI_ASSOC);
		
		if (count($botParty) == 0) {
			// Second player's party is empty (shouldn't be possible now as it is the bot)
			echo json_encode(array("success"=>FALSE,"error"=>"Bot has no party"));
		} else {

			// $partyAlive2 will be used when storing entries in battle
			// Format consists of '#,#,#,#,#,#' where it is a string of iid's delimitted by commas
			$botFirstPokemon = $partyAlive2 = $botParty[0]['iid'];
			for ($row = 1; $row < count($botParty); $row++) {
				$partyAlive2 = $partyAlive2 . "," . $botParty[$row]['iid'];
			}

			// Must check to see if there already exists a battle
			$battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ") AND is_finished=0";
			$battleQueryResult = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);
			
			if (empty($battleQueryResult)) {
				// There is no existing battle, we must create a new one
				$battleStartInsert = "INSERT INTO battle (gid, uid1, uid2, started_at, pokemon1, pokemon2, party_alive1, party_alive2, is_finished) VALUES (null, " . $_POST['uid'] . ", 3, now(), " . 
										$playerFirstPokemon . ", " . $botFirstPokemon . ", '" . $partyAlive1 . "', '" . $partyAlive2 . "', 0);";
				$conn -> query($battleStartInsert);
				echo json_encode(array("success"=>TRUE, "madeNewBattle"=>TRUE, "playerPkmIndex"=>0, "enemyPkmIndex"=>0, "player"=>$playerParty, "enemy"=>$botParty));
			} else {
				// There exists a battle already
				$playerIndex = "pokemon1";
				$enemyIndex = "pokemon2";
				if ($battleQueryResult[0]['uid1'] == $_POST['uid']) {
					$playerIndex = "pokemon2";
					$enemyIndex = "pokemon1";
				}
					
				// Player index values for current pokemon
				// pIndex => where in the partyAlive1 value the pokemon is, used for updating the current pokemon and displaying game
				$pIndex = 0;
				$eIndex = 0;
				
				$playerPkmArr = explode(',', $partyAlive1);
				for ($p = 0; $p < count($playerPkmArr); $p++) {
					if ($playerPkmArr[$p] == $battleQueryResult[0][$playerIndex]) {
						$pIndex = $p;
					}
				}

				// Same as party 1
				$enemyPkmArr = explode(',', $partyAlive2);
				for ($e = 0; $e < count($enemyPkmArr); $e++) {
					if ($enemyPkmArr[$p] == $battleQueryResult[0][$enemyIndex]) {
						$eIndex = $e;
					}
				}

				// Return game info to frontend... $playerParty contains the party/pokemon_inst values for the pokemon on each team (array of assoc arrays)
				echo json_encode(array("success"=>TRUE, "madeNewBattle"=>FALSE, "playerPkmIndex"=>$pIndex, "enemyPkmIndex"=>$eIndex, "player"=>$playerParty, "enemy"=>$botParty));
			}
			
			
		}
	}
} else if (isset($_POST['useMove']) && isset($_POST['uid']) && isset($_POST['move_name'])) {

	// Get battle that the player is in
	$battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ") AND is_finished=0";
	$battleQueryResult = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);

	if (count($battleQueryResult)) {
		// Get the current turn to ensure the player move is valid (Should always be unless player janks out frontend)
		$turnQueryCheck = "SELECT uid, MAX(turn_number) FROM turn WHERE gid=" . $battleQueryResult[0]['gid'] . " GROUP BY uid";
		$lastPlayer = $conn->query($turnQueryCheck)->fetch_all(MYSQLI_ASSOC);

		// It is the player turn if no turn rows exists for the game, or if the last turn was not the players turn
		if (count($lastPlayer) == 0 || $lastPlayer[0]['uid'] != $_POST['uid']) {
			// vars are used for determining the pokemon instance and move results
			$playerIndex = "pokemon1";
			$enemyIndex = "pokemon2";
			$enemyUid = $battleQueryResult[0]['uid2'];
			//! this commented section is determining which uid1/uid2 slot in the battle database corresponds to the player (for now should always be uid1)

			// if ($battleQueryResult[0]['uid1'] == $_POST['uid']) {
			// 	$playerIndex = "pokemon2";
			// 	$enemyIndex = "pokemon1";
			// 	$enemyUid = $battleQueryResult[0]['uid1'];
			// }

			// Get the players pokemon so we can determine the move (not used atm)
			// Should just need to query the move database to get the move info and then type matchup for dmg calc
			$pkmInstQuery = "SELECT * FROM pokemon_inst WHERE uid=" . $_POST['uid'] . " AND iid=" . $battleQueryResult[0][$playerIndex] . ";";
			$pkmInstResult = $conn -> query($pkmInstQuery) -> fetch_all(MYSQLI_ASSOC);
			if (count($pkmInstResult)) {
					
				// Get the enemy pokemon so we can apply the dmg
				$enemyPkmInstQuery = "SELECT cur_hp, max_hp FROM pokemon_inst WHERE uid=" . $enemyUid . " AND iid=" . $battleQueryResult[0][$enemyIndex] . ";";
				$enemyPkmInstResult = $conn -> query($pkmInstQuery) -> fetch_all(MYSQLI_ASSOC);

				if (count($enemyPkmInstResult)) {
					// Assuming both pokemon exist => get new hp, update pokemon_inst with hp, create new turn row with this move and return HP string to frontend
					$newCurHp = max($enemyPkmInstResult[0]['cur_hp'] - 10, 0);
					$updateEnemyQuery = "UPDATE pokemon_inst SET cur_hp=" . $newCurHp . " WHERE uid=" . $enemyUid . " AND iid=" . $battleQueryResult[0][$enemyIndex] . ";";
					$conn -> query($updateEnemyQuery);

					$newTurnNumber = (count($lastPlayer) ? $lastPlayer[0]['turn_number'] : 0) + 1;

					$insertTurnQuery = "INSERT INTO turn (turn_number, gid, uid, move_at, pid, move_name) VALUES (" . $newTurnNumber . ", " . $battleQueryResult[0]['gid'] .
										", " . $_POST['uid'] . ", now(), " . $pkmInstResult[0]['pid'] . ", '" . $pkmInstResult[0][$_POST['move_name']] . "');";
					$conn -> query($insertTurnQuery);

					$hpString = $newCurHp . " / " . $enemyPkmInstResult[0]['max_hp'] . " HP";
					echo json_encode(array("success"=>TRUE, "enemyHP"=>$hpString));
				} else {
					echo json_encode(array("success"=>FALSE, "query"=>$pkmInstQuery, "error"=>"Could not find enemy pokemon instance."));
				}
			} else {
				echo json_encode(array("success"=>FALSE, "query"=>$pkmInstQuery, "error"=>"Could not find player pokemon instance."));
			}
		} else {
			echo json_encode(array("success"=>FALSE,"error"=>"It is not your turn."));
		}
	} else {
		echo json_encode(array("success"=>FALSE,"error"=>"Game not started."));
	}
} else {
	echo json_encode(array("success"=>FALSE,"error"=>"Bad request"));
}

$conn->close();
?>

