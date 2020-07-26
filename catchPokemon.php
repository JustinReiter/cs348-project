<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Pokemon Management System</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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

<div class="container" style="padding-top: 2%">
</div>

<?php
echo "<div class=\"container\">";
echo "<h2>Welcome to the Pokemon Safari Zone</h2>";
echo "<br>Here, you can explore the places Pokemon call home.<br>";
echo "<br>Perhaps you can even make a new Pokemon friend on your adventure!<br>";
echo "</div>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["explore"])) {
    // Generate random Pokemon species
    $_SESSION["random_pokemon_inst"] = array();

    $numPokemonQuery = "SELECT COUNT(*) FROM pokemon";
  	$numPokemon = $conn -> query($numPokemonQuery) -> fetch_row()[0];

  	$randPokemonIndex = mt_rand(0, $numPokemon - 1);
  	$randPokemonQuery = "SELECT * FROM pokemon LIMIT 1 OFFSET " . $randPokemonIndex;
  	$_SESSION["random_pokemon_inst"]["base_pokemon"] = $conn -> query($randPokemonQuery) -> fetch_row();

    // Generate random stat variations
    $random_stat_multipliers = array();
    for ($i = 0; $i < 6; $i++) {
      // Generate multiplier in [0.75, 1.25) for each stat
      $random_stat_multipliers[$i]
        = ((mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax()) / 2.0) + 0.75;
    }
    $base_attack = $_SESSION["random_pokemon_inst"]["base_pokemon"][0];
    $base_defense = $_SESSION["random_pokemon_inst"]["base_pokemon"][5];
    $base_hp = $_SESSION["random_pokemon_inst"]["base_pokemon"][8];
    $base_sp_atk = $_SESSION["random_pokemon_inst"]["base_pokemon"][12];
    $base_sp_def = $_SESSION["random_pokemon_inst"]["base_pokemon"][13];
    $base_speed = $_SESSION["random_pokemon_inst"]["base_pokemon"][14];
    $_SESSION["random_pokemon_inst"]["attack"] = intval($random_stat_multipliers[0] * $base_attack);
    $_SESSION["random_pokemon_inst"]["defense"] = intval($random_stat_multipliers[1] * $base_defense);
    $_SESSION["random_pokemon_inst"]["max_hp"] = intval($random_stat_multipliers[2] * $base_hp);
    $_SESSION["random_pokemon_inst"]["sp_atk"] = intval($random_stat_multipliers[3] * $base_sp_atk);
    $_SESSION["random_pokemon_inst"]["sp_def"] = intval($random_stat_multipliers[4] * $base_sp_def);
    $_SESSION["random_pokemon_inst"]["speed"] = intval($random_stat_multipliers[5] * $base_speed);

    // Generate gender
    $percent_male = $_SESSION["random_pokemon_inst"]["base_pokemon"][10];
    if (is_null($percent_male)) {
      $_SESSION["random_pokemon_inst"]["gender"] = "Unknown";
    } else {
      $randGenderNum = mt_rand(0,99);
      if ($randGenderNum < $percent_male) {
        $_SESSION["random_pokemon_inst"]["gender"] = "Male";
      } else {
        $_SESSION["random_pokemon_inst"]["gender"] = "Female";
      }
    }

    // Generate random moves
    $pid = $_SESSION["random_pokemon_inst"]["base_pokemon"][11];

    $numLearnableMoveQuery = "SELECT COUNT(*) FROM learnable_move WHERE pid = " . $pid;
  	$numLearnableMove = $conn -> query($numLearnableMoveQuery) -> fetch_row()[0];

    $randMoveIndex = mt_rand(0, $numLearnableMove - 1);
    $randMoveQuery = "SELECT move_name FROM learnable_move WHERE pid
      = " . $pid . " LIMIT 1 OFFSET " . $randMoveIndex;
  	$_SESSION["random_pokemon_inst"]["move_1"] = $conn -> query($randMoveQuery) -> fetch_row()[0];

    $randMoveIndex = mt_rand(0, $numLearnableMove - 1);
    $randMoveQuery = "SELECT move_name FROM learnable_move WHERE pid
      = " . $pid . " LIMIT 1 OFFSET " . $randMoveIndex;
  	$_SESSION["random_pokemon_inst"]["move_2"] = $conn -> query($randMoveQuery) -> fetch_row()[0];

    $randMoveIndex = mt_rand(0, $numLearnableMove - 1);
    $randMoveQuery = "SELECT move_name FROM learnable_move WHERE pid
      = " . $pid . " LIMIT 1 OFFSET " . $randMoveIndex;
  	$_SESSION["random_pokemon_inst"]["move_3"] = $conn -> query($randMoveQuery) -> fetch_row()[0];

    $randMoveIndex = mt_rand(0, $numLearnableMove - 1);
    $randMoveQuery = "SELECT move_name FROM learnable_move WHERE pid
      = " . $pid . " LIMIT 1 OFFSET " . $randMoveIndex;
  	$_SESSION["random_pokemon_inst"]["move_4"] = $conn -> query($randMoveQuery) -> fetch_row()[0];

    unset($_POST["explore"]);

  } else if (isset($_SESSION["random_pokemon_inst"]) && isset($_POST["accept"])) {
    $base_happiness = $_SESSION['random_pokemon_inst']['base_pokemon'][2];
    $name = $_SESSION['random_pokemon_inst']['base_pokemon'][9];
    $pid = $_SESSION["random_pokemon_inst"]["base_pokemon"][11];
    $insertPokemonInstQuery = "INSERT INTO pokemon_inst
      (iid, cur_hp, max_hp, attack, defense, sp_atk, sp_def, speed, happiness, exp,
      nickname, gender, move_1, move_2, move_3, move_4, pid, uid)
      VALUES (null, " . $_SESSION['random_pokemon_inst']['max_hp'] . ", " . $_SESSION['random_pokemon_inst']['max_hp'] . ", "
        . $_SESSION['random_pokemon_inst']['attack'] . ", " . $_SESSION['random_pokemon_inst']['defense'] . ", "
        . $_SESSION['random_pokemon_inst']['sp_atk'] . ", " . $_SESSION['random_pokemon_inst']['sp_def'] . ", "
        . $_SESSION['random_pokemon_inst']['speed'] . ", " . $base_happiness . ", 0, '" . $name . "', '" . $_SESSION['random_pokemon_inst']['gender'] . "', '"
        . $_SESSION['random_pokemon_inst']['move_1'] . "', '" . $_SESSION['random_pokemon_inst']['move_2'] . "', '"
        . $_SESSION['random_pokemon_inst']['move_3'] . "', '" . $_SESSION['random_pokemon_inst']['move_4'] . "', "
        . $pid . ", " . $_SESSION['uid'] . ")";
    $conn -> query($insertPokemonInstQuery);

    $name = $_SESSION["random_pokemon_inst"]["base_pokemon"][9];
    echo "<div class=\"container\">";
    echo $name . " joined your Pokemon Management System!<br>";
    echo "</div>";
    unset($_POST["accept"]);
    unset($_SESSION["random_pokemon_inst"]);

  } else if (isset($_SESSION["random_pokemon_inst"]) && isset($_POST["decline"])) {
    $name = $_SESSION["random_pokemon_inst"]["base_pokemon"][9];
    echo "<div class=\"container\">";
    echo $name . " left.<br>";
    echo "</div>";
    unset($_POST["decline"]);
    unset($_SESSION["random_pokemon_inst"]);
  }
}

