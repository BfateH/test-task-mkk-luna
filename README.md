## Тестовое задание для МКК Луна

**Для запуска проекта**
- composer install
- Настроить .env
- php artisan key:generate
- php artisan migrate --seed
- php artisan l5-swagger:generate
- docker-compose up -d

**Для доступа к API указать в заголовках X-API-Key или передавать его как queryParam**
- Сам ключ оставил в .env.example
- Продублирую и тут **13888eddf7dccb60096fdaa810db836364e6b3b5b0c7c376f45665cd0a50e938**
- Ключ можно поменять при желании :)

**Дополнительно**
- При тестировании через Postman или другую программу добавить заголовок Accept application/json
- Swagger можно найти по /api/documentation
