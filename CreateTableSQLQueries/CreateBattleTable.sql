CREATE TABLE battle
(
    gid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
    uid1 integer NOT NULL,
    uid2 integer NOT NULL,
    started_at DATETIME NOT NULL,
    pokemon1 integer NOT NULL,
    pokemon2 integer NOT NULL,
    party_alive1 VARCHAR(256) NOT NULL,
    party_alive2 VARCHAR(256) NOT NULL,
    is_finished BOOL NOT NULL,
    FOREIGN KEY(uid1) REFERENCES player(uid),
    FOREIGN KEY(uid2) REFERENCES player(uid),
    FOREIGN KEY(pokemon1) REFERENCES pokemon(pid),
    FOREIGN KEY(pokemon2) REFERENCES pokemon(pid)
);
