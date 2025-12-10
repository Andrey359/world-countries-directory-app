# World Countries Directory API

Complete REST API for managing world countries directory with CRUD operations, built with Symfony 7.0 and MySQL 8.0.

## Quick Overview

This project implements a fully functional API for storing and managing country information with the following features:

- Full CRUD operations (Create, Read, Update, Delete)
- Support for multiple code formats (Alpha-2, Alpha-3, Numeric ISO codes)
- Complete data validation
- Cyrillic (Russian) text support
- Docker containerization
- MySQL database with UTF-8 support
- Clean architecture with dependency injection
- Comprehensive error handling

## API Endpoints

```
GET    /api                    -> Server status
GET    /api/ping               -> Ping test
GET    /api/country            -> Get all countries
GET    /api/country/{code}     -> Get country by code (Alpha-2, Alpha-3, or Numeric)
POST   /api/country            -> Create new country
PATCH  /api/country/{code}     -> Update country
DELETE /api/country/{code}     -> Delete country
```

## Quick Start

### Prerequisites
- Docker Desktop
- Docker Compose
- Git

### Installation

```bash
# Clone repository
git clone https://github.com/Andrey359/world-countries-directory-app.git
cd world-countries-directory-app

# Start Docker containers
docker-compose up -d

# Install dependencies
docker exec -it symfony_php composer install
```

### Test the API

```bash
# Check server status
curl http://localhost:8080/api

# Get all countries
curl http://localhost:8080/api/country

# Get country by code
curl http://localhost:8080/api/country/RU

# Create new country
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{"shortName":"Japan","fullName":"Japan","isoAlpha2":"JP","isoAlpha3":"JPN","isoNumeric":"392","population":125800000,"square":377975}'

# Update country
curl -X PATCH http://localhost:8080/api/country/JP \
  -H "Content-Type: application/json" \
  -d '{"population":126000000}'

# Delete country
curl -X DELETE http://localhost:8080/api/country/JP
```

See [QUICK_START.md](QUICK_START.md) for detailed testing guide.

## Documentation

- **[QUICK_START.md](QUICK_START.md)** - Complete setup and testing guide
- **[FINAL_ANALYSIS_REPORT.md](FINAL_ANALYSIS_REPORT.md)** - Complete project analysis and verification
- **[CYRILLIC_CLEANUP_REPORT.md](CYRILLIC_CLEANUP_REPORT.md)** - Filename verification and cleanup
- **[VERIFICATION_CHECKLIST.md](VERIFICATION_CHECKLIST.md)** - Testing checklist
- **[TECHNICAL_REQUIREMENTS.md](TECHNICAL_REQUIREMENTS.md)** - Requirements mapping
- **[ENCODING_VERIFICATION.md](ENCODING_VERIFICATION.md)** - UTF-8 and Cyrillic support
- **[IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md)** - Feature implementation status
- **[docs/API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)** - Complete API specification
- **[docs/class_diagram.txt](docs/class_diagram.txt)** - Architecture diagram

## Project Structure

```
world-countries-directory-app/
|
+-- app/                          Symfony application root
|   +-- src/
|   |   +-- Controller/            HTTP controllers
|   |   |   +-- StatusController.php
|   |   |   +-- CountryController.php
|   |   |
|   |   +-- Model/                 Domain models and business logic
|   |   |   +-- Country.php                (Data model)
|   |   |   +-- CountryScenarios.php      (Business logic)
|   |   |   +-- CountryRepository.php     (Repository interface)
|   |   |   +-- StranaM.php               (Alias)
|   |   |   +-- Exceptions/               (Custom exceptions)
|   |   |
|   |   +-- Rdb/                   Data access layer
|   |   |   +-- SqlHelper.php             (Database connection)
|   |   |   +-- CountryStorage.php        (Database operations)
|   |   |
|   |   +-- Kernel.php             Symfony Kernel
|   |
|   +-- config/                    Configuration
|   |   +-- services.yaml          (Dependency injection)
|   |   +-- bundles.php
|   |   +-- packages/
|   |
|   +-- public/
|   |   +-- index.php              (Entry point)
|   |
|   +-- bin/
|   |   +-- console               (CLI tool)
|   |
|   +-- composer.json              Composer dependencies
|   +-- .env                       Environment variables
|   +-- .env.example               Example env
|   +-- .gitignore
|
+-- mysql/
|   +-- init/                      Database initialization
|   |   +-- 01-create-schema.sql   (Table schema)
|   |   +-- 02-seed-data.sql       (Test data - 10 countries)
|
+-- nginx/
|   +-- default.conf               Nginx configuration
|
+-- docs/                          Documentation
|   +-- API_DOCUMENTATION.md       (Complete API spec)
|   +-- class_diagram.txt          (Architecture)
|   +-- screenshots/               (Test screenshots)
|
+-- docker-compose.yml             Docker Compose configuration
+-- Dockerfile                     PHP container definition
+-- README.md                      (This file)
+-- QUICK_START.md                 (Setup and testing guide)
+-- FINAL_ANALYSIS_REPORT.md        (Complete analysis)
+-- CYRILLIC_CLEANUP_REPORT.md     (Filename verification)
+-- VERIFICATION_CHECKLIST.md      (Testing checklist)
+-- TECHNICAL_REQUIREMENTS.md      (Requirements mapping)
+-- ENCODING_VERIFICATION.md       (UTF-8 support)
+-- IMPLEMENTATION_STATUS.md       (Feature checklist)
```

