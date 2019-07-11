To generate keys:
openssl genrsa -out id_rsa_jwt.pem 2048
openssl rsa -in id_rsa_jwt.pem -pubout > id_rsa_jwt.pub

Don't forget to run migrations
To create a user you can use CLI: 
$ bin/console app:create-user --username="basic" --email="basic@domain.com" --roles="ROLE_USER"
$ bin/console app:create-user --username="admin" --email="admin@domain.com" --roles="ROLE_USER" --roles="ROLE_ADMIN"