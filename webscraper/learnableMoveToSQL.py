#from bs4 import BeautifulSoup
import bs4
import pandas as pd
import numpy as np
import requests
import csv

results = []
with open('move_learn_clean.csv') as csvfile:
  reader = csv.reader(csvfile)
  for row in reader: # each row is a list
    results.append(row)

with open('initLearnableMoveTable.sql', 'a') as f:
  for move in results:
    f.write("INSERT INTO learnable_move(pid, move_name) VALUES (" + move[1] + ", \"" + move[0] + "\");\n")
