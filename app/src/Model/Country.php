<?php

namespace App\Model;

class Country
{
    private ?string $shortName = null;
    private ?string $fullName = null;
    private ?string $isoAlpha2 = null;
    private ?string $isoAlpha3 = null;
    private ?string $isoNumeric = null;
    private ?int $population = null;
    private ?float $square = null;

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getIsoAlpha2(): ?string
    {
        return $this->isoAlpha2;
    }

    public function setIsoAlpha2(?string $isoAlpha2): self
    {
        $this->isoAlpha2 = $isoAlpha2;
        return $this;
    }

    public function getIsoAlpha3(): ?string
    {
        return $this->isoAlpha3;
    }

    public function setIsoAlpha3(?string $isoAlpha3): self
    {
        $this->isoAlpha3 = $isoAlpha3;
        return $this;
    }

    public function getIsoNumeric(): ?string
    {
        return $this->isoNumeric;
    }

    public function setIsoNumeric(?string $isoNumeric): self
    {
        $this->isoNumeric = $isoNumeric;
        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): self
    {
        $this->population = $population;
        return $this;
    }

    public function getSquare(): ?float
    {
        return $this->square;
    }

    public function setSquare(?float $square): self
    {
        $this->square = $square;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'shortName' => $this->shortName,
            'fullName' => $this->fullName,
            'isoAlpha2' => $this->isoAlpha2,
            'isoAlpha3' => $this->isoAlpha3,
            'isoNumeric' => $this->isoNumeric,
            'population' => $this->population,
            'square' => $this->square,
        ];
    }

    public static function fromArray(array $data): self
    {
        $country = new self();
        $country->setShortName($data['shortName'] ?? $data['short_name'] ?? null);
        $country->setFullName($data['fullName'] ?? $data['full_name'] ?? null);
        $country->setIsoAlpha2($data['isoAlpha2'] ?? $data['iso_alpha2'] ?? null);
        $country->setIsoAlpha3($data['isoAlpha3'] ?? $data['iso_alpha3'] ?? null);
        $country->setIsoNumeric($data['isoNumeric'] ?? $data['iso_numeric'] ?? null);
        $country->setPopulation(isset($data['population']) ? (int)$data['population'] : null);
        $country->setSquare(isset($data['square']) ? (float)$data['square'] : null);
        return $country;
    }
}
