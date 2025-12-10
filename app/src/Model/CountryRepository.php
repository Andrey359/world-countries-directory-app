<?php

namespace App\Model;

interface CountryRepository
{
    /**
     * Get all countries from storage
     * @return Country[] Array of Country objects
     */
    public function findAll(): array;

    /**
     * Find country by ISO Alpha-2 code (e.g., RU, US, FR)
     * @param string $code Two-letter ISO code
     * @return Country|null Country object or null if not found
     */
    public function findByAlpha2(string $code): ?Country;

    /**
     * Find country by ISO Alpha-3 code (e.g., RUS, USA, FRA)
     * @param string $code Three-letter ISO code
     * @return Country|null Country object or null if not found
     */
    public function findByAlpha3(string $code): ?Country;

    /**
     * Find country by ISO numeric code (e.g., 643, 840, 250)
     * @param string $code Numeric ISO code
     * @return Country|null Country object or null if not found
     */
    public function findByNumeric(string $code): ?Country;

    /**
     * Save new country to storage
     * @param Country $country Country object to save
     * @return void
     */
    public function save(Country $country): void;

    /**
     * Update existing country in storage by code
     * @param string $code Country code (any type: alpha2, alpha3, or numeric)
     * @param Country $country Updated country data
     * @return void
     */
    public function update(string $code, Country $country): void;

    /**
     * Delete country from storage by code
     * @param string $code Country code (any type: alpha2, alpha3, or numeric)
     * @return void
     */
    public function delete(string $code): void;

    /**
     * Check if country with given short name exists
     * @param string $shortName Short name to check
     * @param string|null $excludeCode Code to exclude from check (for updates)
     * @return bool True if exists
     */
    public function existsByShortName(string $shortName, ?string $excludeCode = null): bool;

    /**
     * Check if country with given full name exists
     * @param string $fullName Full name to check
     * @param string|null $excludeCode Code to exclude from check (for updates)
     * @return bool True if exists
     */
    public function existsByFullName(string $fullName, ?string $excludeCode = null): bool;

    /**
     * Check if country with given code exists
     * @param string $code Country code to check (any type)
     * @return bool True if exists
     */
    public function existsByCode(string $code): bool;
}
