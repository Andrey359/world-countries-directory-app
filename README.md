# World Countries Directory API

**Лабораторная работа: Справочник стран мира**

Веб-приложение на PHP и Symfony для управления справочником стран мира с использованием REST API и MySQL базы данных.

## Описание проекта

Это полнофункциональное Web API приложение, реализующее CRUD-операции для справочника стран мира согласно заданию по веб-программированию.

## Стек технологий

- **PHP 8.2+** - Язык программирования
- **Symfony 7.0** - Web фреймворк
- **MySQL 8.0** - Релационная база данных
- **Docker & Docker Compose** - Контейнеризация
- **Nginx** - Web-сервер
- **mysqli** - Расширение для работы с MySQL
- **VS Code** - IDE

## Структура проекта

```
world-countries-directory-app/
├── app/                           # Symfony приложение
│   ├── src/
│   │   ├── Controller/             # Контроллеры
│   │   │   ├── StatusController.php
│   │   │   └── CountryController.php
│   │   ├── Model/                  # Бизнес-логика
│   │   │   ├── Country.php
│   │   │   ├── CountryScenarios.php
│   │   │   ├── CountryRepository.php
│   │   │   └── Exceptions/
│   │   │       ├── CountryNotFoundException.php
│   │   │       ├── InvalidCountryCodeException.php
│   │   │       ├── ValidationException.php
│   │   │       └── DuplicateCountryException.php
│   │   ├── Rdb/                    # Доступ к данным
│   │   │   ├── SqlHelper.php
│   │   │   └── CountryStorage.php
│   │   └── Kernel.php
│   ├── config/
│   │   ├── bundles.php
│   │   ├── packages/
│   │   └── services.yaml
│   ├── public/
│   │   └── index.php
│   ├── bin/
│   │   └── console
│   ├── .env
│   ├── .gitignore
│   ├── composer.json
│   └── composer.lock
├── mysql/
│   └── init/                       # Скрипты инициализации БД
│       ├── 01-create-schema.sql
│       └── 02-seed-data.sql
├── nginx/
│   └── default.conf                # Конфигурация Nginx
├── docs/                           # Документация
│   ├── screenshots/                # Скриншоты Postman
│   ├── class_diagram.png           # Диаграмма классов
│   └── API_DOCUMENTATION.md        # API документация
├── .env
├── docker-compose.yml
├── Dockerfile
└── README.md
```

## Запуск проекта

### С использованием Docker

1. **Клонируйте репозиторий:**
   ```bash
   git clone https://github.com/Andrey359/world-countries-directory-app.git
   cd world-countries-directory-app
   ```

2. **Скопируйте .env файл:**
   ```bash
   cp app/.env.example app/.env
   ```

3. **Запустите Docker контейнеры:**
   ```bash
   docker-compose up -d
   ```

4. **Установите зависимости Composer:**
   ```bash
   docker exec symfony_php composer install
   ```

5. **Проверьте доступность приложения:**
   ```
   http://localhost:8080
   ```

### Без Docker

1. **Установите PHP 8.2+ и MySQL 8.0**

2. **Клонируйте репозиторий:**
   ```bash
   git clone https://github.com/Andrey359/world-countries-directory-app.git
   cd world-countries-directory-app/app
   ```

3. **Установите зависимости:**
   ```bash
   composer install
   ```

4. **Создайте базу данных и запустите скрипты:**
   ```bash
   mysql -u root -p < ../mysql/init/01-create-schema.sql
   mysql -u root -p < ../mysql/init/02-seed-data.sql
   ```

5. **Запустите Symfony сервер:**
   ```bash
   php bin/console server:run
   ```

## API Endpoints

### Статус сервера

**GET /api**
```json
{
  "status": "server is running",
  "host": "localhost:8080",
  "protocol": "http"
}
```

**GET /api/ping**
```json
{
  "status": "pong"
}
```

### Операции со странами

#### Получить все страны
**GET /api/country**

Ответ: 200 OK
```json
[
  {
    "shortName": "Russia",
    "fullName": "Russian Federation",
    "isoAlpha2": "RU",
    "isoAlpha3": "RUS",
    "isoNumeric": "643",
    "population": 146150789,
    "square": 17125191.0
  },
  ...
]
```

#### Получить страну по коду
**GET /api/country/{code}**

Параметр `code` может быть:
- Двухбуквенный код (e.g., RU, US, FR)
- Трехбуквенный код (e.g., RUS, USA, FRA)
- Числовой код (e.g., 643, 840, 250)

Ответ: 200 OK
```json
{
  "shortName": "Russia",
  "fullName": "Russian Federation",
  "isoAlpha2": "RU",
  "isoAlpha3": "RUS",
  "isoNumeric": "643",
  "population": 146150789,
  "square": 17125191.0
}
```

Ошибки:
- **400** - Невалидный формат кода
- **404** - Страна не найдена

