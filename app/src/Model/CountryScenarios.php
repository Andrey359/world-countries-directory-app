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
     * Получить все страны из справочника
     * Возвращает: Массив объектов Country
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Получить страну по коду (поддерживает Alpha-2, Alpha-3 или числовой)
     * Параметр: $code - код страны
     * Возвращает: Объект Country
     * Исключения: InvalidCountryCodeException, CountryNotFoundException
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
                $country = $this->repository->findByAlpha2($code);
                break;
            case 'alpha3':
                $country = $this->repository->findByAlpha3($code);
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
     * Сохранить новую страну в справочник
     * Параметр: $country - объект Country для сохранения
     * Исключения: ValidationException, DuplicateCountryException
     */
    public function store(Country $country): void
    {
        $this->validateCountry($country, true);
        $this->checkDuplicates($country);
        $this->repository->save($country);
    }

    /**
     * Редактировать существующую страну по коду
     * Параметр: $code - код страны для идентификации
     * Параметр: $country - обновленные данные страны (коды не должны меняться)
     * Возвращает: Обновленный объект Country
     * Исключения: InvalidCountryCodeException, CountryNotFoundException, ValidationException, DuplicateCountryException
     */
    public function edit(string $code, Country $country): Country
    {
        $codeType = $this->detectCodeType($code);
        
        if ($codeType === null) {
            throw new InvalidCountryCodeException($code);
        }

        $existingCountry = $this->get($code);
        
        if ($country->getIsoAlpha2() !== null && $country->getIsoAlpha2() !== $existingCountry->getIsoAlpha2()) {
            throw new ValidationException('Нельзя изменить код ISO Alpha-2');
        }
        if ($country->getIsoAlpha3() !== null && $country->getIsoAlpha3() !== $existingCountry->getIsoAlpha3()) {
            throw new ValidationException('Нельзя изменить код ISO Alpha-3');
        }
        if ($country->getIsoNumeric() !== null && $country->getIsoNumeric() !== $existingCountry->getIsoNumeric()) {
            throw new ValidationException('Нельзя изменить числовой код ISO');
        }

        $country->setIsoAlpha2($existingCountry->getIsoAlpha2());
        $country->setIsoAlpha3($existingCountry->getIsoAlpha3());
        $country->setIsoNumeric($existingCountry->getIsoNumeric());

        $this->validateCountry($country, false);
        $this->checkDuplicates($country, $code);
        $this->repository->update($code, $country);

        return $this->get($code);
    }

    /**
     * Удалить страну по коду
     * Параметр: $code - код страны
     * Исключения: InvalidCountryCodeException, CountryNotFoundException
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

    private function validateCountry(Country $country, bool $validateCodes): void
    {
        if ($validateCodes) {
            if (empty($country->getIsoAlpha2()) || !preg_match('/^[A-Z]{2}$/i', $country->getIsoAlpha2())) {
                throw new ValidationException('Неверный формат кода ISO Alpha-2');
            }
            if (empty($country->getIsoAlpha3()) || !preg_match('/^[A-Z]{3}$/i', $country->getIsoAlpha3())) {
                throw new ValidationException('Неверный формат кода ISO Alpha-3');
            }
            if (empty($country->getIsoNumeric()) || !preg_match('/^\d{3}$/', $country->getIsoNumeric())) {
                throw new ValidationException('Неверный формат числового кода ISO');
            }
        }

        if (empty(trim($country->getShortName() ?? ''))) {
            throw new ValidationException('Краткое название не может быть пустым');
        }
        if (empty(trim($country->getFullName() ?? ''))) {
            throw new ValidationException('Полное название не может быть пустым');
        }
        if ($country->getPopulation() === null || $country->getPopulation() < 0) {
            throw new ValidationException('Население должно быть неотрицательным');
        }
        if ($country->getSquare() === null || $country->getSquare() < 0) {
            throw new ValidationException('Площадь должна быть неотрицательной');
        }
    }

    private function checkDuplicates(Country $country, ?string $excludeCode = null): void
    {
        if ($this->repository->existsByShortName($country->getShortName(), $excludeCode)) {
            throw new DuplicateCountryException('краткое название', $country->getShortName());
        }
        if ($this->repository->existsByFullName($country->getFullName(), $excludeCode)) {
            throw new DuplicateCountryException('полное название', $country->getFullName());
        }
        
        if ($excludeCode === null) {
            if ($this->repository->existsByCode($country->getIsoAlpha2())) {
                throw new DuplicateCountryException('код ISO Alpha-2', $country->getIsoAlpha2());
            }
            if ($this->repository->existsByCode($country->getIsoAlpha3())) {
                throw new DuplicateCountryException('код ISO Alpha-3', $country->getIsoAlpha3());
            }
            if ($this->repository->existsByCode($country->getIsoNumeric())) {
                throw new DuplicateCountryException('числовой код ISO', $country->getIsoNumeric());
            }
        }
    }
}
