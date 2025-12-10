<?php

namespace App\Model;

interface CountryRepository
{
    /**
     * Получить все страны из хранилища
     * @return Country[] Массив объектов Country
     */
    public function findAll(): array;

    /**
     * Найти страну по коду ISO Alpha-2
     * @param string $code Двухбуквенный ISO код
     * @return Country|null Объект Country или null если не найдено
     */
    public function findByAlpha2(string $code): ?Country;

    /**
     * Найти страну по коду ISO Alpha-3
     * @param string $code Трехбуквенный ISO код
     * @return Country|null Объект Country или null если не найдено
     */
    public function findByAlpha3(string $code): ?Country;

    /**
     * Найти страну по числовому коду ISO
     * @param string $code Числовой ISO код
     * @return Country|null Объект Country или null если не найдено
     */
    public function findByNumeric(string $code): ?Country;

    /**
     * Сохранить новую страну в хранилище
     * @param Country $country Объект Country для сохранения
     * @return void
     */
    public function save(Country $country): void;

    /**
     * Обновить существующую страну в хранилище
     * @param string $code Код страны (любого типа)
     * @param Country $country Обновленные данные страны
     * @return void
     */
    public function update(string $code, Country $country): void;

    /**
     * Удалить страну из хранилища по коду
     * @param string $code Код страны (любого типа)
     * @return void
     */
    public function delete(string $code): void;

    /**
     * Проверить существует ли страна с данным коротким названием
     * @param string $shortName Короткое название для проверки
     * @param string|null $excludeCode Код для исключения из проверки (для обновлений)
     * @return bool True если существует
     */
    public function existsByShortName(string $shortName, ?string $excludeCode = null): bool;

    /**
     * Проверить существует ли страна с данным полным названием
     * @param string $fullName Полное название для проверки
     * @param string|null $excludeCode Код для исключения из проверки (для обновлений)
     * @return bool True если существует
     */
    public function existsByFullName(string $fullName, ?string $excludeCode = null): bool;

    /**
     * Проверить существует ли страна с данным кодом
     * @param string $code Код страны для проверки
     * @return bool True если существует
     */
    public function existsByCode(string $code): bool;
}