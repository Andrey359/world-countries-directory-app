<?php

namespace App\Model;

use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\InvalidCountryCodeException;
use App\Model\Exceptions\ValidationException;
use App\Model\Exceptions\DuplicateCountryException;

class CountryScenarios
{
    private CountryRepository $repository;

    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all countries from the directory
     * @return array Array of Country objects
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Get country by code (supports Alpha-2, Alpha-3, or Numeric)
     * Automatically detects code type and searches accordingly
     * @param string $code Country code
     * @return Country Country object
     * @throws InvalidCountryCodeException If code format is invalid
     * @throws CountryNotFoundException If country not found
     */
    public function get(string $code): Country
    {
        $codeType = $this->detectCodeType($code);
        
        if ($codeType === null) {
            throw new InvalidCountryCodeException($code);
        }

        $country = null;
        switch ($codeType) {
            case 'alpha2':
                $country = $this->repository->findByAlpha2(strtoupper($code));
                break;
            case 'alpha3':
                $country = $this->repository->findByAlpha3(strtoupper($code));
                break;
            case 'numeric':
                $country = $this->repository->findByNumeric($code);
                break;
        }

        if ($country === null) {
            throw new CountryNotFoundException($code);
        }

        return $country;
    }

    /**
     * Store new country in the directory
     * Validates codes, names, population and square
     * Checks uniqueness of codes and names
     * @param Country $country Country object to store
     * @return void
     * @throws ValidationException If validation fails
     * @throws DuplicateCountryException If country with same code or name exists
     */
    public function store(Country $country): void
    {
        $this->validateCountry($country, true);
        $this->checkDuplicates($country);
        $this->repository->save($country);
    }

    /**
     * Edit existing country by code
     * Codes cannot be changed, only names, population and square can be updated
     * @param string $code Country code to identify country (any type)
     * @param Country $country Updated country data (codes should not be changed)
     * @return Country Updated country object
     * @throws InvalidCountryCodeException If code format is invalid
     * @throws CountryNotFoundException If country not found
     * @throws ValidationException If validation fails
     * @throws DuplicateCountryException If updated data conflicts with existing countries
     */
    public function edit(string $code, Country $country): Country
    {
        $codeType = $this->detectCodeType($code);
        
        if ($codeType === null) {
            throw new InvalidCountryCodeException($code);
        }

        $existingCountry = $this->get($code);
        
        // Ensure codes are not changed
        if ($country->getIsoAlpha2() !== null && $country->getIsoAlpha2() !== $existingCountry->getIsoAlpha2()) {
            throw new ValidationException('Cannot change ISO Alpha-2 code');
        }
        if ($country->getIsoAlpha3() !== null && $country->getIsoAlpha3() !== $existingCountry->getIsoAlpha3()) {
            throw new ValidationException('Cannot change ISO Alpha-3 code');
        }
        if ($country->getIsoNumeric() !== null && $country->getIsoNumeric() !== $existingCountry->getIsoNumeric()) {
            throw new ValidationException('Cannot change ISO Numeric code');
        }

        // Set existing codes
        $country->setIsoAlpha2($existingCountry->getIsoAlpha2());
        $country->setIsoAlpha3($existingCountry->getIsoAlpha3());
        $country->setIsoNumeric($existingCountry->getIsoNumeric());

        $this->validateCountry($country, false);
        $this->checkDuplicates($country, $code);
        $this->repository->update($code, $country);

        return $this->get($code);
    }

    /**
     * Delete country by code
     * @param string $code Country code (any type: alpha2, alpha3, or numeric)
     * @return void
     * @throws InvalidCountryCodeException If code format is invalid
     * @throws CountryNotFoundException If country not found
     */
    public function delete(string $code): void
    {
        $codeType = $this->detectCodeType($code);
        
        if ($codeType === null) {
            throw new InvalidCountryCodeException($code);
        }

        $this->get($code);
        $this->repository->delete($code);
    }

    /**
     * Detect code type based on format
     * @param string $code Code to check
     * @return string|null 'alpha2', 'alpha3', 'numeric' or null if invalid
     */
    private function detectCodeType(string $code): ?string
    {
        if (preg_match('/^[A-Z]{2}$/i', $code)) {
            return 'alpha2';
        }
        if (preg_match('/^[A-Z]{3}$/i', $code)) {
            return 'alpha3';
        }
        if (preg_match('/^\d{3}$/', $code)) {
            return 'numeric';
        }
        return null;
    }

    /**
     * Validate country data
     * @param Country $country Country to validate
     * @param bool $validateCodes Whether to validate codes
     * @return void
     * @throws ValidationException If validation fails
     */
    private function validateCountry(Country $country, bool $validateCodes): void
    {
        if ($validateCodes) {
            if (empty($country->getIsoAlpha2()) || !preg_match('/^[A-Z]{2}$/i', $country->getIsoAlpha2())) {
                throw new ValidationException('Invalid ISO Alpha-2 code format (must be 2 letters)');
            }
            if (empty($country->getIsoAlpha3()) || !preg_match('/^[A-Z]{3}$/i', $country->getIsoAlpha3())) {
                throw new ValidationException('Invalid ISO Alpha-3 code format (must be 3 letters)');
            }
            if (empty($country->getIsoNumeric()) || !preg_match('/^\d{3}$/', $country->getIsoNumeric())) {
                throw new ValidationException('Invalid ISO Numeric code format (must be 3 digits)');
            }
        }

        if (empty(trim($country->getShortName() ?? ''))) {
            throw new ValidationException('Short name cannot be empty');
        }
        if (empty(trim($country->getFullName() ?? ''))) {
            throw new ValidationException('Full name cannot be empty');
        }
        if ($country->getPopulation() === null || $country->getPopulation() < 0) {
            throw new ValidationException('Population must be non-negative number');
        }
        if ($country->getSquare() === null || $country->getSquare() < 0) {
            throw new ValidationException('Square must be non-negative number');
        }
    }

    /**
     * Check for duplicate names and codes
     * @param Country $country Country to check
     * @param string|null $excludeCode Code to exclude from check (for updates)
     * @return void
     * @throws DuplicateCountryException If duplicate found
     */
    private function checkDuplicates(Country $country, ?string $excludeCode = null): void
    {
        if ($this->repository->existsByShortName($country->getShortName(), $excludeCode)) {
            throw new DuplicateCountryException('short name', $country->getShortName());
        }
        if ($this->repository->existsByFullName($country->getFullName(), $excludeCode)) {
            throw new DuplicateCountryException('full name', $country->getFullName());
        }
        
        if ($excludeCode === null) {
            if ($this->repository->existsByCode($country->getIsoAlpha2())) {
                throw new DuplicateCountryException('ISO Alpha-2 code', $country->getIsoAlpha2());
            }
            if ($this->repository->existsByCode($country->getIsoAlpha3())) {
                throw new DuplicateCountryException('ISO Alpha-3 code', $country->getIsoAlpha3());
            }
            if ($this->repository->existsByCode($country->getIsoNumeric())) {
                throw new DuplicateCountryException('ISO Numeric code', $country->getIsoNumeric());
            }
        }
    }
}
