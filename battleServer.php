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
	$battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ")
  AND is_finished=0 AND uid1 IS NOT NULL AND uid2 IS NOT NULL";
	$result = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);
	$playerTurn = $gameInProgress = FALSE;

	// Response/message is used to update game text on frontend
	$response = "Welcome to the Arena";
	$message = "Click 'Start Duel' to join a game.";
  $hpString = "";
  $playerPid = "";
  $playerNickname = "";

  $move1 = "";
  $move2 = "";
  $move3 = "";
  $move4 = "";

  $numAliveQuery = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ") ORDER BY started_at DESC LIMIT 1;";
  $numAliveQueryResult = $conn -> query($numAliveQuery) -> fetch_all(MYSQLI_ASSOC);
  $pokemonAliveIndex = "";
  if ($numAliveQueryResult[0]['uid1'] == $_POST['uid']) {
    $pokemonAliveIndex = "party_alive1";
  } else {
    $pokemonAliveIndex = "party_alive2";
  }

  $pokemonAliveMostRecentOrCurrentBattle = count(explode(',', $numAliveQueryResult[0][$pokemonAliveIndex]));
  if ($numAliveQueryResult[0][$pokemonAliveIndex] == "") {
    $pokemonAliveMostRecentOrCurrentBattle = 0;
  }

  $partySizeQuery = "SELECT COUNT(*) AS count FROM party WHERE uid = " . $_POST['uid'] . ";";
  $partySizeQueryResult = $conn -> query($partySizeQuery) -> fetch_all(MYSQLI_ASSOC);

  $playerPokemonRemainingString = $pokemonAliveMostRecentOrCurrentBattle . " / " . $partySizeQueryResult[0]['count'] . " Pokemon Remaining";

	if (count($result)) {
		$gameInProgress = TRUE;

    $playerIndex = "";
    if ($result[0]['uid1'] == $_POST['uid']) {
      $playerIndex = "pokemon1";
    } else {
      $playerIndex = "pokemon2";
    }
    $currentPokemonQuery = "SELECT " . $playerIndex . " FROM battle WHERE gid = " . $result[0]['gid'] . ";";
    $currentPokemonQueryResult = $conn -> query($currentPokemonQuery) -> fetch_all(MYSQLI_ASSOC);
    $currentPokemonInstQuery = "SELECT * FROM pokemon_inst WHERE iid = " . $currentPokemonQueryResult[0][$playerIndex] . ";";
    $currentPokemonInstQueryResult = $conn -> query($currentPokemonInstQuery) -> fetch_all(MYSQLI_ASSOC);
    $hpString = $currentPokemonInstQueryResult[0]['cur_hp'] . " / " . $currentPokemonInstQueryResult[0]['max_hp'] . " HP";
    $playerPid = $currentPokemonInstQueryResult[0]['pid'];
    $playerNickname = $currentPokemonInstQueryResult[0]['nickname'];
    $move1 = $currentPokemonInstQueryResult[0]['move_1'];
    $move2 = $currentPokemonInstQueryResult[0]['move_2'];
    $move3 = $currentPokemonInstQueryResult[0]['move_3'];
    $move4 = $currentPokemonInstQueryResult[0]['move_4'];

    $enemyParty = "";
    $enemyUid = "";
    if ($result[0]['uid1'] == $_POST['uid']) {
      $enemyParty = "party_alive2";
      $enemyUid = $result[0]['uid2'];
    } else {
      $enemyParty = "party_alive1";
      $enemyUid = $result[0]['uid1'];
    }

    $numRemaining = count(explode(',',$result[0][$enemyParty]));

    $partySizeQuery = "SELECT COUNT(*) AS count FROM party WHERE uid = " . $enemyUid . ";";
    $partySizeQueryResult = $conn -> query($partySizeQuery) -> fetch_all(MYSQLI_ASSOC);

    $enemyPokemonRemainingString = $numRemaining . " / " . $partySizeQueryResult[0]['count'] . " Pokemon Remaining";


		// Since there is a battle in-progress, get turn info to find out whose turn it is next
		// *Should* always be the players turn as bot should play immediately
		$result = $result[0];
		$turnQueryCheck = "SELECT COUNT(*) AS count FROM turn WHERE gid=" . $result['gid'];
		$lastPlayer = $conn->query($turnQueryCheck)->fetch_all(MYSQLI_ASSOC);

		// Player's turn
		if (($result['uid1'] == $_POST['uid'] && $lastPlayer[0]['count'] % 2 == 0)
      || ($result['uid2'] == $_POST['uid'] && $lastPlayer[0]['count'] % 2 == 1)) {
			$playerTurn = TRUE;

			$response = "Player Turn";
			$message = "Click one of your moves.";
		} else {
			$response = "Opponent's Turn";
			$message = "Please wait for your opponent to move.";
		}
	}

  $enemyPokemonRemainingString = "";

	echo json_encode(array("success"=>TRUE, "playerTurn"=>$playerTurn, "gameInProgress"=>$gameInProgress,
    "res"=>$response, "msg"=>$message, "playerHP"=>$hpString, "playerPid"=>$playerPid,
    "playerNickname"=>$playerNickname, "numPartyAlive"=>$pokemonAliveMostRecentOrCurrentBattle,
    "playerPokemonRemaining"=>$playerPokemonRemainingString, "enemyPokemonRemaining"=>$enemyPokemonRemainingString,
    "move1"=>$move1, "move2"=>$move2, "move3"=>$move3, "move4"=>$move4));
} else if (isset($_POST['forfeit']) && isset($_POST['uid']))  {
  // End match
  $battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ")
  AND is_finished=0 AND uid1 IS NOT NULL AND uid2 IS NOT NULL";
	$result = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);

  $partyIndex = "";
  if ($_POST['uid'] == $result[0]['uid1']) {
    $partyIndex = "party_alive1";
  } else {
    $partyIndex = "party_alive2";
  }

  // Heal participating pokemon
  $healPokemonQuery = "UPDATE pokemon_inst SET cur_hp = max_hp WHERE uid = "
    . $result[0]['uid1'] . " OR uid = " . $result[0]['uid2'] . ";";
  $conn -> query($healPokemonQuery);

  $forfeitQuery = "UPDATE battle SET is_finished = TRUE, " . $partyIndex . " = \"\"  WHERE gid = " . $result[0]['gid'] . ";";
  $conn -> query($forfeitQuery);
  echo json_encode(array("success"=>TRUE));
} else if (isset($_POST['startDuel']) && isset($_POST['uid'])) {
  $searching = false;
	// We must first get the parties of both players in the battle. This will mainly be used for creating the battle row and for displaying on the frontend
	$partyQuery1 = 'SELECT * FROM party, pokemon_inst WHERE party.uid=' . $_POST['uid'] . ' AND party.iid=pokemon_inst.iid ORDER BY party.party_order';
	$playerParty = $conn -> query($partyQuery1) -> fetch_all(MYSQLI_ASSOC);

	if (count($playerParty) == 0) {
		// Player party is empty and thus cannot battle
		echo json_encode(array("success"=>FALSE,"error"=>"Player has no party", "searching"=>$searching));
	} else {

		// $partyAlive1 will be used when storing entries in battle
		// Format consists of '#,#,#,#,#,#' where it is a string of iid's delimitted by commas
		$playerFirstPokemon = $partyAlive1 = $playerParty[0]['iid'];
		for ($row = 1; $row < count($playerParty); $row++) {
			$partyAlive1 = $partyAlive1 . "," . $playerParty[$row]['iid'];
		}

		// Must check to see if there already exists a battle with user
		$battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ")
      AND is_finished=0 AND uid1 IS NOT NULL AND uid2 IS NOT NULL;";
		$battleQueryResult = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);

		if (empty($battleQueryResult)) {
      // Do not create new battle if already searching and do not join self
      $alreadySearchingQueryCheck = "SELECT * FROM battle WHERE uid1=" . $_POST['uid'] . " AND is_finished=0;";
  		$alreadySearchingQueryResult = $conn -> query($alreadySearchingQueryCheck) -> fetch_all(MYSQLI_ASSOC);
      if (empty($alreadySearchingQueryResult)) {
        // Check if can join an existing battle looking for second player
        // Must check to see if there already exists a battle with user
    		$battleSearchQueryCheck = "SELECT * FROM battle WHERE uid2 IS NULL AND is_finished=0;";
    		$battleSearchQueryResult = $conn -> query($battleSearchQueryCheck) -> fetch_all(MYSQLI_ASSOC);
        if (empty($battleSearchQueryResult)) {
    			// There is no existing battle, we must create a new one if not already searching for battle
    			$battleStartInsert = "INSERT INTO battle (gid, uid1, uid2, started_at, pokemon1, pokemon2, party_alive1, party_alive2, is_finished) VALUES (null, " . $_POST['uid'] . ", NULL, now(), " .
    									$playerFirstPokemon . ", " . "NULL" . ", '" . $partyAlive1 . "', '" . "NULL" . "', 0);";
    			$conn -> query($battleStartInsert);
          $response = "Searching for opponent...";
          $searching = TRUE;
    			echo json_encode(array("success"=>FALSE, "madeNewBattle"=>FALSE, "playerPkmIndex"=>0, "player"=>$playerParty, "res"=>$response, "searching"=>$searching));
        } else {
          // Join as player 2 of existing battle

          // Same process as the first party... Get the party in order and create the $partyAlive2 val
          $partyQuery2 = 'SELECT * FROM party, pokemon_inst WHERE party.uid=' . $_POST['uid'] . ' AND party.iid=pokemon_inst.iid ORDER BY party.party_order';
          $player2Party = $conn -> query($partyQuery2) -> fetch_all(MYSQLI_ASSOC);

          // $partyAlive2 will be used when storing entries in battle
    			// Format consists of '#,#,#,#,#,#' where it is a string of iid's delimitted by commas
    			$p2FirstPokemon = $partyAlive2 = $player2Party[0]['iid'];
    			for ($row = 1; $row < count($player2Party); $row++) {
    				$partyAlive2 = $partyAlive2 . "," . $player2Party[$row]['iid'];
    			}

          $battleStartUpdate = "UPDATE battle
            SET uid2 = " . $_POST['uid'] . ", pokemon2 = " . $p2FirstPokemon . " , party_alive2 = '" . $partyAlive2 . "'  WHERE gid = " . $battleSearchQueryResult[0]['gid'] . ";";
          $conn -> query($battleStartUpdate);

    			echo json_encode(array("success"=>TRUE, "madeNewBattle"=>TRUE, "playerPkmIndex"=>0, "enemyPkmIndex"=>0, "player"=>$playerParty, "enemy"=>$player2Party, "searching"=>$searching));
        }
      }
      $response = "Searching for opponent...";
      echo json_encode(array("success"=>FALSE, "madeNewBattle"=>FALSE, "playerPkmIndex"=>0, "player"=>$playerParty, "res"=>$response, "searching"=>$searching));
		} else {
      // Get opponent uid
      $opponentUid = "";
      if ($battleQueryResult[0]['uid1'] == $_POST['uid']) {
        $opponentUid = $battleQueryResult[0]['uid2'];
      } else {
        $opponentUid = $battleQueryResult[0]['uid1'];
      }
      // Same process as the first party... Get the party in order and create the $partyAlive2 val
      $partyQuery2 = 'SELECT * FROM party, pokemon_inst WHERE party.uid=' . $opponentUid. ' AND party.iid=pokemon_inst.iid ORDER BY party.party_order';
    	$player2Party = $conn -> query($partyQuery2) -> fetch_all(MYSQLI_ASSOC);

      // $partyAlive2 will be used when storing entries in battle
      // Format consists of '#,#,#,#,#,#' where it is a string of iid's delimitted by commas
      $p2FirstPokemon = $partyAlive2 = $player2Party[0]['iid'];
      for ($row = 1; $row < count($player2Party); $row++) {
        $partyAlive2 = $partyAlive2 . "," . $player2Party[$row]['iid'];
      }

			// There exists a battle already
			$playerIndex = "pokemon1";
			$enemyIndex = "pokemon2";
			if ($battleQueryResult[0]['uid2'] == $_POST['uid']) {
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
				if ($enemyPkmArr[$e] == $battleQueryResult[0][$enemyIndex]) {
					$eIndex = $e;
				}
			}

			// Return game info to frontend... $playerParty contains the party/pokemon_inst values for the pokemon on each team (array of assoc arrays)
			echo json_encode(array("success"=>TRUE, "madeNewBattle"=>FALSE, "playerPkmIndex"=>$pIndex, "enemyPkmIndex"=>$eIndex, "player"=>$playerParty, "enemy"=>$player2Party));
		}
	}
} else if (isset($_POST['useMove']) && isset($_POST['uid']) && isset($_POST['move_name'])) {
  $newPokemon = FALSE;
  $win = FALSE;
  $damage = 0;
  $effectiveness = "";
  $actual_move_name = "";

	// Get battle that the player is in
	$battleQueryCheck = "SELECT * FROM battle WHERE (uid1=" . $_POST['uid'] . " OR uid2=" . $_POST['uid'] . ") AND is_finished=0";
	$battleQueryResult = $conn -> query($battleQueryCheck) -> fetch_all(MYSQLI_ASSOC);

	if (count($battleQueryResult)) {
		$turnQueryCheck = "SELECT COUNT(*) AS count FROM turn WHERE gid=" . $battleQueryResult[0]['gid'];
		$lastPlayer = $conn->query($turnQueryCheck)->fetch_all(MYSQLI_ASSOC);

		// Player's turn
		if (($battleQueryResult[0]['uid1'] == $_POST['uid'] && $lastPlayer[0]['count'] % 2 == 0)
      || ($battleQueryResult[0]['uid2'] == $_POST['uid'] && $lastPlayer[0]['count'] % 2 == 1)) {

			// vars are used for determining the pokemon instance and move results
			$playerIndex = "";
			$enemyIndex = "";
      // Get opponent uid
      $enemyUid = "";
      $enemyParty = "";
      if ($battleQueryResult[0]['uid1'] == $_POST['uid']) {
        $enemyUid = $battleQueryResult[0]['uid2'];
        $playerIndex = "pokemon1";
        $enemyIndex = "pokemon2";
        $enemyParty = "party_alive2";
      } else {
        $enemyUid = $battleQueryResult[0]['uid1'];
        $playerIndex = "pokemon2";
  			$enemyIndex = "pokemon1";
        $enemyParty = "party_alive1";
      }
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
				$enemyPkmInstQuery = "SELECT * FROM pokemon_inst WHERE uid=" . $enemyUid . " AND iid=" . $battleQueryResult[0][$enemyIndex] . ";";
				$enemyPkmInstResult = $conn -> query($enemyPkmInstQuery) -> fetch_all(MYSQLI_ASSOC);

				if (count($enemyPkmInstResult)) {
					// Assuming both pokemon exist => get new hp, update pokemon_inst with hp, create new turn row with this move and return HP string to frontend
          $actual_move_name = $pkmInstResult[0][$_POST['move_name']];

          $moveQuery = "SELECT * FROM move WHERE move_name = '" . $actual_move_name . "';";
  				$moveQueryResult = $conn -> query($moveQuery) -> fetch_all(MYSQLI_ASSOC);
          $moveType = $moveQueryResult[0]['type'];
          $damage = $moveQueryResult[0]['base_power'] * 0.8;

          $enemyPokemonSpecQuery = "SELECT type1, COALESCE(type2, '???') AS type2NN FROM pokemon WHERE pid = " . $enemyPkmInstResult[0]['pid'] . ";";
          $enemyPokemonSpecQueryResult = $conn -> query($enemyPokemonSpecQuery) -> fetch_all(MYSQLI_ASSOC);
          $firstDefendingType = $enemyPokemonSpecQueryResult[0]['type1'];
          $secondDefendingType = $enemyPokemonSpecQueryResult[0]['type2NN'];

          $firstTypeMultQuery = "SELECT damage_factor FROM type_matchup
            WHERE attacking_type = '" . $moveType . "' AND defending_type = '" . $firstDefendingType . "';";
  				$firstTypeMultQueryResult = $conn -> query($firstTypeMultQuery) -> fetch_all(MYSQLI_ASSOC);
          $effectiveness = $firstTypeMultQueryResult[0]['damage_factor'];

          if ($secondDefendingType != "???") {
            $secondTypeMultQuery = "SELECT damage_factor FROM type_matchup
              WHERE attacking_type = '" . $moveType . "' AND defending_type = '" . $secondDefendingType . "';";
    				$secondTypeMultQueryResult = $conn -> query($secondTypeMultQuery) -> fetch_all(MYSQLI_ASSOC);
            $effectiveness = $effectiveness * $secondTypeMultQueryResult[0]['damage_factor'];
          }

          $damage = $damage * $effectiveness;

          if ($effectiveness <= 0.25) {
            $effectiveness = "It's barely effective...";
          } else if ($effectiveness <= 0.75) {
            $effectiveness = "It's not very effective...";
          } else if ($effectiveness <= 1.25) {
            $effectiveness = "It's okay.";
          } else {
            $effectiveness = "It's super effective!";
          }

          $damage = max(1, $damage);

          $newCurHp = max($enemyPkmInstResult[0]['cur_hp'] - $damage, 0);
					$updateEnemyQuery = "UPDATE pokemon_inst SET cur_hp=" . $newCurHp . " WHERE uid=" . $enemyUid . " AND iid=" . $battleQueryResult[0][$enemyIndex] . ";";
					$conn -> query($updateEnemyQuery);

					$newTurnNumber = $lastPlayer[0]['count'] + 1;

					$insertTurnQuery = "INSERT INTO turn (turn_number, gid, uid, move_at, pid, move_name) VALUES (" . $newTurnNumber . ", " . $battleQueryResult[0]['gid'] .
										", " . $_POST['uid'] . ", now(), " . $pkmInstResult[0]['pid'] . ", '" . $pkmInstResult[0][$_POST['move_name']] . "');";
					$conn -> query($insertTurnQuery);

					$hpString = $newCurHp . " / " . $enemyPkmInstResult[0]['max_hp'] . " HP";

          $newEnemyNickname = "";
          $newEnemyPid = "";
          $newMove1 = "";
          $newMove2 = "";
          $newMove3 = "";
          $newMove4 = "";

          // Opponent fainted
          if ($newCurHp <= 0) {
            $enemyPartyArr = explode(',',$battleQueryResult[0][$enemyParty]);
            // Has next pokemon
            if (count($enemyPartyArr) > 1) {
              $newPokemon = TRUE;
              $nextPokemonQuery = "UPDATE battle SET " . $enemyIndex . " = " . $enemyPartyArr[1] . " WHERE gid = ". $battleQueryResult[0]['gid'] . ";";
    					$conn -> query($nextPokemonQuery);

              $newPokemonId = $enemyPartyArr[1];
              $newEnemyQuery = "SELECT * FROM pokemon_inst WHERE iid = " . $newPokemonId . ";";
              $newEnemyQueryResult = $conn -> query($newEnemyQuery) -> fetch_all(MYSQLI_ASSOC);
              $newEnemyNickname = $newEnemyQueryResult[0]['nickname'];
              $newEnemyPid = $newEnemyQueryResult[0]['pid'];
              $hpString = $newEnemyQueryResult[0]['cur_hp'] . " / " . $newEnemyQueryResult[0]['max_hp'] . " HP";
              $newMove1 = $newEnemyQueryResult[0]['move_1'];
              $newMove2 = $newEnemyQueryResult[0]['move_2'];
              $newMove3 = $newEnemyQueryResult[0]['move_3'];
              $newMove4 = $newEnemyQueryResult[0]['move_4'];

              $newAliveParty = $enemyPartyArr[1];
              for ($row = 2; $row < count($enemyPartyArr); $row++) {
        				$newAliveParty = $newAliveParty . "," . $enemyPartyArr[$row];
        			}
              $faintPokemonQuery = "UPDATE battle SET " . $enemyParty . " = '" . $newAliveParty . "' WHERE gid = ". $battleQueryResult[0]['gid'] . ";";
    					$conn -> query($faintPokemonQuery);
            } else {
              // Game over since no next Pokemon
              $win = TRUE;
              $gameOverQuery = "UPDATE battle SET is_finished = TRUE, " . $enemyParty . " = \"\" WHERE gid = ". $battleQueryResult[0]['gid'] . ";";
    					$conn -> query($gameOverQuery);

              // Heal participating pokemon
              $healPokemonQuery = "UPDATE pokemon_inst SET cur_hp = max_hp WHERE uid = "
                . $battleQueryResult[0]['uid1'] . " OR uid = " . $battleQueryResult[0]['uid2'] . ";";
    					$conn -> query($healPokemonQuery);
            }
          }

          $numRemaining = count(explode(',',$battleQueryResult[0][$enemyParty]));

          $partySizeQuery = "SELECT COUNT(*) AS count FROM party WHERE uid = " . $enemyUid . ";";
          $partySizeQueryResult = $conn -> query($partySizeQuery) -> fetch_all(MYSQLI_ASSOC);

          $enemyPokemonRemainingString = $numRemaining . " / " . $partySizeQueryResult[0]['count'] . " Pokemon Remaining";

					echo json_encode(array("success"=>TRUE, "enemyHP"=>$hpString, "newPokemon"=>$newPokemon,
          "newMove1"=>$newMove1, "newMove2"=>$newMove2, "newMove3"=>$newMove3, "newMove4"=>$newMove4,
          "enemyNickname"=>$newEnemyNickname, "enemyPid"=>$newEnemyPid, "win"=>$win,
          "enemyPokemonRemaining"=>$enemyPokemonRemainingString,
          "effectiveness"=>$effectiveness, "damage"=>$damage, "move_name"=>$actual_move_name));
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
