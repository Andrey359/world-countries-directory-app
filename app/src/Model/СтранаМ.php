<?php

namespace App\Model;

class СтранаМ
{
    private ?string $короткоеНазвание = null;
    private ?string $полноеНазвание = null;
    private ?string $исоОт 2 = null;
    private ?string $исоОт 3 = null;
    private ?string $исоЧисло = null;
    private ?int $население = null;
    private ?float $площадь = null;

    public function получитьКороткоеНазвание(): ?string
    {
        return $this->короткоеНазвание;
    }

    public function установитьКороткоеНазвание(?string $короткоеНазвание): self
    {
        $this->короткоеНазвание = $короткоеНазвание;
        return $this;
    }

    public function получитьПолноеНазвание(): ?string
    {
        return $this->полноеНазвание;
    }

    public function установитьПолноеНазвание(?string $полноеНазвание): self
    {
        $this->полноеНазвание = $полноеНазвание;
        return $this;
    }

    public function получитьИсоОт 2(): ?string
    {
        return $this->исоОт 2;
    }

    public function установитьИсоОт 2(?string $исоОт 2): self
    {
        $this->исоОт 2 = $исоОт 2;
        return $this;
    }

    public function получитьИсоОт 3(): ?string
    {
        return $this->исоОт 3;
    }

    public function установитьИсоОт 3(?string $исоОт 3): self
    {
        $this->исоОт 3 = $исоОт 3;
        return $this;
    }

    public function получитьИсоЧисло(): ?string
    {
        return $this->исоЧисло;
    }

    public function установитьИсоЧисло(?string $исоЧисло): self
    {
        $this->исоЧисло = $исоЧисло;
        return $this;
    }

    public function получитьНаселение(): ?int
    {
        return $this->население;
    }

    public function установитьНаселение(?int $население): self
    {
        $this->население = $население;
        return $this;
    }

    public function получитьПлощадь(): ?float
    {
        return $this->площадь;
    }

    public function установитьПлощадь(?float $площадь): self
    {
        $this->площадь = $площадь;
        return $this;
    }

    public function в Массив(): array
    {
        return [
            'короткоеНазвание' => $this->короткоеНазвание,
            'полноеНазвание' => $this->полноеНазвание,
            'исоОт 2' => $this->исоОт 2,
            'исоОт 3' => $this->исоОт 3,
            'исоЧисло' => $this->исоЧисло,
            'население' => $this->население,
            'площадь' => $this->площадь,
        ];
    }

    public static function изМассива(array $данные): self
    {
        $страна = new self();
        $страна->setShortName($данные['короткоеНазвание'] ?? $данные['shortName'] ?? null);
        $страна->setFullName($данные['полноеНазвание'] ?? $данные['fullName'] ?? null);
        $страна->setIsoAlpha2($данные['исоОт 2'] ?? $данные['isoAlpha2'] ?? null);
        $страна->setIsoAlpha3($данные['исоОт 3'] ?? $данные['isoAlpha3'] ?? null);
        $страна->setIsoNumeric($данные['исоЧисло'] ?? $данные['isoNumeric'] ?? null);
        $страна->setPopulation($данные['население'] ?? $данные['population'] ?? null);
        $страна->setSquare($данные['площадь'] ?? $данные['square'] ?? null);
        return $страна;
    }

    public function setShortName(?string $shortName): self
    {
        $this->короткоеНазвание = $shortName;
        return $this;
    }

    public function setFullName(?string $fullName): self
    {
        $this->полноеНазвание = $fullName;
        return $this;
    }

    public function setIsoAlpha2(?string $isoAlpha2): self
    {
        $this->исоОт 2 = $isoAlpha2;
        return $this;
    }

    public function setIsoAlpha3(?string $isoAlpha3): self
    {
        $this->исоОт 3 = $isoAlpha3;
        return $this;
    }

    public function setIsoNumeric(?string $isoNumeric): self
    {
        $this->исоЧисло = $isoNumeric;
        return $this;
    }

    public function setPopulation(?int $population): self
    {
        $this->население = $population;
        return $this;
    }

    public function setSquare(?float $square): self
    {
        $this->площадь = $square;
        return $this;
    }

    public function toArray(): array
    {
        return $this->в Массив();
    }

    public static function fromArray(array $data): self
    {
        return self::изМассива($data);
    }
}
