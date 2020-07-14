-- Creates new table for the player accounts
CREATE TABLE player
(
    uid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    pin VARCHAR(4) NOT NULL,
    joined_at DATETIME NOT NULL
);
-- Inserts a default account into the table
INSERT INTO player (uid, name, pin, joined_at) VALUES (0, "admin", "0000", now());
