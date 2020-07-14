CREATE TABLE favourite_pokemon
(
	uid integer NOT NULL,
	pid integer NOT NULL,
	PRIMARY KEY(pid, uid),
	FOREIGN KEY(uid) REFERENCES players(uid),
	FOREIGN KEY(pid) REFERENCES pokemon(pid)
);

