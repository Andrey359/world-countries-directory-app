# API Документация - World Countries Directory

## Основная информация

Это полная документация REST API для управления справочником стран мира. Все ответы возвращаются в формате JSON.

## Базовый URL

```
http://localhost:8080/api
```

## Статус кодов

- **200 OK** - Успешный запрос
- **204 No Content** - Успешно создано/обновлено/удалено
- **400 Bad Request** - Ошибка валидации или невалидный формат
- **404 Not Found** - Ресурс не найден
- **409 Conflict** - Конфликт (дублирование)
- **500 Internal Server Error** - Ошибка сервера

## Endpoints

### 1. Статус сервера

#### GET /
Получить информацию о статусе сервера

**Ответ: 200 OK**
```json
{
  "status": "server is running",
  "host": "localhost:8080",
  "protocol": "http"
}
```

#### GET /ping
Проверить доступность сервера

**Ответ: 200 OK**
```json
{
  "status": "pong"
}
```

### 2. Страны

#### GET /country
Получить список всех стран

**Параметры запроса:** Нет

**Ответ: 200 OK**
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
  {
    "shortName": "USA",
    "fullName": "United States of America",
    "isoAlpha2": "US",
    "isoAlpha3": "USA",
    "isoNumeric": "840",
    "population": 331900000,
    "square": 9833520.0
  }
]
```

#### GET /country/{code}
Получить информацию о конкретной стране

**Параметры пути:**
- `code` (required) - Код страны (Alpha-2, Alpha-3 или Numeric)
  - Alpha-2: 2 буквы (RU, US, FR)
  - Alpha-3: 3 буквы (RUS, USA, FRA)
  - Numeric: 3 цифры (643, 840, 250)

**Примеры:**
```
GET /country/RU
GET /country/RUS
GET /country/643
```

**Ответ: 200 OK**
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

**Ошибки:**

400 Bad Request - Невалидный формат кода
```json
{
  "error": "Invalid country code format"
}
```

404 Not Found - Страна не найдена
```json
{
  "error": "Country not found"
}
```

#### POST /country
Создать новую страну

**Заголовки:**
```
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "shortName": "Germany",
  "fullName": "Federal Republic of Germany",
  "isoAlpha2": "DE",
  "isoAlpha3": "DEU",
  "isoNumeric": "276",
  "population": 83500000,
  "square": 357022.0
}
```

**Обязательные поля:**
- shortName (строка, не пустая, уникальная)
- fullName (строка, не пустая, уникальная)
- isoAlpha2 (2 буквы, уникальный)
- isoAlpha3 (3 буквы, уникальный)
- isoNumeric (3 цифры, уникальный)
- population (целое число, >= 0)
- square (число, >= 0)

**Ответ: 204 No Content**

**Ошибки:**

400 Bad Request - Ошибка валидации
```json
{
  "error": "Validation error: Invalid ISO Alpha-2 code format (must be 2 letters)"
}
```

409 Conflict - Дублирование
```json
{
  "error": "Conflict: Country with ISO Alpha-2 code 'DE' already exists"
}
```

#### PATCH /country/{code}
Обновить информацию о стране

**Параметры пути:**
- `code` (required) - Код страны (Alpha-2, Alpha-3 или Numeric)

**Заголовки:**
```
Content-Type: application/json
```

**Тело запроса:**
```json
{
  "shortName": "Germany",
  "fullName": "Federal Republic of Germany",
  "population": 84000000,
  "square": 357022.0
}
```

**Примечания:**
- Коды (isoAlpha2, isoAlpha3, isoNumeric) не могут быть изменены
- Если коды переданы в теле запроса, они проверяются на соответствие существующим
- Остальные поля подчиняются тем же правилам валидации, что и при создании

**Ответ: 200 OK**
```json
{
  "shortName": "Germany",
  "fullName": "Federal Republic of Germany",
  "isoAlpha2": "DE",
  "isoAlpha3": "DEU",
  "isoNumeric": "276",
  "population": 84000000,
  "square": 357022.0
}
```

**Ошибки:**

400 Bad Request - Невалидный код или данные
```json
{
  "error": "Invalid country code format"
}
```

404 Not Found - Страна не найдена
```json
{
  "error": "Country not found"
}
```

409 Conflict - Конфликт данных
```json
{
  "error": "Conflict: Country with short name 'Russia' already exists"
}
```

#### DELETE /country/{code}
Удалить страну

**Параметры пути:**
- `code` (required) - Код страны (Alpha-2, Alpha-3 или Numeric)

**Ответ: 204 No Content**

**Ошибки:**

400 Bad Request - Невалидный код
```json
{
  "error": "Invalid country code format"
}
```

404 Not Found - Страна не найдена
```json
{
  "error": "Country not found"
}
```

## Примеры использования

### Получить все страны
```bash
curl -X GET http://localhost:8080/api/country
```

### Получить страну
```bash
curl -X GET http://localhost:8080/api/country/RU
```

### Создать страну
```bash
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName": "Japan",
    "fullName": "Japan",
    "isoAlpha2": "JP",
    "isoAlpha3": "JPN",
    "isoNumeric": "392",
    "population": 125800000,
    "square": 377975.0
  }'
```

### Обновить страну
```bash
curl -X PATCH http://localhost:8080/api/country/JP \
  -H "Content-Type: application/json" \
  -d '{
    "population": 126000000
  }'
```

### Удалить страну
```bash
curl -X DELETE http://localhost:8080/api/country/JP
```

## Правила валидации

### ISO коды
- Alpha-2: Ровно 2 латинские буквы (a-z, A-Z)
- Alpha-3: Ровно 3 латинские буквы (a-z, A-Z)
- Numeric: Ровно 3 цифры (0-9)
- Все коды должны быть уникальными в базе

### Названия
- Не могут быть пустыми или содержать только пробелы
- Должны быть уникальными в базе
- Максимальная длина: 100 символов (short name), 200 символов (full name)
- Поддерживают кириллицу и другие Unicode символы

### Числовые значения
- Population: целое число, >= 0
- Square: число с плавающей точкой, >= 0

## Обработка ошибок

Все ошибки возвращаются в формате:
```json
{
  "error": "Описание ошибки"
}
```

Статус код определяет тип ошибки:
- **400** - Ошибка на стороне клиента (валидация, формат)
- **404** - Ресурс не найден
- **409** - Конфликт (дублирование)
- **500** - Ошибка сервера
