## Requirements:
- Docker v4.35.1

## Installation:
- clone this repo: https://github.com/Kythadrin/symfony.git
- Start docker container by following commands:
```
docker-compose build
docker-compose up
```
- inside docker "php" container run following commands(You can use this command for it ```docker-compose exec php bash```):
```
composer install && npm install
npm run build
bin/doctrine.php orm:schema-tool:update --force
```

Once all these steps have been completed, the application will be available at: http://127.0.0.1:8080/