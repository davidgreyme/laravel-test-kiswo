
## Simple Laravel Testing

### Install App
- download app from git repo
```commandline
git clone https://github.com/DreH-World/simple-laravel-testing.git
cd simple-laravel-testing
```

- install vendor
```commandline
composer install
```
- install node_modules
```commandline
npm -i && npm run dev
```

- make .env file and .env.testing
```command line
cp .env.example .env
cp .env.exmaple .env.testing
```

you can set the database information.
```
DB_CONNECTION=pgsql
DB_HOST=$host_name
DB_PORT=5432
DB_DATABASE=$your_database_name
DB_USERNAME=$username
DB_PASSWORD=$password
```
create another database for testing env.
```
DB_CONNECTION=pgsql
DB_HOST=$host_name
DB_PORT=5432
DB_DATABASE=$your_test_database_name
DB_USERNAME=$username
DB_PASSWORD=$password
```
migrate
```commandline
php artisan migrate
```

### Test App
```commandline
php artisan test
```
