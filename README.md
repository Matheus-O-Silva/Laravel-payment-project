
### Passo a passo

Clone Repositório

```sh

git clone https://github.com/Matheus-O-Silva/Laravel-payment-project.git
```

```sh

cd  Laravel-payment-project

```

  
  

Crie o Arquivo .env

```sh

cp .env.example  .env

```

  
  

Atualize as variáveis de ambiente do arquivo .env

```dosini

APP_NAME=Laravel

APP_ENV=local

APP_KEY=

APP_DEBUG=true

APP_URL=http://localhost

  

LOG_CHANNEL=stack

LOG_DEPRECATIONS_CHANNEL=null

LOG_LEVEL=debug

  

DB_CONNECTION=mysql

DB_HOST=db

DB_PORT=3306

DB_DATABASE=laravel

DB_USERNAME=root

DB_PASSWORD=root

  

CACHE_DRIVER=file

QUEUE_CONNECTION=redis

SESSION_DRIVER=file

FILESYSTEM_DISK=local

  

MEMCACHED_HOST=127.0.0.1

  

REDIS_HOST=redis

REDIS_PASSWORD=null

REDIS_PORT=6379

  

MAIL_MAILER=smtp

MAIL_HOST=sandbox.smtp.mailtrap.io

MAIL_PORT=2525

MAIL_USERNAME=7f1323710f0dc2

MAIL_PASSWORD=4853d5553007c8

MAIL_ENCRYPTION=tls

  

```

  

Suba os containers do projeto

```sh

docker-compose up  -d

```

  
  

Acesse o container app

```sh

docker-compose exec  app  bash

```

  
  

Instale as dependências do projeto

```sh

composer install

```

  
  

Gere a key do projeto Laravel

```sh

php artisan  key:generate

```

Execute o comando artisan para gerar a estrutura do banco de dados

```sh

php artisan  migrate

```

Execute o comando artisan para popular o banco com os usuários de teste

```sh

php artisan  db:seed

```

  
  


O Laravel utilizará a rota:

[http://localhost:8989](http://localhost:8989)
