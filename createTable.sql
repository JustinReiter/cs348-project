CREATE TABLE pokemon
(
    attack integer NOT NULL,
    base_egg_steps integer NOT NULL,
    base_happiness integer NOT NULL,
    base_total integer NOT NULL,
    capture_rate integer NOT NULL,
    classification VARCHAR(64),
    defense integer NOT NULL,
    exp integer NOT NULL,
    height real,
    hp integer NOT NULL,
    name VARCHAR(16) NOT NULL,
    percent_male  real NOT NULL,
    pid integer NOT NULL PRIMARY KEY,
    sp_atk integer NOT NULL,
    sp_def integer NOT NULL,
    speed integer NOT NULL,
    type1 VARCHAR(8) NOT NULL,
    type2 VARCHAR(8),
    weight real,
    gen integer NOT NULL,
    is_legendary tinyint NOT NULL
);
