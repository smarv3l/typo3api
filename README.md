[![Build Status](https://travis-ci.org/Nemo64/typo3api.svg?branch=master)](https://travis-ci.org/Nemo64/typo3api)

# apis for easier typo3 handling

# testing

Checkout this repo and install the composer dependencies using `composer update`.
I don't ship a `composer.lock` since this library must run with the newest dependencies.
If you don't have composer locally use `docker-compose run --rm --entrypoint=composer php update`. 

## run the unit tests

There are classical unit tests included.

run `docker-compose run --rm php vendor/bin/phpunit tests`

## run the shipped typo3 instance

Since many features can't easily be tested without a running typo3 instance.
To test the interface use the included typo3 instance.

run `docker-compose up` and then access `localhost:8080`.
All passwords are `password` and the default user is `admin`. 

### updating the shipped mysql dump

If you changed something in the database which you think is important to ship for other testers:
simply run the following command while the database is running
`docker-compose exec db bash -c "mysqldump -uroot -ppassword database > /docker-entrypoint-initdb.d/database.sql"`
