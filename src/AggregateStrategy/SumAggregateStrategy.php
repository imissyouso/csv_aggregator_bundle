<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\AggregateStrategy;

class SumAggregateStrategy implements AggregateStrategyInterface
{
    public function apply($a, $b)
    {
        return $a + $b;
    }
}
