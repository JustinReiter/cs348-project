-- Create indices to improve performance

-- Increases performance when doing search by name in the Pokemon Searcher
-- The index needs to be manually created as name is neither a primary key nor
-- foreign key of Pokemon so a index is not automatically created
-- for it by the database
CREATE INDEX pokemon_name ON pokemon(name);

-- Increases performance when looking up player name (since it is the first
-- attribute of the index) or name and pin to support log-in authentication
-- queries.
-- The index needs to be manually created as neither attribute is a
-- primary key nor foreign key of player
-- so a index is not automatically created for it by the database
CREATE INDEX player_details ON player(name, pin);
