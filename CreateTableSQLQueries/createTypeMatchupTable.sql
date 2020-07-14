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
