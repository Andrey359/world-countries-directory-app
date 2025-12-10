<?php

namespace App\Rdb;

use App\Model\Country;
use App\Model\CountryRepository;
use mysqli;

class CountryStorage implements CountryRepository
{
    private SqlHelper $sqlHelper;

    public function __construct(SqlHelper $sqlHelper)
    {
        $this->sqlHelper = $sqlHelper;
    }

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

    public function findByAlpha2(string $code): ?Country
    {
        return $this->findByCode('iso_alpha2', strtoupper($code));
    }

    public function findByAlpha3(string $code): ?Country
    {
        return $this->findByCode('iso_alpha3', strtoupper($code));
    }

    public function findByNumeric(string $code): ?Country
    {
        return $this->findByCode('iso_numeric', $code);
    }

    public function save(Country $country): void
    {
        $connection = $this->sqlHelper->openDbConnection();
        
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
        $connection->close();
    }

    public function update(string $code, Country $country): void
    {
        $connection = $this->sqlHelper->openDbConnection();
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
        $connection->close();
    }

    public function delete(string $code): void
    {
        $connection = $this->sqlHelper->openDbConnection();
        $codeColumn = $this->getCodeColumn($code);
        
        $stmt = $connection->prepare("DELETE FROM countries WHERE {$codeColumn} = ?");
        $codeValue = $this->normalizeCode($code);
        $stmt->bind_param('s', $codeValue);
        $stmt->execute();
        $stmt->close();
        $connection->close();
    }

    public function existsByShortName(string $shortName, ?string $excludeCode = null): bool
    {
        $connection = $this->sqlHelper->openDbConnection();
        
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
        $connection->close();
        
        return $exists;
    }

    public function existsByFullName(string $fullName, ?string $excludeCode = null): bool
    {
        $connection = $this->sqlHelper->openDbConnection();
        
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
        $connection->close();
        
        return $exists;
    }

    public function existsByCode(string $code): bool
    {
        return $this->findByCodeColumn($this->getCodeColumn($code), $this->normalizeCode($code)) !== null;
    }

    private function findByCode(string $column, string $value): ?Country
    {
        return $this->findByCodeColumn($column, $value);
    }

    private function findByCodeColumn(string $column, string $value): ?Country
    {
        $connection = $this->sqlHelper->openDbConnection();
        $stmt = $connection->prepare("SELECT * FROM countries WHERE {$column} = ?");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $country = null;
        if ($row = $result->fetch_assoc()) {
            $country = $this->mapRowToCountry($row);
        }
        
        $stmt->close();
        $connection->close();
        
        return $country;
    }

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

    private function normalizeCode(string $code): string
    {
        if (preg_match('/^[A-Z]{2,3}$/i', $code)) {
            return strtoupper($code);
        }
        return $code;
    }
}
