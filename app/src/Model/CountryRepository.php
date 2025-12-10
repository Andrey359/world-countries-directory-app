<?php

namespace App\Model;

interface CountryRepository
{
    /**
     * Получить все страны из хранилища
     * Возвращает: Массив объектов Country
     */
    public function findAll(): array;

    /**
     * Найти страну по коду ISO Alpha-2
     * Параметр: $code - двухбуквенный ISO код
     * Возвращает: Объект Country или null если не найдено
     */
    public function findByAlpha2(string $code): ?Country;

    /**
     * Найти страну по коду ISO Alpha-3
     * Параметр: $code - трехбуквенный ISO код
     * Возвращает: Объект Country или null если не найдено
     */
    public function findByAlpha3(string $code): ?Country;

    /**
     * Найти страну по числовому коду ISO
     * Параметр: $code - числовой ISO код
     * Возвращает: Объект Country или null если не найдено
     */
    public function findByNumeric(string $code): ?Country;

    /**
     * Сохранить новую страну в хранилище
     * Параметр: $country - объект Country для сохранения
     */
    public function save(Country $country): void;

    /**
     * Обновить существующую страну в хранилище
     * Параметр: $code - код страны (любого типа)
     * Параметр: $country - обновленные данные страны
     */
    public function update(string $code, Country $country): void;

    /**
     * Удалить страну из хранилища по коду
     * Параметр: $code - код страны (любого типа)
     */
    public function delete(string $code): void;

    /**
     * Проверить существует ли страна с данным кратким названием
     * Параметр: $shortName - краткое название для проверки
     * Параметр: $excludeCode - код для исключения из проверки (для обновлений)
     * Возвращает: true если существует
     */
    public function existsByShortName(string $shortName, ?string $excludeCode = null): bool;

    /**
     * Проверить существует ли страна с данным полным названием
     * Параметр: $fullName - полное название для проверки
     * Параметр: $excludeCode - код для исключения из проверки (для обновлений)
     * Возвращает: true если существует
     */
    public function existsByFullName(string $fullName, ?string $excludeCode = null): bool;

    /**
     * Проверить существует ли страна с данным кодом
     * Параметр: $code - код страны для проверки
     * Возвращает: true если существует
     */
    public function existsByCode(string $code): bool;
}
