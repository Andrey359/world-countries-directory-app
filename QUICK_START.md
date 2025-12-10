# Quick Start Guide - World Countries Directory API

## Prerequisites

- Docker Desktop installed
- Docker Compose installed
- Git installed
- curl or Postman for API testing

## Installation & Running

### Step 1: Clone repository

```bash
git clone https://github.com/Andrey359/world-countries-directory-app.git
cd world-countries-directory-app
```

### Step 2: Start Docker containers

```bash
docker-compose up -d
```

This starts:
- PHP 8.2-FPM (port 9000)
- Nginx (port 8080)
- MySQL 8.0 (port 3306)

### Step 3: Install dependencies

```bash
docker exec -it symfony_php composer install
```

### Step 4: Verify containers are running

```bash
docker ps
```

You should see 3 containers:
- symfony_php
- symfony_nginx
- symfony_mysql

## Testing the API

### 1. Test Status Endpoints

```bash
# Check server status
curl http://localhost:8080/api

# Should return:
# {"status":"server is running","host":"localhost","protocol":"http"}

# Ping test
curl http://localhost:8080/api/ping

# Should return:
# {"status":"pong"}
```

### 2. Test GET All Countries

```bash
curl http://localhost:8080/api/country

# Returns array of all countries sorted by name
```

### 3. Test GET Country by Code

```bash
# By Alpha-2 code
curl http://localhost:8080/api/country/RU

# By Alpha-3 code
curl http://localhost:8080/api/country/RUS

# By Numeric code
curl http://localhost:8080/api/country/643

# All return same country object:
# {"shortName":"Russia","fullName":"Russian Federation","isoAlpha2":"RU","isoAlpha3":"RUS","isoNumeric":"643","population":146150789,"square":17125191}
```

### 4. Test CREATE Country

```bash
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName":"Japan",
    "fullName":"Japan",
    "isoAlpha2":"JP",
    "isoAlpha3":"JPN",
    "isoNumeric":"392",
    "population":125800000,
    "square":377975
  }'

# Returns: 204 No Content
# Verify with: curl http://localhost:8080/api/country/JP
```

### 5. Test UPDATE Country

```bash
curl -X PATCH http://localhost:8080/api/country/JP \
  -H "Content-Type: application/json" \
  -d '{"population":126000000,"square":377975}'

# Returns: 200 OK with updated country object
```

### 6. Test DELETE Country

```bash
curl -X DELETE http://localhost:8080/api/country/JP

# Returns: 204 No Content
# Verify deletion with: curl http://localhost:8080/api/country/JP
# Should return: 404 Not Found
```

## Error Examples

### Invalid Code Format (400)

```bash
curl http://localhost:8080/api/country/INVALID
# Response: {"error":"Invalid country code format"}
```

### Country Not Found (404)

```bash
curl http://localhost:8080/api/country/ZZZ
# Response: {"error":"Country not found"}
```

### Duplicate Entry (409)

```bash
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName":"Russia",
    "fullName":"Russian Federation",
    "isoAlpha2":"RU",
    "isoAlpha3":"RUS",
    "isoNumeric":"643",
    "population":100,
    "square":100
  }'

# Response: {"error":"Conflict: Country with ISO Alpha-2 code RU already exists"}
```

## Database Access

```bash
# Connect to MySQL container
docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db

# Query countries table
SELECT * FROM countries;

# Exit
quit
```

## Stopping Containers

```bash
docker-compose down
```

## Logs & Debugging

```bash
# View PHP logs
docker logs symfony_php

# View Nginx logs
docker logs symfony_nginx

# View MySQL logs
docker logs symfony_mysql

# View container status
docker stats
```

## Project Structure

```
app/src/
├── Controller/
│   ├── StatusController.php       (GET /api, GET /api/ping)
│   └── CountryController.php      (CRUD endpoints)
├── Model/
│   ├── Country.php                (Data model)
│   ├── CountryScenarios.php       (Business logic)
│   ├── CountryRepository.php      (Interface)
│   ├── Exceptions/                (Custom exceptions)
│   └── StranaM.php                (English alias)
├── Rdb/
│   ├── SqlHelper.php              (Database connection)
│   └── CountryStorage.php         (Data persistence)
└── Kernel.php                      (Symfony kernel)

config/
├── services.yaml                   (Dependency injection)
├── bundles.php                     (Bundle configuration)
└── packages/
    ├── framework.yaml
    ├── routing.yaml
    └── ...

mysql/init/
├── 01-create-schema.sql            (Database schema)
└── 02-seed-data.sql                (Test data)

nginx/
└── default.conf                    (Nginx configuration)

docs/
├── API_DOCUMENTATION.md            (Detailed API docs)
├── class_diagram.txt               (Architecture)
└── screenshots/                    (Test screenshots)
```

## Cyrillic Support

The API fully supports Cyrillic (Russian) text:

```bash
# Create with Russian names
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName":"Болгария",
    "fullName":"Республика Болгария",
    "isoAlpha2":"BG",
    "isoAlpha3":"BGR",
    "isoNumeric":"100",
    "population":6856000,
    "square":110910
  }'

# Retrieve with Cyrillic support
curl http://localhost:8080/api/country/BG
# Returns with Cyrillic names properly encoded in JSON
```

## Validation Rules

### Codes
- Alpha-2: exactly 2 letters (A-Z)
- Alpha-3: exactly 3 letters (A-Z)
- Numeric: exactly 3 digits (0-9)
- All codes must be unique

### Names
- Short name: 1-100 characters, not empty, unique
- Full name: 1-200 characters, not empty, unique
- Both support Cyrillic characters

### Numbers
- Population: non-negative integer
- Square: non-negative decimal
- Both required

## Complete API Examples

See `docs/API_DOCUMENTATION.md` for complete API specification with all endpoints, request/response formats, and error codes.

## Architecture

See `docs/class_diagram.txt` for complete system architecture and class relationships.

## Implementation Status

See `IMPLEMENTATION_STATUS.md` for detailed checklist of all 14 requirements.

## Verification Checklist

See `VERIFICATION_CHECKLIST.md` for complete testing checklist.

## File Naming

All filenames are in English (Latin characters) for Windows 10 compatibility.
See `CYRILLIC_CLEANUP_REPORT.md` for details on filename normalization.

## UTF-8 & Internationalization

See `ENCODING_VERIFICATION.md` for full details on character encoding and Cyrillic support.

## Support

For issues or questions, check:
1. Docker logs: `docker logs symfony_php`
2. Database connection: `docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db -e 'SELECT COUNT(*) FROM countries;'`
3. Composer autoload: `docker exec -it symfony_php composer dump-autoload`

## Production Considerations

- Change database password in .env
- Update APP_SECRET in .env
- Enable HTTPS in Nginx config
- Set APP_ENV=prod
- Implement rate limiting
- Add request logging
- Set up database backups
