<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\WriteModel;

final class RegularMarketPrice extends AbstractWriteModel
{
    private const SHORT_NAME = 'shortName';
    private const LONG_NAME = 'longName';

    private const METADATA = [
        self::SHORT_NAME => [
            'type' => self::TYPE_STRING,
        ],
        self::LONG_NAME => [
            'type' => self::TYPE_STRING,
        ],
    ];

    protected ?string $fmt = null;

    protected ?float $raw = null;

    public function fromArray(array $data): self
    {
        foreach ($data as $propertyName => $value) {
            switch ($propertyName) {
                case 'fmt':
                case 'raw':
                    $this->$propertyName = $value;

                    break;
            }
        }

        return $this;
    }

    public function getFmt(): ?string
    {
        return $this->fmt;
    }

    public function setFmt(string $fmt): self
    {
        $this->fmt = $fmt;

        return $this;
    }

    public function getRaw(): ?float
    {
        return $this->raw;
    }

    public function setRaw(float $raw): self
    {
        $this->raw = $raw;

        return $this;
    }

    protected function metadata(): array
    {
        return self::METADATA;
    }
}
