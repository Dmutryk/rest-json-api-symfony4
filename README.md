To generate keys run:
```
openssl genrsa -out rsa_256.pem 2048
openssl rsa -in rsa_256.pem -pubout > rsa_256.pub
```

Don't forget to run migrations
```
php bin/console doctrine:migrations:migrate
```

To create a user you can use CLI. This commands will return you a password of created user.:
```
bin/console app:create-user --username="user" --email="user@mail.com" --roles="ROLE_USER"
bin/console app:create-user --username="admin" --email="admin@mail.com" --roles="ROLE_USER" --roles="ROLE_ADMIN"
```

For testing work you can use curl commands

To get token run with password from precious command:
```
curl -i -X POST {your-server-address}/getToken -H 'Content-Type: application/x-www-form-urlencoded' -d "username=...&password=..."
```

To get protected data run this command with token which you get on previous step:
```
curl -i -X GET {your-server-address}/api/v1/secret-route
-H 'Authorization: Bearer insert-your-token-here...........'
```