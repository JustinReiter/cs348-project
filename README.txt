cs348-project

###########################################################################

How to create and load sample database to GCP:

Commands from Google Cloud CLI connected to project

Copy app.yaml and env.php from Config_Templates (provided in this code.zip)
and fill in with relevant parameters

Connect to gcloud DB instance (sample command below)
gcloud sql connect <DB_INSTANCE> --user=<USERNAME>

Create database and table schema
Run SQL scripts in CreateTableSQLQueries (provided in this code.zip)
The data sources for the SQL scripts are given in the 
SQL files in CreateTableSQLQueries where applicable

Clone project master branch
git clone https://github.com/JustinReiter/cs348-project.git

Run in project directory
composer install

cd ~
$ wget https://dl.google.com/cloudsql/cloud_sql_proxy.linux.amd64 -O cloud_sql_proxy
$ chmod +x cloud_sql_proxy
./cloud_sql_proxy -instances=<PROJECT_ID>:<LOCALE>:<DN_INSTANCE>

Run in project directory (use new terminal instance)
php -S localhost:8080

###########################################################################

Implemented features:

i)    Implemented a battle system to use your party to play PvP against another player in real time
      Client page implemented in battle.php with battleServer.php handling game logic and updates

i)    Login/Create Account to Persist Player Data Across Multiple Sessions
      Primarily implemented in index.php
      
ii)   Pokemon Search Page (Search by Type, Name, and Pokedex Number)
      Implemented in pokemon.php

iii)  Favourite/Unfavourite Pokemon feature for quick access
      Implemented in pokemon.php and favouritePokemon.php

iv)   A page to generate/add specific Pokemon instances to your team with randomized stats
      Implemented in catchPokemon.php

v)    The ability to populate and assign an order to your party (team) of Pokemon
      Implemented in organizePokemon.php

vi)   User information page with display of favourited Pokemon
      Implemented in profile.php

vii)  Detailed individual Pokemon stats with images
      Implemented in viewPokemonPage.php

###########################################################################

Link to deployed version:

(updated) https://cs348-project-279406.uk.r.appspot.com/
(old) https://cs348demo-279318.uc.r.appspot.com/