#### Создать новую страну
**POST /api/country**

Тело запроса:
```json
{
  "shortName": "France",
  "fullName": "French Republic",
  "isoAlpha2": "FR",
  "isoAlpha3": "FRA",
  "isoNumeric": "250",
  "population": 67413000,
  "square": 643801.0
}
```

Ответ: 204 No Content

Ошибки:
- **400** - Ошибка валидации
- **409** - Конфликт (код или имя уже существуют)

#### Обновить страну
**PATCH /api/country/{code}**

Тело запроса (коды не изменяются):
```json
{
  "shortName": "France",
  "fullName": "French Republic",
  "population": 67500000,
  "square": 643801.0
}
```

Ответ: 200 OK
```json
{
  "shortName": "France",
  "fullName": "French Republic",
  "isoAlpha2": "FR",
  "isoAlpha3": "FRA",
  "isoNumeric": "250",
  "population": 67500000,
  "square": 643801.0
}
```

Ошибки:
- **400** - Невалидный код или данные
- **404** - Страна не найдена
- **409** - Конфликт с существующими данными

#### Удалить страну
**DELETE /api/country/{code}**

Ответ: 204 No Content

Ошибки:
- **400** - Невалидный код
- **404** - Страна не найдена

## Архитектура приложения

### Слои приложения

1. **Presentation Layer (Контроллеры)**
   - `StatusController.php` - Статус сервера
   - `CountryController.php` - CRUD операции со странами
   - Обработка HTTP запросов и формирование ответов

2. **Business Logic Layer (Сценарии)**
   - `CountryScenarios.php` - Бизнес-логика операций
   - Валидация данных
   - Проверка уникальности
   - Определение типа кода (Alpha-2, Alpha-3, Numeric)

3. **Data Access Layer (Хранилище)**
   - `CountryRepository.php` - Интерфейс для работы с данными
   - `CountryStorage.php` - Реализация через mysqli
   - `SqlHelper.php` - Управление соединениями с БД

4. **Domain Model (Модель)**
   - `Country.php` - Сущность страны
   - `Exceptions/` - Кастомные исключения

### Обработка исключений

- **CountryNotFoundException** - Страна не найдена (404)
- **InvalidCountryCodeException** - Невалидный код (400)
- **ValidationException** - Ошибка валидации (400)
- **DuplicateCountryException** - Конфликт при дублировании (409)

## Валидация данных

### Коды
- ISO Alpha-2: ровно 2 латинские буквы (e.g., RU, US)
- ISO Alpha-3: ровно 3 латинские буквы (e.g., RUS, USA)
- ISO Numeric: ровно 3 цифры (e.g., 643, 840)

### Имена
- Короткое имя: не может быть пустым, должно быть уникальным
- Полное имя: не может быть пустым, должно быть уникальным

### Численные данные
- Население: неотрицательное целое число
- Площадь: неотрицательное действительное число

## База данных

### Таблица countries

```sql
CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    short_name VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(200) NOT NULL UNIQUE,
    iso_alpha2 CHAR(2) NOT NULL UNIQUE,
    iso_alpha3 CHAR(3) NOT NULL UNIQUE,
    iso_numeric CHAR(3) NOT NULL UNIQUE,
    population BIGINT UNSIGNED NOT NULL DEFAULT 0,
    square DECIMAL(15, 2) UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_iso_alpha2 (iso_alpha2),
    INDEX idx_iso_alpha3 (iso_alpha3),
    INDEX idx_iso_numeric (iso_numeric)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Тестирование API

Для тестирования используется **Postman**. Скриншоты всех тестов находятся в папке `docs/screenshots/`.

### Примеры запросов

**Получить все страны:**
```bash
curl http://localhost:8080/api/country
```

**Получить страну по коду:**
```bash
curl http://localhost:8080/api/country/RU
```

**Создать новую страну:**
```bash
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName": "Germany",
    "fullName": "Federal Republic of Germany",
    "isoAlpha2": "DE",
    "isoAlpha3": "DEU",
    "isoNumeric": "276",
    "population": 83500000,
    "square": 357022.0
  }'
```

**Обновить страну:**
```bash
curl -X PATCH http://localhost:8080/api/country/RU \
  -H "Content-Type: application/json" \
  -d '{
    "shortName": "Russia",
    "fullName": "Russian Federation",
    "population": 147000000,
    "square": 17125191.0
  }'
```

**Удалить страну:**
```bash
curl -X DELETE http://localhost:8080/api/country/DE
```

## Документация

- `docs/API_DOCUMENTATION.md` - Полная документация API
- `docs/class_diagram.png` - Диаграмма классов проекта
- `docs/screenshots/` - Скриншоты тестирования в Postman

## Автор

Автор: Andrey359
Дата создания: Декабрь 2025

## Лицензия

Этот проект создан в образовательных целях.
