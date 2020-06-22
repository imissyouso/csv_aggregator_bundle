<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Entity;

class MetricRow
{
    /**
     * @var string
     */
    private $rowName;

    /**
     * @var array
     */
    private $values;

    /**
     * MetricValue constructor.
     *
     * @param string $rowName
     * @param array $values
     */
    public function __construct(string $rowName, array $values)
    {
        $this->rowName = $rowName;
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getRowName(): string
    {
        return $this->rowName;
    }

    /**
     * @param string $rowName
     */
    public function setRowName(string $rowName): void
    {
        $this->rowName = $rowName;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
