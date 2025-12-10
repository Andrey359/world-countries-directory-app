# Справочник стран мира - REST API

Полнофункциональное REST API приложение для управления справочником стран мира с операциями CRUD, построенное на Symfony 7.0 и MySQL 8.0.

## Быстрый обзор

Этот проект реализует полностью функциональное API для хранения и управления информацией о странах со следующими возможностями:

- Полные операции CRUD (Создание, Чтение, Обновление, Удаление)
- Поддержка нескольких форматов кодов (Alpha-2, Alpha-3, Числовой ISO коды)
- Полная валидация данных
- Поддержка кириллицы (русского языка)
- Docker контейнеризация
- MySQL база данных с UTF-8 поддержкой
- Чистая архитектура с внедрением зависимостей
- Комплексная обработка ошибок

## API Эндпоинты

```
GET    /api                    -> Статус сервера
GET    /api/ping               -> Проверка пинга
GET    /api/country            -> Получить все страны
GET    /api/country/{code}     -> Получить страну по коду (Alpha-2, Alpha-3 или Числовой)
POST   /api/country            -> Создать новую страну
PATCH  /api/country/{code}     -> Обновить страну
DELETE /api/country/{code}     -> Удалить страну
```

## Быстрый старт

### Требования
- Docker Desktop
- Docker Compose
- Git

### Установка

```bash
# Клонировать репозиторий
git clone https://github.com/Andrey359/world-countries-directory-app.git
cd world-countries-directory-app

# Запустить Docker контейнеры
docker-compose up -d

# Установить зависимости
docker exec -it symfony_php composer install
```

### Тестирование API

```bash
# Проверить статус сервера
curl http://localhost:8080/api

# Получить все страны
curl http://localhost:8080/api/country

# Получить страну по коду
curl http://localhost:8080/api/country/RU

# Создать новую страну
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{"shortName":"Japan","fullName":"Japan","isoAlpha2":"JP","isoAlpha3":"JPN","isoNumeric":"392","population":125800000,"square":377975}'

# Обновить страну
curl -X PATCH http://localhost:8080/api/country/JP \
  -H "Content-Type: application/json" \
  -d '{"population":126000000}'

# Удалить страну
curl -X DELETE http://localhost:8080/api/country/JP
```

См. [QUICK_START.md](QUICK_START.md) для детального руководства по тестированию.

## Структура проекта

```
world-countries-directory-app/
|
+-- app/                          Корневая папка Symfony приложения
|   +-- src/
|   |   +-- Controller/            HTTP контроллеры
|   |   |   +-- StatusController.php
|   |   |   +-- CountryController.php
|   |   |
|   |   +-- Model/                 Доменные модели и бизнес-логика
|   |   |   +-- Country.php                (Модель данных)
|   |   |   +-- CountryScenarios.php      (Бизнес-логика)
|   |   |   +-- CountryRepository.php     (Интерфейс репозитория)
|   |   |   +-- StranaM.php               (Алиас)
|   |   |   +-- Exceptions/               (Пользовательские исключения)
|   |   |
|   |   +-- Rdb/                   Слой доступа к данным
|   |   |   +-- SqlHelper.php             (Управление подключением)
|   |   |   +-- CountryStorage.php        (Операции с БД)
|   |   |
|   |   +-- Kernel.php             Symfony ядро
|   |
|   +-- config/                    Конфигурация
|   |   +-- services.yaml          (Внедрение зависимостей)
|   |   +-- bundles.php
|   |   +-- packages/
|   |
|   +-- public/
|   |   +-- index.php              (Точка входа)
|   |
|   +-- bin/
|   |   +-- console               (Инструмент CLI)
|   |
|   +-- composer.json              Зависимости Composer
|   +-- .env                       Переменные окружения
|   +-- .env.example               Пример переменных
|   +-- .gitignore
|
+-- mysql/
|   +-- init/                      Инициализация БД
|   |   +-- 01-create-schema.sql   (Схема таблицы)
|   |   +-- 02-seed-data.sql       (Тестовые данные - 10 стран)
|
+-- nginx/
|   +-- default.conf               Конфигурация Nginx
|
+-- docs/                          Документация
|   +-- API_DOCUMENTATION.md       (Полная спецификация API)
|   +-- class_diagram.txt          (Архитектура)
|   +-- screenshots/               (Скриншоты тестов)
|
+-- docker-compose.yml             Конфигурация Docker Compose
+-- Dockerfile                     Определение PHP контейнера
+-- README.md                      (Этот файл)
+-- QUICK_START.md                 (Руководство по установке и тестированию)
+-- FINAL_ANALYSIS_REPORT.md        (Полный анализ)
+-- CYRILLIC_CLEANUP_REPORT.md     (Проверка файлов)
+-- VERIFICATION_CHECKLIST.md      (Чек-лист тестирования)
+-- TECHNICAL_REQUIREMENTS.md      (Соответствие требованиям)
+-- ENCODING_VERIFICATION.md       (Поддержка UTF-8)
+-- IMPLEMENTATION_STATUS.md       (Статус реализации)
```

## Модель данных

### Сущность Country

```json
{
  "shortName": "Россия",
  "fullName": "Российская Федерация",
  "isoAlpha2": "RU",
  "isoAlpha3": "RUS",
  "isoNumeric": "643",
  "population": 146150789,
  "square": 17125191.0
}
```

### Правила валидации

**ISO Коды:**
- Alpha-2: Ровно 2 латинских буквы (A-Z), без учёта регистра
- Alpha-3: Ровно 3 латинских буквы (A-Z), без учёта регистра  
- Числовой: Ровно 3 цифры (0-9)
- Все коды должны быть уникальными

