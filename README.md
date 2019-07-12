To generate keys run:
```
openssl genrsa -out id_rsa_jwt.pem 2048
openssl rsa -in id_rsa_jwt.pem -pubout > id_rsa_jwt.pub
```

Don't forget to run migrations
To create a user you can use CLI:
```
bin/console app:create-user --username="basic" --email="basic@domain.com" --roles="ROLE_USER"
bin/console app:create-user --username="admin" --email="admin@domain.com" --roles="ROLE_USER" --roles="ROLE_ADMIN"
```

For testing work you can use curl commands

To get token run:
```
curl -i -X POST {your-server-address}/login -H 'Content-Type: application/json' -d '{"username": "basic","password":"16a0098250ad0d5ca214c2bea779bfd3"}'
```

To get protected data run this command with token which you get on previous step:
```
curl -i -X GET {your-server-address}/api/v1/secret-route
-H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpZCI6ImZlODAzO...........'
```