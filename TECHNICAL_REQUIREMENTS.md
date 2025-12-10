# Technical Requirements - Detailed Implementation

## 1. Project Structure

world-countries-directory-app/
- app/
  - src/
    - Controller/
      - StatusController.php
      - CountryController.php
    - Model/
      - Country.php
      - CountryScenarios.php
      - CountryRepository.php
      - Exceptions/
        - CountryNotFoundException.php
        - InvalidCountryCodeException.php
        - ValidationException.php
        - DuplicateCountryException.php
    - Rdb/
      - SqlHelper.php
      - CountryStorage.php
    - Kernel.php
  - config/
    - bundles.php
    - packages/
      - framework.yaml
      - routing.yaml
    - services.yaml
  - public/
    - index.php
  - bin/
    - console
  - .env
  - .env.example
  - .gitignore
  - composer.json
  - composer.lock
- mysql/
  - init/
    - 01-create-schema.sql
    - 02-seed-data.sql
- nginx/
  - default.conf
- docs/
  - API_DOCUMENTATION.md
  - class_diagram.txt
  - screenshots/
- .env
- .gitignore
- docker-compose.yml
- Dockerfile
- README.md
- IMPLEMENTATION_STATUS.md
- VERIFICATION_CHECKLIST.md
- TECHNICAL_REQUIREMENTS.md

## 2. Technology Stack

- Language: PHP 8.2+
- Framework: Symfony 7.0
- Database: MySQL 8.0
- Database Driver: mysqli with prepared statements
- Server: Nginx + PHP-FPM
- Containerization: Docker & Docker Compose
- Character Encoding: UTF-8 (utf8mb4)
- Package Manager: Composer

## 3. API Endpoints

### Status Endpoints
GET  /api                          -> Server status (200)
GET  /api/ping                     -> Ping status (200)

### Country CRUD Endpoints
GET    /api/country               -> Get all (200)
GET    /api/country/{code}        -> Get by code (200|400|404)
POST   /api/country               -> Create (204|400|409)
PATCH  /api/country/{code}        -> Update (200|400|404|409)
DELETE /api/country/{code}        -> Delete (204|400|404)

## 4. Data Model

### Country Entity
- shortName: string (100 chars max, unique, not empty)
- fullName: string (200 chars max, unique, not empty)
- isoAlpha2: string (2 letters, uppercase, unique)
- isoAlpha3: string (3 letters, uppercase, unique)
- isoNumeric: string (3 digits, unique)
- population: integer (>= 0, required)
- square: decimal (>= 0, required)

## 5. Validation Rules

### ISO Codes
- Alpha-2: Exactly 2 letters [A-Z], case-insensitive
- Alpha-3: Exactly 3 letters [A-Z], case-insensitive
- Numeric: Exactly 3 digits [0-9]
- Unique: All codes must be unique in database

### Names
- Not Empty: Cannot be null or whitespace only
- Unique: Both short and full names must be unique
- Length: 100 chars max (short), 200 chars max (full)
- UTF-8: Full Cyrillic support (Russian, etc.)

### Numbers
- Population: Non-negative integer
- Square: Non-negative decimal
- Required: Both must be provided

## 6. Error Handling

### HTTP Status Codes
- 200 OK: Successful GET or PATCH
- 204 No Content: Successful POST or DELETE
- 400 Bad Request: Validation error or invalid code format
- 404 Not Found: Resource not found
- 409 Conflict: Duplicate entry (code or name)
- 500 Internal Server Error: Server error

### Exception Classes
- CountryNotFoundException (404)
- InvalidCountryCodeException (400)
- ValidationException (400)
- DuplicateCountryException (409)

## 7. Code Organization

### Presentation Layer
- StatusController: Server status endpoints
- CountryController: CRUD endpoints, request/response handling

### Business Logic Layer
- CountryScenarios: CRUD operations, validation, code detection

### Data Access Layer
- CountryRepository: Interface for data operations
- CountryStorage: MySQL implementation with mysqli
- SqlHelper: Database connection management

### Domain Layer
- Country: Entity/DTO for data transfer
- Exceptions: Domain-specific exceptions

## 8. Security

- Prepared Statements: All SQL queries use parameterized queries
- Input Validation: Strict validation of all inputs
- No SQL Injection: Parameters bound separately
- No XSS: JSON responses properly escaped
- No Sensitive Data: No passwords or keys in code
- Error Messages: User-friendly without stack traces

## 9. UTF-8 & Internationalization

- Charset: UTF-8 (utf8mb4) in all files
- Database: utf8mb4_unicode_ci collation
- MySQL Connection: set_charset('utf8mb4')
- JSON Encoding: Full UTF-8 support
- Cyrillic Support: Russian and other scripts work
- BOM: Files properly encoded without BOM

## 10. Windows 10 Compatibility

- File Paths: Forward slashes used
- Line Endings: LF (Unix standard)
- No Unix-specific: No shell scripts
- Docker Desktop: Full compatibility
- File Naming: ASCII characters only
- No Symlinks: Standard file operations

## 11. Testing

### Positive Test Cases
- Create valid country
- Update valid country fields
- Delete existing country
- Get all countries
- Get by Alpha-2 code
- Get by Alpha-3 code
- Get by Numeric code

### Negative Test Cases
- Invalid code format (400)
- Country not found (404)
- Duplicate code (409)
- Duplicate name (409)
- Empty names (400)
- Negative numbers (400)
- Invalid JSON (400)
- Try to update codes (400)

## 12. Docker Setup

### docker-compose.yml
- PHP Service: PHP 8.2-FPM
- Nginx Service: Latest Alpine
- MySQL Service: 8.0 with initialization
- Volumes: Proper mounts
- Networks: Internal communication
- Environment: .env file support

### Dockerfile
- Base: php:8.2-fpm
- Extensions: mysqli, pdo, curl, etc.
- Composer: Latest version
- Symfony CLI: Included
- UTF-8: Proper locale settings

## 13. Database Operations

### Script 1: Schema Creation
- Table with proper indexes
- UTF-8 collation
- Constraints and defaults
- Timestamp fields

### Script 2: Test Data
- 10+ countries
- Various regions
- Data validation
- Cleanup before insert

## 14. Documentation

- README.md: Setup and usage
- API_DOCUMENTATION.md: Endpoints and examples
- class_diagram.txt: Architecture overview
- IMPLEMENTATION_STATUS.md: Implementation checklist
- VERIFICATION_CHECKLIST.md: Testing checklist
- TECHNICAL_REQUIREMENTS.md: This file

Completion Status: 100%

All 14 requirements fully implemented and verified.
