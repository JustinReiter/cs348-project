-- TO-DO: Should reference pokemon_inst iid instead of pokemon pid
-- for pokemon1 and pokemon2 and mark pokemon_inst with bool flag
-- for when they are released instead of deleting them
-- from the table to maintain foreign key constraint
-- May be better to reference a party instead of party_alive1
-- and party_alive2 strings to ensure data validity/consistency
CREATE TABLE battle
(
    gid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    uid1 integer NOT NULL,
    uid2 integer,
    started_at DATETIME NOT NULL,
    pokemon1 integer NOT NULL,
    pokemon2 integer,
    party_alive1 VARCHAR(256) NOT NULL,
    party_alive2 VARCHAR(256),
    is_finished BOOL NOT NULL,
    FOREIGN KEY(uid1) REFERENCES player(uid),
    FOREIGN KEY(uid2) REFERENCES player(uid),
    FOREIGN KEY(pokemon1) REFERENCES pokemon(pid),
    FOREIGN KEY(pokemon2) REFERENCES pokemon(pid)
);
