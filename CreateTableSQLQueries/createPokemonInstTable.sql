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
