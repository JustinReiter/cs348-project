-- Creates table of all possible pokemon moves
CREATE TABLE move
(
    move_name varchar(16) NOT NULL NOT NULL PRIMARY KEY,
    type VARCHAR(8) NOT NULL FOREIGN KEY REFERENCES type(tid),
    base_power integer NOT NULL,
    accuracy real NOT NULL,
    pp integer NOT NULL
);
