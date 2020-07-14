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
