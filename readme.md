# PHP Laravel Exercise

## Evaluation Points

* Basic programming concepts
* PHP, MySQL and Laravel knowledge 
* Data modeling

## Overall Problem
The CSV file located on `csv/superheros.csv` contains superheros that is not filtrable.

## Overall Requirement
We need an API based on laravel that allow us to retrieve superheroes.

## Exercise
We will divide the exercise in 3 parts:

1. **Data modeling:**
Define a correct structure to import the `CSV` and manage the database efficiently based on eloquent.

2. **Data import:**
Create a script to import `csv/superheros.csv` in the models database tables. No MySQL have to be provided, the script need to allow us to import the data correctly.

3. **API to retrieve data:**
Generate a API endpoint to retrieve superheroes. The endpoint need to be able to filter by GET params (three properties at least), order and paginate the results.

## Setup and Config

### Requirements
1. PHP
2. MySQL database
3. Composer


### Installation guide

1. Clone the repo:
`git clone git@bitbucket.org:consultr/laravel.git`
2. Once you cloned the repository, you will need to create a MySQL database
3. Setup the `exercise/.env` file
4. Execute the command: `sh scripts/install.sh`
