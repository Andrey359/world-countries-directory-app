-- Create database schema for world countries directory

DROP TABLE IF EXISTS countries;

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