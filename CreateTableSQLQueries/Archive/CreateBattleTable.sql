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
