SELECT p.pid, p.name, i.max_hp, i.attack, i.defense, i.sp_atk, i.sp_def, i.speed,
i.move_1, i.move_2, i.move_3, i.move_4, i.party_iid, i.nickname, i.party_order
  FROM (
    SELECT max_hp, attack, defense, sp_atk, sp_def, speed, pid,
    move_1, move_2, move_3, move_4, party_order, party.iid AS party_iid, nickname
    FROM party INNER JOIN pokemon_inst ON party.iid = pokemon_inst.iid WHERE party.uid = 2
  ) AS i,
  pokemon AS p
  WHERE i.pid = p.pid ORDER BY i.party_order;
