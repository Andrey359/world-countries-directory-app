<?php

namespace App\Rdb;

use App\Model\Country;
use App\Model\CountryRepository;
use mysqli;
use Exception;

class CountryStorage implements CountryRepository
{
    private SqlHelper $sqlHelper;

    public function __construct(SqlHelper $sqlHelper)
    {
        $this->sqlHelper = $sqlHelper;
    }

    /**
     * Get all countries from database, ordered by short name
     * @return array Array of Country objects
     */
    public function findAll(): array
    {
        $connection = $this->sqlHelper->openDbConnection();
        $query = "SELECT * FROM countries ORDER BY short_name ASC";
        $result = $connection->query($query);
        
        $countries = [];
        while ($row = $result->fetch_assoc()) {
            $countries[] = $this->mapRowToCountry($row);
        }
        
        $connection->close();
        return $countries;
    }

    /**
     * Find country by ISO Alpha-2 code
     * @param string $code Two-letter ISO code
     * @return Country|null
     */
    public function findByAlpha2(string $code): ?Country
    {
        return $this->findByCode('iso_alpha2', strtoupper($code));
    }

    /**
     * Find country by ISO Alpha-3 code
     * @param string $code Three-letter ISO code
     * @return Country|null
     */
    public function findByAlpha3(string $code): ?Country
    {
        return $this->findByCode('iso_alpha3', strtoupper($code));
    }

    /**
     * Find country by ISO numeric code
     * @param string $code Numeric ISO code
     * @return Country|null
     */
    public function findByNumeric(string $code): ?Country
    {
        return $this->findByCode('iso_numeric', $code);
    }

