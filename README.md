Symfony Assignment
==============
PHP Version: **PHP 7.0.0**



Step 1

Clone the project:
`git clone git@github.com:spmsupun/assessment-99x.git`

Step 2

Run `composer install`

Step 3

Config database(sample db details): `app/config/parameters.yml.dist`

Step 4 

Create Database : `php bin/console doctrine:database:create`

Step 5

Create Schema: `php bin/console doctrine:schema:update --force`

Step 6

Feed the database `php bin/console doctrine:fixtures:load`

Step 7

Run the application: `php bin/console server:start`

**Test**

`./vendor/bin/simple-phpunit`


**Note:**
* Coupon stored in coupon table.
* "10% discount for children" stored in category table.
* Recommended environment is linux(Ubuntu)
* Since there are no any user registration, cart's books store using a unique id. when user close the browser, cart item will be lose.
