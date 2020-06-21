<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\RowProcessor;

use imissyouso\CsvAggregatorBundle\Entity\MetricRow;

interface MetricsRowProcessorInterface
{
    public function process(array $header, array $row): MetricRow;
}
