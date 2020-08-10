CREATE TABLE favourite_pokemon
(
	uid integer NOT NULL,
	pid integer NOT NULL,
	PRIMARY KEY(pid, uid),
	FOREIGN KEY(uid) REFERENCES player(uid),
	FOREIGN KEY(pid) REFERENCES pokemon(pid)
);
