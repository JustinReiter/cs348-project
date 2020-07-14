USE pokemon;

DELETE FROM party;

DELETE FROM pokemon_inst;

DELETE FROM favourite_pokemon;

DELETE FROM player;
INSERT INTO player (uid, name, pin, joined_at) VALUES (0, "admin", "0000", now());
