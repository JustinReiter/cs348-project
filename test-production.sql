-- Find pokemon that are of type fire and print the pokedex number, name, types and generation -- Only 10 results shown to shorten .out file (pokemon.php)
SELECT pid, name, type1, type2, gen FROM pokemon WHERE type1="fire" OR type2="fire" LIMIT 10;

-- Return pokemon information for individual pokemon stats page (viewPokemonPage.php)
SELECT * FROM pokemon WHERE pid=483;

-- Find players that contain the name admin (index.php)
SELECT * FROM player WHERE name="admin";

-- Create new player (index.php)
INSERT INTO player (uid, name, pin, joined_at) VALUES (NULL, "New Player", "1111", now());

-- Find pokemon that have been favourited by a player before insert (pokemon.php)
SELECT * FROM favourite_pokemon WHERE uid=1;

-- Add a pokemon to the favourited list of a player (favouritePokemon.php)
INSERT INTO favourite_pokemon (uid, pid) VALUES (1, 483);

-- Find pokemon that have been favourited by a player after insert (pokemon.php)
SELECT * FROM favourite_pokemon WHERE uid=1;

-- Unfavourite a pokemon for a specific player (favouritePokemon.php)
DELETE FROM favourite_pokemon WHERE uid=1 AND pid=483;

-- Find pokemon that have been favourited by a player after delete (pokemon.php)
SELECT * FROM favourite_pokemon WHERE uid=1;

-- Selects how many moves a Pokemon with pid 1 (Bulbasaur) can learn (catchPokemon.php)
SELECT COUNT(*) FROM learnable_move WHERE pid = 1;

-- Returns the learnable move at index 1 (indexed from 0) of learnable moves
-- for Pokemon with pid 1 (Bulbasaur) (catchPokemon.php)
SELECT move_name FROM learnable_move WHERE pid = 1 LIMIT 1 OFFSET 1;

-- Insert instance of Bulbasaur (pid = 1) into pokemon_inst table for admin user
INSERT INTO pokemon_inst
  (iid, cur_hp, max_hp, attack, defense, sp_atk, sp_def, speed, happiness, exp,
  nickname, gender, move_1, move_2, move_3, move_4, pid, uid)
  VALUES (null, 60, 60, 50, 55, 60, 60, 40, 200, 0, "Bulbasaur", "Male",
  "grass_move", "poison_move", "grass_move", null, 1, 1);

-- Insert Bulbasaur instance to admin user party
INSERT INTO party
  (uid, iid, party_order)
  VALUES (1, 1, 0);

-- Get size of admin user party
SELECT COUNT(*) FROM party WHERE uid = 1;

-- Get the number of times a Bulbasaur appears in a party
-- In practice, it is used to check if the pokemon_inst is in a party
SELECT COUNT(*) FROM party WHERE iid = 1;

-- Get the position of Bulbasaur in the party of the admin user
SELECT party_order FROM party WHERE iid = 1 AND uid = 1;

-- Get Pokemon details of all Pokemon in admin user party in party order
SELECT p.pid, p.name, i.max_hp, i.attack, i.defense, i.sp_atk, i.sp_def, i.speed,
  i.move_1, i.move_2, i.move_3, i.move_4, i.party_iid, i.nickname, i.party_order
  FROM (
    SELECT max_hp, attack, defense, sp_atk, sp_def, speed, pid,
    move_1, move_2, move_3, move_4, party_order, party.iid AS party_iid, nickname
    FROM party INNER JOIN pokemon_inst ON party.iid = pokemon_inst.iid WHERE party.uid = 1
  ) AS i,
  pokemon AS p
  WHERE i.pid = p.pid ORDER BY i.party_order;

-- Get Pokemon details of all Pokemon in admin user collection in
-- Pokedex number (pid) order
SELECT p.pid, p.name, i.max_hp, i.attack, i.defense, i.sp_atk, i.sp_def, i.speed,
i.move_1, i.move_2, i.move_3, i.move_4, i.iid, i.nickname
  FROM pokemon_inst AS i, pokemon AS p WHERE i.pid = p.pid AND i.uid = 1 ORDER BY p.pid;

-- Remove Bulbasaur from the party of the admin user
DELETE FROM party WHERE iid = 1 AND uid = 1;

-- Remove Bulbasaur from the collection of the admin user
DELETE FROM pokemon_inst WHERE iid = 1;