## Data Model

### Country Entity

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

### Validation Rules

**ISO Codes:**
- Alpha-2: Exactly 2 letters (A-Z), case-insensitive
- Alpha-3: Exactly 3 letters (A-Z), case-insensitive  
- Numeric: Exactly 3 digits (0-9)
- All codes must be unique

**Names:**
- Short name: 1-100 characters, not empty, unique
- Full name: 1-200 characters, not empty, unique
- Full Cyrillic (Russian) support

**Numbers:**
- Population: Non-negative integer
- Square: Non-negative decimal
- Both required

## Cyrillic Support

The API fully supports Cyrillic text (Russian and other scripts):

```bash
curl -X POST http://localhost:8080/api/country \
  -H "Content-Type: application/json" \
  -d '{
    "shortName": "Bulgaria",
    "fullName": "Republic of Bulgaria",
    "isoAlpha2": "BG",
    "isoAlpha3": "BGR",
    "isoNumeric": "100",
    "population": 6856000,
    "square": 110910
  }'
```

## Database

### Connection Details
- Host: mysql (docker internal)
- Port: 3306
- Database: world_countries_db
- User: symfony_user
- Password: symfony_password (changeable in .env)

### Table Schema

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

## Architecture

### Layers

1. **Presentation Layer**: HTTP Controllers
   - StatusController: Server status and ping endpoints
   - CountryController: CRUD endpoints

2. **Business Logic Layer**: Scenarios
   - CountryScenarios: Core business logic, validation, code detection

3. **Data Access Layer**: Repository Pattern
   - CountryRepository: Interface
   - CountryStorage: MySQL implementation

4. **Database Layer**:
   - SqlHelper: Connection management
   - MySQL: Data persistence

5. **Domain Layer**:
   - Country: Data model/DTO
   - Exceptions: Custom exceptions

### Dependency Injection

All dependencies are configured in `config/services.yaml` and automatically injected:

```
CountryController -> CountryScenarios -> CountryRepository -> CountryStorage -> SqlHelper
```

## Error Handling

### HTTP Status Codes

- **200 OK**: Successful GET or PATCH
- **204 No Content**: Successful POST or DELETE
- **400 Bad Request**: Validation error or invalid code format
- **404 Not Found**: Country not found
- **409 Conflict**: Duplicate entry (code or name)
- **500 Internal Server Error**: Server error

### Exception Hierarchy

```
Exception
+-- CountryNotFoundException (404)
+-- InvalidCountryCodeException (400)
+-- ValidationException (400)
+-- DuplicateCountryException (409)
```

## Technology Stack

- **Language**: PHP 8.2+
- **Framework**: Symfony 7.0
- **Database**: MySQL 8.0
- **Web Server**: Nginx
- **Containerization**: Docker & Docker Compose
- **Package Manager**: Composer
- **Character Encoding**: UTF-8 (utf8mb4)

## Setup Instructions

### 1. Clone Repository
```bash
git clone https://github.com/Andrey359/world-countries-directory-app.git
cd world-countries-directory-app
```

### 2. Start Docker
```bash
docker-compose up -d
```

### 3. Install Dependencies
```bash
docker exec -it symfony_php composer install
```

### 4. Verify Installation
```bash
curl http://localhost:8080/api
# Should return: {"status":"server is running","host":"localhost","protocol":"http"}
```

## Deployment

### Stop Containers
```bash
docker-compose down
```

### View Logs
```bash
docker logs symfony_php
docker logs symfony_nginx
docker logs symfony_mysql
```

### Connect to Database
```bash
docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db
```

## File Naming Conventions

- All filenames use English (Latin) characters only
- Windows 10 compatible paths
- UTF-8 encoding throughout
- Full support for Cyrillic content in database and API responses

See [CYRILLIC_CLEANUP_REPORT.md](CYRILLIC_CLEANUP_REPORT.md) for details.

## Requirements Implementation

All 14 requirements from technical specification are fully implemented:

1. Project structure with Symfony 7.0
2. StatusController with /api and /api/ping endpoints
3. Country model with all required fields
4. Full CRUD operations (GET, POST, PATCH, DELETE)
5. Code type detection (Alpha-2, Alpha-3, Numeric)
6. Comprehensive validation
7. Custom exception handling
8. Database schema with proper indexes
9. CountryRepository pattern
10. CountryScenarios business logic
11. Docker containerization
12. MySQL with UTF-8 support
13. Complete API documentation
14. Comprehensive testing and verification

See [IMPLEMENTATION_STATUS.md](IMPLEMENTATION_STATUS.md) for detailed checklist.

## Support

For issues or questions:
1. Check [QUICK_START.md](QUICK_START.md) for common problems
2. Review Docker logs: `docker logs symfony_php`
3. Check database connection: `docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db -e 'SHOW TABLES;'`
4. Review API documentation: [docs/API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)

## Project Status

Status: COMPLETE AND VERIFIED

All requirements met, all CRUD operations working, all validations in place, all documentation complete.

Ready for production deployment.
