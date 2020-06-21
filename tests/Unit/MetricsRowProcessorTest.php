<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Tests\Unit;

use imissyouso\CsvAggregatorBundle\RowProcessor\DefaultMetricsRowProcessor;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricRowProcessException;
use PHPUnit\Framework\TestCase;

class MetricsRowProcessorTest extends TestCase
{
    public function testDefaultMetricsRowProcessorWithCorrectInputData(): void
    {
        $defaultRowProcessor = new DefaultMetricsRowProcessor();
        $data = $this->sampleCorrectCsvData();
        $header = $data[0];
        $dataWithoutHeader = array_slice($data, 1);

        foreach ($dataWithoutHeader as $i => $line) {
            $metricRow = $defaultRowProcessor->process($header, $line);
            $this->assertEquals($line[0], $metricRow->getRowName());
            $this->assertEquals(array_slice($line, 1), $metricRow->getValues());
        }
    }

    public function testDefaultMetricsRowProcessorWithCorruptedInputData(): void
    {
        $defaultRowProcessor = new DefaultMetricsRowProcessor();
        $data = $this->sampleCorruptedCsvData();
        $header = $data[0];
        $headerColumns = array_slice($header, 1);
        $inputMetricLines = array_slice($data, 1);

        foreach ($inputMetricLines as $i => $inputMetricLine) {
            $inputMetricLineName = $inputMetricLine[0];
            $inputMetricRow = array_slice($inputMetricLine, 1);

            $outputMetricRow = $defaultRowProcessor->process($header, $inputMetricLine);

            $this->assertEquals($inputMetricLineName, $outputMetricRow->getRowName());

            $outputMetrics = $outputMetricRow->getValues();

            // if source line has extra elem it should be trimmed
            if (count($inputMetricRow) > count($headerColumns)) {
                $this->assertSameSize($outputMetrics, $headerColumns);
            }

            foreach ($headerColumns as $headerColumnIdx => $headerColumn) {
                // if required column does not exist in source line
                if (!isset($inputMetricRow[$headerColumnIdx])) {
                    $this->assertEquals(0, $outputMetrics[$headerColumnIdx]);
                    continue;
                }

                // if source value is not a number
                if (!is_numeric($inputMetricRow[$headerColumnIdx])) {
                    $this->assertEquals(0, $outputMetrics[$headerColumnIdx]);
                } else {
                    $this->assertEquals($inputMetricRow[$headerColumnIdx], $outputMetrics[$headerColumnIdx]);
                }
            }
        }
    }


    public function testDefaultMetricsRowProcessorEmptyRow(): void
    {
        $defaultRowProcessor = new DefaultMetricsRowProcessor();

        $this->expectException(MetricRowProcessException::class);

        $defaultRowProcessor->process([
            'date',
            'A',
            'B',
            'C',
        ], []);
    }

    private function sampleCorrectCsvData(): array
    {
        return [
            [
                'date',
                'A',
                'B',
                'C',
            ],
            [
                '2015-01-01',
                1,
                2,
                3,
            ],
            [
                '2015-01-02',
                3,
                2,
                1,
            ],
        ];
    }

    private function sampleCorruptedCsvData(): array
    {
        return [
            [
                'date',
                'A',
                'B',
                'C',
            ],
            [
                '2015-01-01',
                '1',
                -2,
                3,
            ],
            [
                '2015-01-02',
                -3.2,
                2,
                'abc',
                -4,
            ],
            [
                '2015-01-03',
                3,
                2,
            ],
            [
                3,
                2,
            ],
        ];
    }
}
