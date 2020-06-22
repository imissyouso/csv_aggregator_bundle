<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\RowProcessor;

use imissyouso\CsvAggregatorBundle\Entity\MetricRow;

class DefaultMetricsRowProcessor implements MetricsRowProcessorInterface
{
    /**
     * @param array $header
     * @param array $row
     * @return MetricRow
     * @throws MetricRowProcessException
     */
    public function process(array $header, array $row): MetricRow
    {
        if (!count($row)) {
            throw new MetricRowProcessException('Cannot process an empty row');
        }

        $rowName = array_shift($row);

        if($rowName){
            $rowName = (string)$rowName;
        }

        array_shift($header);

        $result = [];
        foreach ($header as $i => $metricValue) {
            // set zero if csv is corrupted and value for the requested row does not exist at all
            if (!isset($row[$i])) {
                $result[$i] = 0;
            } else {
                $currentValue = is_string($row[$i]) ? trim($row[$i]) : $row[$i];

                if (!is_numeric($currentValue)) {
                    // ignore not numeric values, set to zero by default
                    $result[$i] = 0;
                } else {
                    $result[$i] = (float) $currentValue;
                }
            }
        }

        return new MetricRow($rowName, $result);
    }
}
