-- Creates table of all possible pokemon moves
CREATE TABLE move
(
    move_name varchar(16) NOT NULL PRIMARY KEY,
    type VARCHAR(8) NOT NULL,
    base_power integer NOT NULL,
    accuracy real NOT NULL,
    pp integer NOT NULL,
    FOREIGN KEY(type) REFERENCES type(tid)
);

INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('normal_move','normal',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('fighting_move','fighting',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('flying_move','flying',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('poison_move','poison',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('ground_move','ground',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('rock_move','rock',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('bug_move','bug',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('ghost_move','ghost',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('steel_move','steel',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('fire_move','fire',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('water_move','water',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('grass_move','grass',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('electric_move','electric',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('psychic_move','psychic',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('ice_move','ice',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('dragon_move','dragon',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('dark_move','dark',100,95,99);
INSERT INTO move(move_name,type,base_power,accuracy,pp) VALUES ('fairy_move','fairy',100,95,99);
