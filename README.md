# Horoshop
## Install
1. Run docker compose
```shell
docker compose up -d
```
2. Go to container api and install vendors

```shell
composer install
```
3. Go to container api and execute migrations

```shell
php bin/console doctrine:migrations:migrate
```
4. Go to container api and execute fixture

```shell
php bin/console doctrine:fixtures:load
```
## API

### Login as admin

```shell
curl --location 'http://localhost/v1/api/login' \
--header 'Content-Type: application/json' \
--header 'Cookie: XDEBUG_SESSION=PHPSTORM' \
--data '{
    "username": "admin",
    "password": "admin"
}'
```

### Login as user

```shell
curl --location 'http://localhost/v1/api/login' \
--header 'Content-Type: application/json' \
--data '{
    "username": "user",
    "password": "user"
}'
```

### Add new user
```shell
curl --location --request PUT 'http://localhost/v1/api/users' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <your token ...>' \
--data '{
    "login": "user2",
    "pass": "user3",
    "phone":"12345678"
}'
```

### Update user
```shell
curl --location 'http://localhost/v1/api/users' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <your token ...>' \
--data '{
    "id": 6,
    "login": "user2",
    "pass": "user2",
    "phone": "12345678"
}'
```

### Get user
```shell
curl --location 'http://localhost/v1/api/users/6' \
--header 'Authorization: Bearer <your token ...>'
```

### DELETE user
```shell
curl --location --request DELETE 'http://localhost/v1/api/users/3' \
--header 'Authorization: Bearer <your token ...>'
```