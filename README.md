Сделано на самописе с использованием [ORM](https://github.com/mlfordev/shorter_api/tree/main/components) фреймворка https://github.com/phact-cmf/phact

Мой самописный код - полностью папка [/app](https://github.com/mlfordev/shorter_api/tree/main/app)

# Веб-приложение для сокращения ссылок

Приложение предоставляет веб-интерфейс и REST API, поддерживающий различный функционал для различных ролей (их всего 2 - анонимный пользователь и администратор).

Веб-интерфейс позволяет:

- Создать короткую ссылку (форма с полем ввода).
- Перейти по этой ссылке.

Интерфейс общий для всех ролей, админки нет.

REST API позволяет пользователю:

- Создать короткую ссылку
- Получить полный адрес короткой ссылки

Администратор дополнительно может (тоже через REST API):

- Получить список всех созданных ссылок с возможностью фильтрации.
- Получить данные конкретной ссылки
- Изменить конкретную ссылку (адрес, на который указывает сокращенный код).
- Удалить ссылку
- Получить список всех переходов по ссылкам с возможностью фильтрации.

Способ аутентификации - Base.

## Установка проекта

PHP 7.3, MySQL 5.7

`git clone git@github.com:mlfordev/shorter_api.git`

`composer install` в папке проекта

Файл `.env.example` переименовать в `.env` и вписать в него настройки базы данных

В базе данных создать таблицы из файла `/sql/tables.sql`

`DOCUMENT_ROOT` веб-сервера настраивается на папку `public_html`

Запуск проекта на встроенном php-сервере `php -S localhost:8880 -t public_html/`

## Описание методов API

### Методы для работы со ссылками

- `GET` `/short-links`, статус ответа `200` - возвращает список сохранённых ссылок, или пустой список. Метод доступен администраторам. Дополнительные параметры:

    `url` - длинная ссылка. Возвращает все данные по длинной ссылке
    
    `page` - номер страницы. Ссылки приходят списком по 10 штук. Информация о пагинации приходит в заголовке ответа `Link`

    Примеры запросов: 
  
    `curl -u admin:111111 -X GET http://localhost:8880/short-links` - возвращает первую страницу списка ссылок, или пустой список
  
    `curl -u admin:111111 -X GET -d "url=https://yandex.ru" -G http://localhost:8880/short-links` - возвращает информацию по значению длинной ссылки, или пустой список

    `curl -u admin:111111 -X GET -d "page=2" -G http://localhost:8880/short-links` - возвращает вторую страницу списка ссылок, или пустой список


- `GET` `/short-links/{id}`, статус ответа `200` - Возвращает информацию по ссылке по её `id`. Если ресурс не найден, возвращает статус `404`. Метод доступен администраторам.

    Пример запроса: `curl -u admin:111111 -X GET http://localhost:8880/short-links/1`
  
    
- `POST` `/short-links`, статус ответа `201` - создаёт короткую ссылку, возвращает её `id`. Метод доступен гостям и администраторам.

    Принимает параметр `url` со значением длинной ссылки. В случае неудачи возвращает статусы `422` или `500`
  
    Пример запроса: `curl -X POST -d "url=https://gist.github.com/bobdobbalina/47330cc70248315e1bdccfb430dce36a" http://localhost:8880/short-links`
  

- `PATCH` `/short-links/{id}`, статус ответа `200` - обновляет значение полной ссылки по её `id`. Метод доступен администраторам.

    Возвращает всё информацию по ссылке. В случае неудачи возвращает статусы `404`, `422`, `500`.
  
    Принимает параметр: `url` - новое значение длинной ссылки.
  
    Пример запроса: `curl -u admin:111111 -X PATCH -d "url=https://gist.github.com" http://localhost:8880/short-links/27`
  

- `DELETE` `/short-links/{id}`, статус ответа `204` - Удаляет ссылку по её `id`. Метод доступен администраторам.

    Пример запроса: `curl -u admin:111111 -X DELETE http://localhost:8880/short-links/27`
  
### Методы для работы с аккаунтами администраторов

Методы доступны только администраторам.

- `GET` `/admins`, статус ответа `200` - возвращает список администраторов. Доступны параметры `login`, `page`.

    Примеры запросов: 
  
    `curl -u admin:111111 -X GET http://localhost:8880/admins`
  
    `curl -u admin:111111 -X GET -d "login=admin" -G http://localhost:8880/admins`
  
    `curl -u admin:111111 -X GET -d "page=2" -G http://localhost:8880/admins`


- `GET` `/admins/{id}`, статус ответа `200` - возвращает данные администратора по его `id`.

    Пример запроса: `curl -u admin:111111 -X GET http://localhost:8880/admins/1`


- `POST` `/admins`, статус ответа `201` - создаёт администратора. Возвращает его `id`. В случае неудачи возвращает статусы `422` или `500`

    Принимает параметры `login`, `password`. 

    Примеры запроса: `curl -u admin:111111 -X POST -d "login=admin3" -d "password=333333" http://localhost:8880/admins`


- `PATCH` `/admins/{id}`, статус ответа `200` - обновляет пароль администратора. Возвращает данные администратора, кроме пароля. Администратор может изменить только свой пароль. В случае неудачи возвращает статусы `404`, `422`, `500`

    Принимает параметр `password`.

    Пример запроса: `curl -u admin:111111 -X PATCH -d "password=333333" http://localhost:8880/admins/1`


- `DELETE` `/admins/{id}`, статус ответа `204` - удаляет администратора по его `id`. Администратор может удалить только свою учётную запись.

    Пример запроса: `curl -u admin3:333333 -X DELETE http://localhost:8880/admins/3`


### Метод для работы с переходами по ссылкам

Метод доступен только администраторам.

- `GET` `/clicks`, статус ответа `200` - возвращает список переходов по коротким ссылкам или пустой список. Доступны параметры `short_code`, `page`.

  Примеры запросов:

  `curl -u admin:111111 -X GET http://localhost:8880/clicks`

  `curl -u admin:111111 -X GET -d "short_code=q" -G http://localhost:8880/clicks`

  `curl -u admin:111111 -X GET -d "page=2" -G http://localhost:8880/clicks`


