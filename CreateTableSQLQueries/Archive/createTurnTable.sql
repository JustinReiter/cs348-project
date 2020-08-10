CREATE TABLE turn
(
    turn_number integer AUTO_INCREMENT NOT NULL,
    gid integer NOT NULL,
    uid integer NOT NULL,
    move_at DATETIME NOT NULL,
    pid integer NOT NULL,
    move_name varchar(16) NOT NULL,
    PRIMARY KEY(turn_number, gid),
    FOREIGN KEY(gid) REFERENCES battle(gid),
    FOREIGN KEY(uid) REFERENCES player(uid),
    FOREIGN KEY(pid) REFERENCES pokemon(pid),
    FOREIGN KEY(move_name) REFERENCES move(move_name)
);
