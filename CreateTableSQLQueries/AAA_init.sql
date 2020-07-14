CREATE DATABASE pokemon;
USE pokemon;

-- Creates table of types
CREATE TABLE type
(
    tid VARCHAR(8) NOT NULL PRIMARY KEY
);

-- Creates table storing how effective each type is against another type
CREATE TABLE type_matchup
(
    attacking_type VARCHAR(8) NOT NULL,
    defending_type VARCHAR(8) NOT NULL,
    damage_factor real NOT NULL,
  	PRIMARY KEY(attacking_type, defending_type),
  	FOREIGN KEY(attacking_type) REFERENCES type(tid),
  	FOREIGN KEY(defending_type) REFERENCES type(tid)
);

-- Based on https://rankedboost.com/pokemon-sun-moon/type-chart/

-- Creates pokemon table to store the general information of every pokemon
CREATE TABLE pokemon
(
    base_attack integer NOT NULL,
    base_egg_steps integer NOT NULL,
    base_happiness integer NOT NULL,
    base_capture_rate integer NOT NULL,
    classification VARCHAR(64),
    base_defense integer NOT NULL,
    base_exp_given integer NOT NULL,
    height real,
    base_hp integer NOT NULL,
    name VARCHAR(16) NOT NULL,
    percent_male real,
    pid integer NOT NULL PRIMARY KEY,
    base_sp_atk integer NOT NULL,
    base_sp_def integer NOT NULL,
    base_speed integer NOT NULL,
    type1 VARCHAR(8) NOT NULL FOREIGN KEY REFERENCES type(tid),
    type2 VARCHAR(8) FOREIGN KEY REFERENCES type(tid),
    weight real,
    gen integer NOT NULL,
    is_legendary tinyint(1) NOT NULL
);

-- To import the pokemon afterwards, there are cli tools or the gui to upload the clean csv file into the table

-- Creates table of all possible pokemon moves
CREATE TABLE move
(
    move_name varchar(16) NOT NULL NOT NULL PRIMARY KEY,
    type VARCHAR(8) NOT NULL FOREIGN KEY REFERENCES type(tid),
    base_power integer NOT NULL,
    accuracy real NOT NULL,
    pp integer NOT NULL
);

-- Creates many to many relationship table storing
-- what pokemon can learn what moves
CREATE TABLE learnable_move
(
  pid integer NOT NULL,
  move_name varchar(16) NOT NULL,
  PRIMARY KEY(pid, move_name),
  FOREIGN KEY(pid) REFERENCES pokemon(pid),
  FOREIGN KEY(move_name) REFERENCES move(move_name)
);


-- Creates new table for the player accounts
CREATE TABLE player
(
    uid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    pin VARCHAR(4) NOT NULL,
    joined_at DATETIME NOT NULL
);
-- Inserts a default account into the table
INSERT INTO player (uid, name, pin, joined_at) VALUES (0, "admin", "0000", now());

CREATE TABLE favourite_pokemon
(
	uid integer NOT NULL,
	pid integer NOT NULL,
	PRIMARY KEY(pid, uid),
	FOREIGN KEY(uid) REFERENCES players(uid),
	FOREIGN KEY(pid) REFERENCES pokemon(pid)
);

-- Creates pokemon instance table to store the
-- specific randomized stats based on base stats and customizations
-- given to a pokemon belonging to a specific player
CREATE TABLE pokemon_inst
(
    iid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cur_hp integer NOT NULL,
    max_hp integer NOT NULL,
    attack integer NOT NULL,
    defense integer NOT NULL,
    sp_atk integer NOT NULL,
    sp_def integer NOT NULL,
    speed integer NOT NULL,
    happiness integer NOT NULL,
    exp integer NOT NULL,
    nickname VARCHAR(16) NOT NULL,
    gender VARCHAR(8) NOT NULL,
    move_1 varchar(16)  NOT NULL FOREIGN KEY REFERENCES move(move_name),
    move_2 varchar(16) FOREIGN KEY REFERENCES move(move_name),
    move_3 varchar(16) FOREIGN KEY REFERENCES move(move_name),
    move_4 varchar(16) FOREIGN KEY REFERENCES move(move_name),
    pid integer NOT NULL FOREIGN KEY REFERENCES pokemon(pid),
    uid integer NOT NULL FOREIGN KEY REFERENCES player(uid)
);

-- Creates table to store the one to many relationship of
-- player to pokemon in their party
CREATE TABLE party
(
  uid integer NOT NULL,
  iid integer NOT NULL,
  party_order integer NOT NULL,
  PRIMARY KEY(uid, iid),
  FOREIGN KEY(uid) REFERENCES player(uid),
  FOREIGN KEY(iid) REFERENCES pokemon_inst(iid)
);
