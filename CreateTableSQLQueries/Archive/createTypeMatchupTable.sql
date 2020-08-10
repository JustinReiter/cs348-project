-- Creates table storing how effective each type is against another type
CREATE TABLE type_matchup
(
    attacking_type VARCHAR(8) NOT NULL,
    defending_type VARCHAR(8) NOT NULL,
    damage_factor real NOT NULL,
  	PRIMARY KEY(attacking_type, defending_type),
  	FOREIGN KEY(attacking_type) REFERENCES type(tid),
  	FOREIGN KEY(defending_type) REFERENCES type(tid)
);

-- Based on https://rankedboost.com/pokemon-sun-moon/type-chart/
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','rock',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','ghost',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('normal','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','normal',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','flying',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','poison',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','rock',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','bug',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','ghost',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','steel',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','psychic',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','ice',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','dark',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fighting','fairy',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','fighting',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','rock',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','bug',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','grass',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','electric',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('flying','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','poison',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','ground',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','rock',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','ghost',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','steel',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','grass',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('poison','fairy',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','flying',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','poison',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','rock',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','bug',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','steel',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','fire',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','grass',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','electric',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ground','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','fighting',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','flying',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','ground',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','bug',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','fire',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','ice',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('rock','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','fighting',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','flying',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','poison',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','ghost',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','fire',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','grass',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','psychic',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','dark',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('bug','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','normal',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','fighting',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','ghost',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','steel',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','psychic',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','dark',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ghost','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','rock',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','fire',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','water',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','electric',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','ice',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('steel','fairy',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','rock',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','bug',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','steel',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','fire',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','water',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','grass',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','ice',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','dragon',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fire','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','ground',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','rock',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','steel',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','fire',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','water',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','grass',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','dragon',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('water','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','flying',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','poison',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','ground',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','rock',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','bug',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','fire',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','water',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','grass',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','dragon',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('grass','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','flying',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','ground',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','steel',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','water',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','grass',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','electric',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','dragon',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('electric','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','fighting',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','poison',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','psychic',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','dark',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('psychic','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','flying',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','ground',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','fire',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','water',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','grass',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','ice',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','dragon',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('ice','fairy',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','fighting',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','dragon',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','dark',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dragon','fairy',0);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','fighting',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','poison',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','ghost',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','steel',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','fire',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','psychic',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','dragon',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','dark',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('dark','fairy',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','normal',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','fighting',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','flying',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','poison',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','ground',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','rock',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','bug',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','ghost',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','steel',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','fire',0.5);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','water',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','grass',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','electric',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','psychic',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','ice',1);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','dragon',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','dark',2);
INSERT INTO type_matchup(attacking_type,defending_type,damage_factor) VALUES ('fairy','fairy',1);