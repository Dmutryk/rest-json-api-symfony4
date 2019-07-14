For using JWT authorization you have to generate keys in project folder with names:`rsa_256.pem` and `rsa_256.pub`. To generate keys you could run this commands:
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
``````

You could refresh your tokens by sending refresh_token to `/refresh-tokens` URL
```
curl -i -X POST {your-server-address}/refresh-tokens -H 'Content-Type: application/x-www-form-urlencoded' -d "refresh_token=..."
```

Also if you want to logout from all devices - you could disable all available tokens by sending refresh_token to `/disable-tokens` URL
```
curl -i -X POST {your-server-address}/disable-tokens -H 'Content-Type: application/x-www-form-urlencoded' -d "refresh_token=..."
```

With every login - all previous tokens will be erased