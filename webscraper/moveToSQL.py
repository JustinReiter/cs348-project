# Assume header row is removed from moves.csv already

#from bs4 import BeautifulSoup
import bs4
import pandas as pd
import numpy as np
import requests
import csv

results = []
with open('moves.csv') as csvfile:
  reader = csv.reader(csvfile)
  for row in reader: # each row is a list
    results.append(row)

with open('initMoveTable.sql', 'a') as f:
  for move in results:
    f.write("INSERT INTO move(move_name, type, category, style, base_power, accuracy, pp) VALUES (\"" + move[1]+ "\", \'" + move[2].lower() + "\', \'" + move[3] + "\', \'" + move[4] + "\', " + move[6] + ", " + move[7] + ", " + move[5] + ");\n")
