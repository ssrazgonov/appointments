# RecordToMaster - SaaS CRM для мастеров красоты

SaaS CRM система с подпиской для малого бизнеса: мастера красоты, ремонтные специалисты, репетиторы, частные мастера.

## Стек технологий

### Backend
- PHP 8.3
- Laravel 11
- MySQL 8.0
- Redis
- JWT Authentication

### Frontend
- Vue 3
- Vite
- Pinia (state management)
- Vue Router
- Axios
- Tailwind CSS

### Инфраструктура
- Docker
- Docker Compose
- Nginx

## Быстрый старт

### 1. Запуск проекта

```bash
docker compose up -d
```

### 2. Установка зависимостей (если нужно)

```bash
# Backend зависимости
docker compose run --rm php composer install

# Frontend зависимости
docker compose run --rm php npm install

# Сборка frontend
docker compose run --rm php npm run build
```

### 3. Миграции и сидеры

```bash
# Запуск миграций
docker compose run --rm php php artisan migrate:fresh --seed

# Сидеры (если нужно)
docker compose run --rm php php artisan db:seed
```

## Доступ к сервисам

- **Frontend**: http://localhost:8080
- **API**: http://localhost:8080/api
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## Тестовые аккаунты

```
Email: test@example.com
Пароль: password123

Email: demo@example.com
Пароль: demo1234
```

## API Endpoints

### Аутентификация
- `POST /api/auth/register` - Регистрация
- `POST /api/auth/login` - Вход
- `POST /api/auth/logout` - Выход
- `POST /api/auth/refresh` - Обновление токена
- `GET /api/auth/me` - Получить текущего пользователя

### Клиенты
- `GET /api/clients` - Список клиентов
- `GET /api/clients/{id}` - Детали клиента
- `POST /api/clients` - Создать клиента
- `PUT /api/clients/{id}` - Обновить клиента
- `DELETE /api/clients/{id}` - Удалить клиента

### Записи
- `GET /api/appointments` - Список записей
- `GET /api/appointments/today` - Записи на сегодня
- `GET /api/appointments/upcoming` - Предстоящие записи
- `POST /api/appointments` - Создать запись
- `PUT /api/appointments/{id}` - Обновить запись
- `DELETE /api/appointments/{id}` - Удалить запись
- `POST /api/appointments/{id}/cancel` - Отменить запись
- `POST /api/appointments/{id}/complete` - Завершить запись

### Подписки
- `GET /api/subscriptions/plans` - Список тарифов
- `GET /api/subscriptions/current` - Текущая подписка
- `GET /api/subscriptions/has-active` - Есть ли активная подписка

### Платежи
- `GET /api/payments` - История платежей
- `POST /api/payments/create` - Создать платеж
- `GET /api/payments/{id}` - Детали платежа

### Отчеты
- `GET /api/dashboard` - Dashboard статистика
- `GET /api/reports/monthly` - Месячный отчет
- `GET /api/reports/yearly` - Годовой отчет

### Robokassa Webhooks
- `POST /api/payments/robokassa/result` - Result URL
- `POST /api/payments/robokassa/success` - Success URL
- `POST /api/payments/robokassa/fail` - Fail URL

## Тарифные планы

### Starter (990₽/мес)
- До 50 клиентов
- До 10 записей в день
- Базовые отчеты
- Email поддержка

### Professional (1990₽/мес)
- До 200 клиентов
- До 30 записей в день
- Расширенные отчеты
- SMS напоминания
- Приоритетная поддержка

### Business (3990₽/мес)
- Безлимитные клиенты
- До 100 записей в день
- Полная аналитика
- SMS & Email напоминания
- API доступ
- 24/7 поддержка

## Переменные окружения

### База данных
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=rtm_db
DB_USERNAME=rtm_user
DB_PASSWORD=rtm_password
```

### Redis
```
REDIS_HOST=redis
REDIS_PORT=6379
```

### Robokassa
```
ROBOKASSA_MERCHANT_ID=ваш_merchant_id
ROBOKASSA_SECRET_KEY_1=ваш_secret_key_1
ROBOKASSA_SECRET_KEY_2=ваш_secret_key_2
ROBOKASSA_TEST_MODE=1
```

### JWT
```
JWT_SECRET=ваш_secret_key
JWT_TTL=10080
```

## Очереди и задачи

Queue worker запускается автоматически как отдельный сервис.

### Отправка уведомлений

```bash
# Запуск уведомлений вручную
docker compose run --rm php php artisan notifications:send-scheduled
```

### Планировщик задач

Планировщик настроен на запуск каждую минуту для:
- Проверки истекающих подписок
- Отправки напоминаний о записях

## Логирование

Логи находятся в `storage/logs/`:
- `laravel.log` - основной лог
- `php_errors.log` - ошибки PHP

## Структура проекта

```
/docker
  /nginx
  /php
/src
  /app
    /Console/Commands
    /Http
      /Controllers/Api
      /Middleware
      /Requests
      /Resources
    /Jobs
    /Models
    /Notifications
    /Repositories
      /Contracts
      /Eloquent
    /Services
  /database
    /migrations
    /seeders
  /resources
    /js
      /api
      /components
      /layouts
      /router
      /stores
      /views
    /views
  /routes
docker-compose.yml
```

## Разработка

### Запуск в режиме разработки

```bash
# Frontend hot reload
docker compose run --rm php npm run dev
```

### Тесты

```bash
docker compose run --rm php php artisan test
```

### Clear cache

```bash
docker compose run --rm php php artisan optimize:clear
```

## Лицензия

MIT
