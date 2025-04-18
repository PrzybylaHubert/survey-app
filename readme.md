# Survey backend

This is backend, which let users create surveys, and complete surveys create by others. It is my small show off project.

I focused on using Symfony's potential and performance

## Local run

### requirements:
- Git
- Docker

### step by step
- Clone the repository `git clone https://github.com/PrzybylaHubert/survey-app.git ./survey-app`
- Run `docker compose up -d`
- Run `docker ps` to list containers, then select id of symfony_app container and run `docker exec -it {container_id} /bin/bash`
- Run `make init` or run all commands in the [Makefile](/Makefile)
- Done!

## Endpoints

All endpoints are listed in [Postman documentation](https://www.postman.com/hewbertpshybylla/workspace/public-workspace/collection/23727786-58c95a62-8bcf-479f-b9fd-f6964a542236?action=share&creator=23727786)

## Technologies used

- PHP 8.2
- Symfony 7.2
- Docker engine 28.0
- MariaDB 11.0
- RabbitMQ 3.13
- Elasticsearch 8.12
- Visual Studio Code
- Git, Github
- WSL
- GPT-4o mini
- Postman