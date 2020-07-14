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
