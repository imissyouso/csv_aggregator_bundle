<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Entity;

class MetricRow
{
    private $rowName;

    private $values;

    /**
     * MetricValue constructor.
     *
     * @param string $rowName
     */
    public function __construct($rowName, array $values)
    {
        $this->rowName = $rowName;
        $this->values = $values;
    }

    /**
     * @return mixed
     */
    public function getRowName()
    {
        return $this->rowName;
    }

    /**
     * @param mixed $rowName
     */
    public function setRowName($rowName): void
    {
        $this->rowName = $rowName;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param mixed $values
     */
    public function setValues($values): void
    {
        $this->values = $values;
    }
}
