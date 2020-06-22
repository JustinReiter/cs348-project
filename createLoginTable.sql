-- Creates new table for the player accounts
CREATE TABLE players
(
    uid integer NOT NULL PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    pin VARCHAR(4) NOT NULL,
    joined_at DATETIME
);
-- Inserts a default account into the table
INSERT INTO players (uid, name, pin, joined_at) VALUES ((SELECT max(uid)+1 FROM players), "admin", "0000", now());