**Имена:**
- Краткое имя: 1-100 символов, не пусто, уникально
- Полное имя: 1-200 символов, не пусто, уникально
- Полная поддержка кириллицы (русский язык)

**Числовые поля:**
- Население: Неотрицательное целое число
- Площадь: Неотрицательное десятичное число
- Оба обязательны

## Поддержка кириллицы

API полностью поддерживает кириллицу (русский текст и другие скрипты):

```bash
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName": "Болгария",
    "fullName": "Республика Болгария",
    "isoAlpha2": "BG",
    "isoAlpha3": "BGR",
    "isoNumeric": "100",
    "population": 6856000,
    "square": 110910
  }'
```

## База данных

### Параметры подключения
- Хост: mysql (внутри Docker)
- Порт: 3306
- База данных: world_countries_db
- Пользователь: symfony_user
- Пароль: symfony_password (изменяется в .env)

### Схема таблицы

```sql
CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_name VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(200) UNIQUE NOT NULL,
    iso_alpha2 CHAR(2) UNIQUE NOT NULL,
    iso_alpha3 CHAR(3) UNIQUE NOT NULL,
    iso_numeric CHAR(3) UNIQUE NOT NULL,
    population BIGINT UNSIGNED NOT NULL DEFAULT 0,
    square DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Архитектура

### Слои приложения

1. **Слой представления**: HTTP контроллеры
   - StatusController: эндпоинты статуса и пинга
   - CountryController: эндпоинты CRUD

2. **Слой бизнес-логики**: Сценарии
   - CountryScenarios: основная логика, валидация, определение типа кода

3. **Слой доступа к данным**: Паттерн репозитория
   - CountryRepository: интерфейс
   - CountryStorage: реализация MySQL

4. **Слой БД**:
   - SqlHelper: управление подключением
   - MySQL: хранение данных

5. **Доменный слой**:
   - Country: модель данных/DTO
   - Exceptions: пользовательские исключения

### Внедрение зависимостей

Все зависимости настроены в `config/services.yaml` и автоматически внедряются:

```
CountryController -> CountryScenarios -> CountryRepository -> CountryStorage -> SqlHelper
```

## Обработка ошибок

### HTTP коды статуса

- **200 OK**: Успешный GET или PATCH
- **204 No Content**: Успешный POST или DELETE
- **400 Bad Request**: Ошибка валидации или невалидный формат кода
- **404 Not Found**: Страна не найдена
- **409 Conflict**: Конфликт при дублировании (код или имя)
- **500 Internal Server Error**: Ошибка сервера

### Иерархия исключений

```
Exception
+-- CountryNotFoundException (404)
+-- InvalidCountryCodeException (400)
+-- ValidationException (400)
+-- DuplicateCountryException (409)
```

## Технологический стек

- **Язык**: PHP 8.2+
- **Фреймворк**: Symfony 7.0
- **База данных**: MySQL 8.0
- **Web сервер**: Nginx
- **Контейнеризация**: Docker & Docker Compose
- **Менеджер пакетов**: Composer
- **Кодировка символов**: UTF-8 (utf8mb4)

## Инструкции по установке

### 1. Клонировать репозиторий
```bash
git clone https://github.com/Andrey359/world-countries-directory-app.git
cd world-countries-directory-app
```

### 2. Запустить Docker
```bash
docker-compose up -d
```

### 3. Установить зависимости
```bash
docker exec -it symfony_php composer install
```

### 4. Проверить установку
```bash
curl http://localhost:8080/api
# Должен вернуть: {"status":"server is running","host":"localhost","protocol":"http"}
```

## Развёртывание

### Остановить контейнеры
```bash
docker-compose down
```

### Просмотреть логи
```bash
docker logs symfony_php
docker logs symfony_nginx
docker logs symfony_mysql
```

### Подключиться к БД
```bash
docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db
```

## Соглашения об именовании файлов

- Все имена файлов используют английские (латинские) символы
- Windows 10 совместимые пути
- UTF-8 кодировка везде
- Полная поддержка кириллицы в БД и ответах API

См. [CYRILLIC_CLEANUP_REPORT.md](CYRILLIC_CLEANUP_REPORT.md) для деталей.

## Реализация требований

Все 14 требований из технического задания полностью реализованы:

1. Структура проекта (Symfony 7.0)
2. StatusController с эндпоинтами /api и /api/ping
3. Модель Country со всеми требуемыми полями
4. Полные операции CRUD (GET, POST, PATCH, DELETE)
5. Определение типа кода (Alpha-2, Alpha-3, Числовой)
6. Комплексная валидация
7. Обработка пользовательских исключений
8. Схема БД с правильными индексами
9. Паттерн CountryRepository
10. Бизнес-логика CountryScenarios
11. Docker контейнеризация
12. MySQL с поддержкой UTF-8
13. Полная документация API
14. Комплексное тестирование и проверка

См. [IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md) для подробного чек-листа.

## Поддержка

При возникновении проблем или вопросов:
1. Проверьте [QUICK_START.md](QUICK_START.md) для решения общих проблем
2. Просмотрите логи Docker: `docker logs symfony_php`
3. Проверьте подключение к БД: `docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db -e 'SHOW TABLES;'`
4. Просмотрите документацию API: [docs/API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)

## Статус проекта

Статус: ЗАВЕРШЕНО И ПРОВЕРЕНО

Все требования выполнены, все операции CRUD работают, все валидации на месте, вся документация завершена.

Готово к развёртыванию в production.
