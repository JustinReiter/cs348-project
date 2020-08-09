#from bs4 import BeautifulSoup
import bs4
import pandas as pd
import numpy as np
import requests
import csv

results = []
with open('move_names.csv') as csvfile:
  reader = csv.reader(csvfile)
  for row in reader: # each row is a list
    results.append(row)

for move in results:
  id = move[0]
  moveName = move[1]

  r = requests.get("https://www.serebii.net/attackdex-sm/" + moveName + ".shtml")

  soup = bs4.BeautifulSoup(r.text, 'html.parser')
  rows = soup.find_all('td', class_='fooinfo')

  if not rows:
    arr = []
    b = []
    b.append(id)
    b.append(moveName)
    arr.append(b)
    x = np.array(arr)
    df = pd.DataFrame(x, columns=['mid', 'move_name'])
    with open('move_learn_no_such_page.csv', 'a') as f:
      f.write(df.to_csv())
    continue

  arr = []

  for row in rows:
    str_row = str(row)
    if str_row.find('#') != -1:
      b = []
      b.append(id)
      b.append(moveName)
      poundIndex = str_row.find('#')
      b.append(str_row[poundIndex + 1 : poundIndex + 4])
      arr.append(b)
      print(b)

  if not arr:
    b = []
    b.append(id)
    b.append(moveName)
    arr.append(b)
    x = np.array(arr)
    df = pd.DataFrame(x, columns=['mid', 'move_name'])
    with open('move_learn_none_can_learn.csv', 'a') as f:
      f.write(df.to_csv())
    continue

  x = np.array(arr)
  df = pd.DataFrame(x, columns=['mid', 'move_name', 'pid'])
  with open('move_learn.csv', 'a') as f:
    f.write(df.to_csv())