function pretty_print_diff($inst_stat, $base_stat) {
  $printStr = "";
  $diff = $inst_stat - $base_stat;
  if ($diff < 0) {
    $printStr = " <span style='color:red;'>(" . $printStr . $diff . ")</span>";
  } else if ($diff > 0) {
    $printStr = " <span style='color:green;'>(+" . $printStr . $diff . ")</span>";
  } else {
    $printStr = " (" . $printStr . $diff . ")";
  }
  return $printStr;
}

?>

<div class="container" style="padding-top: 2%">
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <?php if (!isset($_SESSION["random_pokemon_inst"])) { ?>
      <input class="btn btn-primary" type="submit" name="explore" value="Explore!">
    <?php } else { ?>
        <?php
          $pname = $_SESSION['random_pokemon_inst']['base_pokemon'][9];
          $pid = $_SESSION['random_pokemon_inst']['base_pokemon'][11];

          echo "<h2>You found a ". $pname . " who is interested in joining your team!</h2>";

          echo "<input class=\"btn btn-primary\" type=\"submit\" name=\"accept\" value=\"Accept!\">";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;";
          echo "<input class=\"btn btn-primary\" type=\"submit\" name=\"decline\" value=\"Decline.\">";

          echo "<br>";

          echo "<div class='container' style='padding-top:4%'>";
            echo "<div class='card'>";
              echo "<div class='row no gutters'>";

        	echo '<div class="col-md-4">';
        	echo '<div class="card-body">';

        	echo "<h5 class='card-title'>" . $pname . "</h5>";

        	echo "<p class='card-text'>Pokedex Number: " . $pid . "</p>";

          echo "<p class='card-text'>Gender: " . $_SESSION['random_pokemon_inst']['gender'] . "</p>";

          echo '<img src="img/' . $pid . '.png" class="card-img" alt="' . $pname . ' image" style="padding-left: 2%; padding-top: 2%;">';

        	echo "</div></div>";
          echo "<div class='col-md-4'><div class='card-body'>";

          $base_attack = $_SESSION["random_pokemon_inst"]["base_pokemon"][0];
          $base_defense = $_SESSION["random_pokemon_inst"]["base_pokemon"][5];
          $base_hp = $_SESSION["random_pokemon_inst"]["base_pokemon"][8];
          $base_sp_atk = $_SESSION["random_pokemon_inst"]["base_pokemon"][12];
          $base_sp_def = $_SESSION["random_pokemon_inst"]["base_pokemon"][13];
          $base_speed = $_SESSION["random_pokemon_inst"]["base_pokemon"][14];
        	echo "<p class='card-text'>Max HP: " . $_SESSION['random_pokemon_inst']['max_hp'] . pretty_print_diff($_SESSION['random_pokemon_inst']['max_hp'], $base_hp) . "</p>";
        	echo "<p class='card-text'>Attack: " . $_SESSION['random_pokemon_inst']['attack'] . pretty_print_diff($_SESSION['random_pokemon_inst']['attack'], $base_attack) . "</p>";
        	echo "<p class='card-text'>Base Defense: " . $_SESSION['random_pokemon_inst']['defense'] . pretty_print_diff($_SESSION['random_pokemon_inst']['defense'], $base_defense) . "</p>";
        	echo "<p class='card-text'>Base Sp. Atk: " . $_SESSION['random_pokemon_inst']['sp_atk'] . pretty_print_diff($_SESSION['random_pokemon_inst']['sp_atk'], $base_sp_atk) . "</p>";
        	echo "<p class='card-text'>Base Sp. Def: " . $_SESSION['random_pokemon_inst']['sp_def'] . pretty_print_diff($_SESSION['random_pokemon_inst']['sp_def'], $base_sp_def) . "</p>";
        	echo "<p class='card-text'>Base Speed: " . $_SESSION['random_pokemon_inst']['speed'] . pretty_print_diff($_SESSION['random_pokemon_inst']['speed'], $base_speed) . "</p><br>";
          echo "</div>";
          echo "</div>";

          echo '<div class="col-md-4">';
          echo '<div class="card-body">';

          echo "<p class='card-text'>Move 1: " . $_SESSION['random_pokemon_inst']['move_1'] . "</p>";
          if (isset($_SESSION['random_pokemon_inst']['move_2'])) {
            echo "<p class='card-text'>Move 2: " . $_SESSION['random_pokemon_inst']['move_2'] . "</p>";
          }
          if (isset($_SESSION['random_pokemon_inst']['move_3'])) {
            echo "<p class='card-text'>Move 3: " . $_SESSION['random_pokemon_inst']['move_3'] . "</p>";
          }
          if (isset($_SESSION['random_pokemon_inst']['move_4'])) {
            echo "<p class='card-text'>Move 4: " . $_SESSION['random_pokemon_inst']['move_4'] . "</p>";
          }
            echo "</div>";
          echo "</div>";
        echo "</div>";
        ?>
    <?php } ?>
  </form>
</div>

<?php
$conn -> close();
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
