<?php
declare(strict_types=1);

namespace imissyouso\CsvAggregatorBundle\Validator;

class StrictDateMetricsCsvValidator implements CsvValidatorInterface
{
    /**
     * @var array
     */
    private $expectedHeader;

    /**
     * DateMetricsCsvValidator constructor.
     * @param array $expectedHeader
     */
    public function __construct(array $expectedHeader)
    {
        $this->expectedHeader = $expectedHeader;
    }

    /**
     * @param array $headerRow
     * @throws \RuntimeException
     */
    public function validateHeader(array $headerRow): void
    {
        if (count(array_diff($headerRow, $this->expectedHeader))) {
            throw new \RuntimeException('Wrong header value!');
        }
    }
}
