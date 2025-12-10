# Database Scripts & Architecture Verification

Date: 2025-12-10
Status: VERIFIED AND COMPLETE

## Database Scripts

### Location: mysql/init/

Two SQL scripts handle complete database setup:

#### 1. 01-create-schema.sql

Purpose: Create the countries table with proper schema

Features:
- Drops existing table to ensure clean setup
- Creates countries table with UTF-8 support (utf8mb4_unicode_ci)
- All fields with proper types and constraints
- Unique indexes on all code fields
- Proper timestamps for audit trail

Table Structure:
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

Key Points:
- utf8mb4 charset for full Unicode support including Cyrillic
- utf8mb4_unicode_ci collation for case-insensitive comparisons
- InnoDB engine for transaction support
- AUTO_INCREMENT for primary key
- Indexes on code fields for fast lookups
- Default values for population and square
- Automatic timestamps (created_at, updated_at)

Execution: Automatically run by Docker on first container start

#### 2. 02-seed-data.sql

Purpose: Insert test data for development and testing

Features:
- Cleans existing data with TRUNCATE
- Inserts 10 countries from different regions
- Covers all code types (Alpha-2, Alpha-3, Numeric)
- Real-world data for testing

Test Countries (10 total):
1. Russia (RU, RUS, 643)
2. USA (US, USA, 840)
3. China (CN, CHN, 156)
4. Germany (DE, DEU, 276)
5. Japan (JP, JPN, 392)
6. United Kingdom (GB, GBR, 826)
7. France (FR, FRA, 250)
8. India (IN, IND, 356)
9. Brazil (BR, BRA, 076)
10. Canada (CA, CAN, 124)

Key Points:
- TRUNCATE ensures clean state before insert
- Includes population and area data
- All code formats represented
- Different regions covered
- Verification query included

Execution: Automatically run by Docker on first container start

## How Database Scripts Work

### Docker Integration

Scripts are mounted in Docker MySQL container:

```yaml
services:
  mysql:
    volumes:
      - ./mysql/init:/docker-entrypoint-initdb.d
```

MySQL container automatically executes all .sql files in /docker-entrypoint-initdb.d in alphabetical order:
1. 01-create-schema.sql runs first
2. 02-seed-data.sql runs second
3. Database is fully initialized on first start

### Initialization Flow

```
docker-compose up -d
    |
    v
MySQL container starts
    |
    v
Entrypoint script checks /docker-entrypoint-initdb.d
    |
    v
Runs: 01-create-schema.sql
    - Creates countries table
    - Sets up indexes
    - Ensures UTF-8 support
    |
    v
Runs: 02-seed-data.sql
    - Truncates table (if exists)
    - Inserts 10 test countries
    - Verifies data count
    |
    v
Database ready for application
```

## Class Diagram

### Location: docs/class_diagram.txt

Contains complete system architecture with:

**5 Layer Architecture:**

1. **Presentation Layer**
   - StatusController (status, ping endpoints)
   - CountryController (CRUD operations)
   - Handles HTTP requests/responses

2. **Business Logic Layer**
   - CountryScenarios (core logic, validation)
   - CountryRepository (interface definition)
   - Code type detection
   - Validation and duplicate checking

3. **Data Access Layer**
   - CountryStorage (repository implementation)
   - SqlHelper (database connection)
   - Prepared statements
   - Connection management

4. **Domain Model Layer**
   - Country (data model/DTO)
   - Custom Exceptions:
     - CountryNotFoundException (404)
     - InvalidCountryCodeException (400)
     - ValidationException (400)
     - DuplicateCountryException (409)

5. **Database Layer**
   - MySQL with utf8mb4
   - countries table
   - Indexes and constraints

### Diagram Format

ASCII block diagram showing:
- Class boxes with attributes and methods
- Inheritance relationships
- Interface implementation
- Dependency arrows
- Data flow between layers

### Key Architecture Principles

```
Separation of Concerns:
- Each layer has specific responsibility
- Controllers: HTTP handling
- Scenarios: Business logic
- Storage: Data persistence
- Domain: Data models

Dependency Injection:
- SqlHelper injected into CountryStorage
- CountryRepository injected into CountryScenarios
- CountryScenarios injected into CountryController
- Configured in config/services.yaml

Interface-Based Design:
- CountryRepository is interface
- CountryStorage implements interface
- Easier to test and maintain
- Easy to swap implementations

Exception Handling:
- Custom exceptions for each error type
- Proper HTTP status codes
- Meaningful error messages
```

## Verification

### Database Scripts Verified

- OK: 01-create-schema.sql exists and is correct
- OK: Table definition complete with all fields
- OK: UTF-8 charset properly configured
- OK: All indexes present
- OK: 02-seed-data.sql exists and is correct
- OK: Contains 10 test countries
- OK: All code types represented
- OK: Data is realistic
- OK: TRUNCATE ensures clean state
- OK: Verification query included

### Class Diagram Verified

- OK: docs/class_diagram.txt exists
- OK: Shows complete 5-layer architecture
- OK: All classes documented
- OK: All relationships shown
- OK: All methods documented
- OK: Data flow explained
- OK: Architecture principles listed

## Usage

### View Database Scripts

```bash
cat mysql/init/01-create-schema.sql
cat mysql/init/02-seed-data.sql
```

### View Class Diagram

```bash
cat docs/class_diagram.txt
```

### Verify Database After Setup

```bash
# Connect to MySQL
docker exec -it symfony_mysql mysql -u symfony_user -psymfony_password world_countries_db

# Check table structure
DESCRIBE countries;

# Check data
SELECT COUNT(*) FROM countries;
SELECT * FROM countries;

# Check indexes
SHOW INDEX FROM countries;
```

## Complete Checklist

- OK: Database initialization scripts exist
- OK: Schema script creates proper table
- OK: UTF-8 support configured
- OK: Seed data with 10 countries ready
- OK: Class diagram complete and accurate
- OK: Architecture documented
- OK: All layers explained
- OK: Data flow documented
- OK: Exception handling shown
- OK: Dependency injection illustrated

## Conclusion

All required database scripts and architecture diagrams are present and verified:
1. Database scripts (2 SQL files) properly set up MySQL
2. Class diagram shows complete system architecture
3. All 5 layers documented with relationships
4. Ready for deployment and testing

Status: COMPLETE AND VERIFIED
