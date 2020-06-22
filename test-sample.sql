-- Find pokemon that are of type fire and print the pokedex number, name, types and generation
SELECT pid, name, type1, type2, gen FROM pokemon WHERE type1="fire" OR type2="fire";

-- Find players that contain the name admin
SELECT * FROM players WHERE name="admin";
