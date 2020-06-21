<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Factory;

use imissyouso\CsvAggregatorBundle\Aggregator\AbstractMetricsAggregator;

interface CsvMetricsAggregatorFactoryInterface
{
    public function make(): AbstractMetricsAggregator;
}
