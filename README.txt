cs348-project

###########################################################################

How to create and load sample database to GCP:

# Commands from Google Cloud CLI connected to project 
# (initialized as per "cs348_s20_project.pdf" with DB Instance cs348demo-db)

# Connect to gcloud DB instance (sample command below)
# Sample uses password = password
gcloud sql connect cs348demo-db --user=root

# Create database and table schema
CREATE DATABASE pokemon;
USE pokemon;
CREATE TABLE pokedex (
	attack INT,
	base_egg_steps INT,
	base_happiness INT,
	base_total INT,
	capture_rate INT,
	classification VARCHAR(255),
	defense INT,
	experience_growth INT,
	height_metres FLOAT,
	hp INT,
	name VARCHAR(255),
	percentage_male FLOAT,
	pokedex_number INT NOT NULL,
	sp_attack INT,
	sp_defense INT,
	speed INT,
	type1 VARCHAR(255),
	type2 VARCHAR(255),
	weight_kg FLOAT,
	generation INT,
	is_legendary BOOLEAN,
	PRIMARY KEY(pokedex_number)
);

# Make bucket to store csv to load (choose unique bucket name)
gsutil mb -c standard -l us-east4 gs://cs348_pokemon_demo_bucket

# Upload csv to bucket (run from same directory as csv to upload)
gsutil cp pokemon_clean.csv gs://cs348_pokemon_demo_bucket

# Get service account to give bucket permissions to
# Execute
gcloud sql instances describe cs348demo-db
# Read the line with the serviceAccountEmailAddress (example given below)
serviceAccountEmailAddress: p1024996630659-ynfou6@gcp-sa-cloud-sql.iam.gserviceaccount.com

# Give service account bucket write permission
gsutil acl ch -u p1024996630659-ynfou6@gcp-sa-cloud-sql.iam.gserviceaccount.com:W gs://cs348_pokemon_demo_bucket

# Give service account read permission for relevant csv in bucket
gsutil acl ch -u p1024996630659-ynfou6@gcp-sa-cloud-sql.iam.gserviceaccount.com:R gs://cs348_pokemon_demo_bucket/pokemon_clean.csv

# Import csv to database
gcloud sql import csv cs348demo-db gs://cs348_pokemon_demo_bucket/pokemon_clean.csv --database=pokemon --table=pokedex

# Test Locally

# Run in project directory
composer install

cd ~
$ wget https://dl.google.com/cloudsql/cloud_sql_proxy.linux.amd64 -O cloud_sql_proxy
$ chmod +x cloud_sql_proxy
./cloud_sql_proxy -instances=cs348demo-279320:us-eastt4:cs348demo-db=tcp:3306

# Run in project directory (use new terminal instance)
php -S localhost:8080

