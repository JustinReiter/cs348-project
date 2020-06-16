CREATE TABLE players
(
    uid integer NOT NULL PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    pin VARCHAR(4) NOT NULL,
    joined_at DATETIME
);
