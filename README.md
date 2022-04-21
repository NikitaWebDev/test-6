# Задание 1

Реализовать Rest API эндпоинт для проведения транзакции начисления условных единиц (денег или баллов) от одного пользователя на счет другого. Баланс не может быть отрицательным. В БД проекта должно быть как минимум две обязательных таблицы: пользователи и транзакции.

В рамках этой задачи эндпоинт должен быть доступен без авторизации.

Написать интеграционные тесты, покрывающие как минимум два любых тесткейса (например успешную транзакцию и неуспешную).

Подумать, какие подводные камни могут возникнуть при использовании этого эндпоинта в реальном проекте.

Код должен быть оформлен по стандарту PSR-12. Использовать Php 7.4, Последнюю минорную версию Symfony 5, любую удобную реляционную СУБД. Для тестов использовать PhpUnit. Должна быть возможность развернуть проект локально и запустить тесты.

## Локальный деплой проекта

1. Если не установлен Docker Compose, то [установите его](https://docs.docker.com/compose/install/).
2. `git clone git@github.com:NikitaWebDev/test-6.git`
3. `cp docker-compose.override.yml.example docker-compose.override.yml`
4. `cp .env.example .env`
5. `docker-compose build --pull --no-cache`
6. `docker-compose up -d`
7. `docker-compose exec php sh`
8. `php bin/console doctrine:fixtures:load --append`
9. Чтобы остановить, выполните `docker-compose down --remove-orphans`.

## Запуск тестов
1. `docker-compose exec php sh`
2. `php bin/console --env=test doctrine:database:create`
3. `php bin/console --env=test doctrine:schema:create`
4. `php bin/console --env=test doctrine:fixtures:load --append`
5. `php bin/phpunit`
