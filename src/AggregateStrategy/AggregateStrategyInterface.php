<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\AggregateStrategy;

interface AggregateStrategyInterface
{
    public function apply($a, $b);
}
