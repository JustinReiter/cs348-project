-- Update player account with new pin (where $pin and $uid are replaced with new player pin and their uid)
UPDATE players
SET pin=$pin
WHERE players.uid = $uid;

-- Update player account with new name (where $name and $uid are replaced with new player name and their uid)
Update players
SET name=$name
WHERE players.uid = $uid
