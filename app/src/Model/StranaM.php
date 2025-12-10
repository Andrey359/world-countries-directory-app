<?php

namespace App\Model;

// Russian alias for Country, required by task constraints.
// Internally delegates to Country model and should be kept in sync.

class StranaM
{
    private Country $inner;

    public function __construct()
    {
        $this->inner = new Country();
    }

    public function getShortName(): ?string
    {
        return $this->inner->getShortName();
    }

    public function setShortName(?string $shortName): self
    {
        $this->inner->setShortName($shortName);
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->inner->getFullName();
    }

    public function setFullName(?string $fullName): self
    {
        $this->inner->setFullName($fullName);
        return $this;
    }

    public function getIsoAlpha2(): ?string
    {
        return $this->inner->getIsoAlpha2();
    }

    public function setIsoAlpha2(?string $isoAlpha2): self
    {
        $this->inner->setIsoAlpha2($isoAlpha2);
        return $this;
    }

    public function getIsoAlpha3(): ?string
    {
        return $this->inner->getIsoAlpha3();
    }

    public function setIsoAlpha3(?string $isoAlpha3): self
    {
        $this->inner->setIsoAlpha3($isoAlpha3);
        return $this;
    }

    public function getIsoNumeric(): ?string
    {
        return $this->inner->getIsoNumeric();
    }

    public function setIsoNumeric(?string $isoNumeric): self
    {
        $this->inner->setIsoNumeric($isoNumeric);
        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->inner->getPopulation();
    }

    public function setPopulation(?int $population): self
    {
        $this->inner->setPopulation($population);
        return $this;
    }

    public function getSquare(): ?float
    {
        return $this->inner->getSquare();
    }

    public function setSquare(?float $square): self
    {
        $this->inner->setSquare($square);
        return $this;
    }

    public function toArray(): array
    {
        return $this->inner->toArray();
    }

    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->inner = Country::fromArray($data);
        return $instance;
    }
}
