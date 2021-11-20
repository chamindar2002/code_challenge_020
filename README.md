# code_challenge_020
assement

Local Installation Steps

1. Clone the project

2. Make sure .env file exists (.env file removed from .gitignore so it should be available)

3. Create a mysql empty database

4. Change the database connection variables in the .env file

5. Run "composer install"

6. Run 
    php artisan migrate

7. Run
    php artisan db:seed

8. Run
    php artisan passport:install

9. Run 
    php artisan queue:work

10. Run
    php -S localhost:8000 -t public

11. For Running the tests execute following command from the root directory
    ./vendor/bin/phpunit tests/PostTest.php

Seeder will populate the user table with a user with following credentials which can be used to login
username: admin@gmail.com
password: admin123



