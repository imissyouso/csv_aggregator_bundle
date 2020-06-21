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
