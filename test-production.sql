-- Find pokemon that are of type fire and print the pokedex number, name, types and generation -- Only 10 results shown to shorten .out file (pokemon.php)
SELECT pid, name, type1, type2, gen FROM pokemon WHERE type1="fire" OR type2="fire" LIMIT 10;

-- Return pokemon information for individual pokemon stats page (viewPokemonPage.php)
SELECT * FROM pokemon WHERE pid=483;


-- Find players that contain the name admin (index.php)
SELECT * FROM players WHERE name="admin";

-- Create new player (index.php)
INSERT INTO players (uid, name, pin, joined_at) VALUES ("10", "New Player", "1111", now());


-- Find pokemon that have been favourited by a player (pokemon.php)
SELECT * FROM favourite_pokemon WHERE uid=0;

-- Add a pokemon to the favourited list of a player (favouritePokemon.php)
INSERT INTO favourite_pokemon (uid, pid) VALUES (0, 483);

-- Unfavourite a pokemon for a specific player (favouritePokemon.php)
DELETE FROM favourite_pokemon WHERE uid=0 AND pid=483;