    /**
     * Save new country to database
     * @param Country $country Country object to save
     * @return void
     * @throws Exception If database error occurs
     */
    public function save(Country $country): void
    {
        $connection = $this->sqlHelper->openDbConnection();
        
        try {
            $stmt = $connection->prepare(
                "INSERT INTO countries (short_name, full_name, iso_alpha2, iso_alpha3, iso_numeric, population, square) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            
            $shortName = $country->getShortName();
            $fullName = $country->getFullName();
            $isoAlpha2 = strtoupper($country->getIsoAlpha2());
            $isoAlpha3 = strtoupper($country->getIsoAlpha3());
            $isoNumeric = $country->getIsoNumeric();
            $population = $country->getPopulation();
            $square = $country->getSquare();
            
            $stmt->bind_param(
                'sssssid',
                $shortName,
                $fullName,
                $isoAlpha2,
                $isoAlpha3,
                $isoNumeric,
                $population,
                $square
            );
            
            $stmt->execute();
            $stmt->close();
        } finally {
            $connection->close();
        }
    }

    /**
     * Update existing country in database
     * @param string $code Country code to identify record
     * @param Country $country Updated country data
     * @return void
     * @throws Exception If database error occurs
     */
    public function update(string $code, Country $country): void
    {
        $connection = $this->sqlHelper->openDbConnection();
        
        try {
            $codeColumn = $this->getCodeColumn($code);
            
            $stmt = $connection->prepare(
                "UPDATE countries 
                 SET short_name = ?, full_name = ?, population = ?, square = ? 
                 WHERE {$codeColumn} = ?"
            );
            
            $shortName = $country->getShortName();
            $fullName = $country->getFullName();
            $population = $country->getPopulation();
            $square = $country->getSquare();
            $codeValue = $this->normalizeCode($code);
            
            $stmt->bind_param(
                'ssids',
                $shortName,
                $fullName,
                $population,
                $square,
                $codeValue
            );
            
            $stmt->execute();
            $stmt->close();
        } finally {
            $connection->close();
        }
    }

    /**
     * Delete country from database by code
     * @param string $code Country code
     * @return void
     * @throws Exception If database error occurs
     */
    public function delete(string $code): void
    {
        $connection = $this->sqlHelper->openDbConnection();
        
        try {
            $codeColumn = $this->getCodeColumn($code);
            
            $stmt = $connection->prepare("DELETE FROM countries WHERE {$codeColumn} = ?");
            $codeValue = $this->normalizeCode($code);
            $stmt->bind_param('s', $codeValue);
            $stmt->execute();
            $stmt->close();
        } finally {
            $connection->close();
        }
    }

    /**
     * Check if country with given short name exists
     * @param string $shortName Short name to check
     * @param string|null $excludeCode Code to exclude (for updates)
     * @return bool
     */
    public function existsByShortName(string $shortName, ?string $excludeCode = null): bool
    {
        $connection = $this->sqlHelper->openDbConnection();
        
        try {
            if ($excludeCode === null) {
                $stmt = $connection->prepare("SELECT COUNT(*) as cnt FROM countries WHERE short_name = ?");
                $stmt->bind_param('s', $shortName);
            } else {
                $codeColumn = $this->getCodeColumn($excludeCode);
                $stmt = $connection->prepare(
                    "SELECT COUNT(*) as cnt FROM countries WHERE short_name = ? AND {$codeColumn} != ?"
                );
                $excludeValue = $this->normalizeCode($excludeCode);
                $stmt->bind_param('ss', $shortName, $excludeValue);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $exists = $row['cnt'] > 0;
            
            $stmt->close();
            return $exists;
        } finally {
            $connection->close();
        }
    }

    /**
     * Check if country with given full name exists
     * @param string $fullName Full name to check
     * @param string|null $excludeCode Code to exclude (for updates)
     * @return bool
     */
    public function existsByFullName(string $fullName, ?string $excludeCode = null): bool
    {
        $connection = $this->sqlHelper->openDbConnection();
        
        try {
            if ($excludeCode === null) {
                $stmt = $connection->prepare("SELECT COUNT(*) as cnt FROM countries WHERE full_name = ?");
                $stmt->bind_param('s', $fullName);
            } else {
                $codeColumn = $this->getCodeColumn($excludeCode);
                $stmt = $connection->prepare(
                    "SELECT COUNT(*) as cnt FROM countries WHERE full_name = ? AND {$codeColumn} != ?"
                );
                $excludeValue = $this->normalizeCode($excludeCode);
                $stmt->bind_param('ss', $fullName, $excludeValue);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $exists = $row['cnt'] > 0;
            
            $stmt->close();
            return $exists;
        } finally {
            $connection->close();
        }
    }

    /**
     * Check if country with given code exists (any type)
     * @param string $code Country code
     * @return bool
     */
    public function existsByCode(string $code): bool
    {
        return $this->findByCodeColumn($this->getCodeColumn($code), $this->normalizeCode($code)) !== null;
    }

    /**
     * Find country by specific column
     * @param string $column Database column name
     * @param string $value Column value
     * @return Country|null
     */
    private function findByCode(string $column, string $value): ?Country
    {
        return $this->findByCodeColumn($column, $value);
    }

    /**
     * Generic method to find country by column value
     * @param string $column Column name
     * @param string $value Column value
     * @return Country|null
     */
    private function findByCodeColumn(string $column, string $value): ?Country
    {
        $connection = $this->sqlHelper->openDbConnection();
        
        try {
            $stmt = $connection->prepare("SELECT * FROM countries WHERE {$column} = ?");
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $country = null;
            if ($row = $result->fetch_assoc()) {
                $country = $this->mapRowToCountry($row);
            }
            
            $stmt->close();
            return $country;
        } finally {
            $connection->close();
        }
    }

    /**
     * Map database row to Country object
     * @param array $row Database row
     * @return Country
     */
    private function mapRowToCountry(array $row): Country
    {
        $country = new Country();
        $country->setShortName($row['short_name']);
        $country->setFullName($row['full_name']);
        $country->setIsoAlpha2($row['iso_alpha2']);
        $country->setIsoAlpha3($row['iso_alpha3']);
        $country->setIsoNumeric($row['iso_numeric']);
        $country->setPopulation((int)$row['population']);
        $country->setSquare((float)$row['square']);
        
        return $country;
    }

    /**
     * Determine which database column corresponds to code type
     * @param string $code Code to check
     * @return string Column name
     */
    private function getCodeColumn(string $code): string
    {
        if (preg_match('/^[A-Z]{2}$/i', $code)) {
            return 'iso_alpha2';
        }
        if (preg_match('/^[A-Z]{3}$/i', $code)) {
            return 'iso_alpha3';
        }
        return 'iso_numeric';
    }

    /**
     * Normalize code for database queries
     * Converts letters to uppercase
     * @param string $code Code to normalize
     * @return string
     */
    private function normalizeCode(string $code): string
    {
        if (preg_match('/^[A-Z]{2,3}$/i', $code)) {
            return strtoupper($code);
        }
        return $code;
    }
}
