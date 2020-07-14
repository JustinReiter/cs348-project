-- Creates many to many relationship table storing
-- what pokemon can learn what moves
CREATE TABLE learnable_move
(
  pid integer NOT NULL,
  move_name varchar(16) NOT NULL,
  PRIMARY KEY(pid, move_name),
  FOREIGN KEY(pid) REFERENCES pokemon(pid),
  FOREIGN KEY(move_name) REFERENCES move(move_name)
);
