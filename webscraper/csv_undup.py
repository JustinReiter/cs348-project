from more_itertools import unique_everseen
with open('move_learn.csv','r') as f, open('move_learn_clean.csv','w') as out_file:
    out_file.writelines(unique_everseen(f))