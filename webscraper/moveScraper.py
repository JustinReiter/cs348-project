#from bs4 import BeautifulSoup
import bs4
import pandas as pd
import numpy as np
import requests

r = requests.get('https://bulbapedia.bulbagarden.net/wiki/List_of_moves')

soup = bs4.BeautifulSoup(r.text, 'html.parser')
rows = soup.find_all('tr')

arr = []

for i in range(2, 820):
    move = rows[i]
    b = []
    for i in move.find_all('td'):
        if len(i) == 1:
            b.append(i.contents[0][:-1])
        elif isinstance(i.contents[0], bs4.element.NavigableString):
            b.append(i.contents[0])
        else:
            b.append(i.contents[0].contents[0].string)

    arr.append(b)
x = np.array(arr)
df = pd.DataFrame(x, columns=['index','name', 'type', 'category', 'style', 'pp', 'power', 'accuracy', 'generation'])
with open('moves.csv', 'w') as f:
    f.write(df.to_csv())
print(df)

