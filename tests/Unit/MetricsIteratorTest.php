<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Tests\Unit;

use ArrayObject;
use imissyouso\CsvAggregatorBundle\Iterator\CsvMetricsRowIterator;
use imissyouso\CsvAggregatorBundle\Entity\MetricRow;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricRowProcessException;
use imissyouso\CsvAggregatorBundle\RowProcessor\MetricsRowProcessorInterface;
use imissyouso\CsvAggregatorBundle\Validator\CsvValidatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MetricsIteratorTest extends TestCase
{
    public function testCsvMetricsRowIterator(): void
    {
        /** @var CsvValidatorInterface|MockObject $csvValidatorMock */
        $csvValidatorMock = $this->createMock(CsvValidatorInterface::class);
        /** @var MetricsRowProcessorInterface|MockObject $csvRowProcessorMock */
        $csvRowProcessorMock = $this->createMock(MetricsRowProcessorInterface::class);

        $sampleData = $this->sampleCsvData();

        $iterator = (new ArrayObject($sampleData))->getIterator();
        $rowIterator = new CsvMetricsRowIterator($iterator, $csvValidatorMock, $csvRowProcessorMock);

        // MetricsRowProcessorInterface::process should be called correctly
        $processCallNum = 0;
        $csvRowProcessorMock->method('process')->willReturnCallback(
            function (array $header, array $row) use ($sampleData, &$processCallNum) {
                if (empty($row)) {
                    $processCallNum++;
                    throw new MetricRowProcessException();
                }

                $this->assertEquals($sampleData[0], $header);
                $this->assertEquals($sampleData[$processCallNum + 1], $row);

                $processCallNum++;

                if ($processCallNum >= count($sampleData) - 1) {
                    $processCallNum = 0;
                }

                return new MetricRow((string)$row[0], array_slice($row, 1));
            }
        );

        // Test a few iterations of the Iterator
        $loopsToTest = random_int(3, 10);

        // Header validator should be called exactly once
        $csvValidatorMock->expects($this->once())->method('validateHeader');

        // Row process should be called ROWS_NUM * ITERATIONS_NUM
        $csvRowProcessorMock->expects($this->exactly((count($sampleData) - 1) * $loopsToTest))->method('process');

        for ($i = 0; $i < $loopsToTest; $i++) {
            $iterationNum = 0;
            foreach ($rowIterator as $key => $metricRow) {
                if (empty($sampleData[$iterationNum + 1])) {
                    $iterationNum++;
                }

                // Check a key too
                $this->assertInstanceOf(MetricRow::class, $metricRow);
                $this->assertEquals($key, $iterationNum);
                $this->assertEquals($metricRow->getRowName(), $sampleData[$key+1][0]);
                $this->assertEquals($metricRow->getValues(), array_slice($sampleData[$key+1], 1));
                $iterationNum++;
            }

            $this->assertEquals($iterationNum, count($sampleData) - 1);
        }
    }

    private function sampleCsvData(): array
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
            [],
            [
                '2015-01-02',
                1,
                1,
            ],
            [1],
            [
                5.4,
                4.3,
                55.1,
                9,
            ],
            [
                '2015-01-05',
                'a',
                'b',
                'c',
            ],
        ];
    }
}
